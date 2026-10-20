<?php

namespace App\Controllers;

use App\Models\CustomFormModel;
use App\Models\CustomFormFieldModel;
use App\Models\CustomFormResponseModel;
use App\Models\CustomFormAnswerModel;
use App\Models\MemberModel;

class PublicFormController extends BaseController
{
    protected CustomFormModel $formModel;
    protected CustomFormFieldModel $fieldModel;
    protected CustomFormResponseModel $responseModel;
    protected CustomFormAnswerModel $answerModel;

    public function __construct()
    {
        $this->formModel     = new CustomFormModel();
        $this->fieldModel    = new CustomFormFieldModel();
        $this->responseModel = new CustomFormResponseModel();
        $this->answerModel   = new CustomFormAnswerModel();
    }

    /**
     * Display public dynamic form
     */
    public function show(string $slug)
    {
        $form = $this->formModel->getFormWithFields($slug);

        if (!$form) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Formulir tidak ditemukan.');
        }

        $isExpired = (!empty($form['closed_at']) && strtotime($form['closed_at']) <= time());

        if (empty($form['is_active']) || $isExpired) {
            return view('public/forms/closed', [
                'title'     => $form['title'] . ' — Formulir Ditutup',
                'form'      => $form,
                'isExpired' => $isExpired,
            ]);
        }

        // Pre-fill if user is logged in and linked to a member
        $prefill = [];
        $isLoggedIn = auth()->loggedIn();
        $currentMember = null;

        if ($isLoggedIn) {
            $user = auth()->user();
            $memberModel = new MemberModel();
            $currentMember = $memberModel->where('user_id', $user->id)->first();

            if ($currentMember) {
                $prefill['nama_lengkap']   = $currentMember['nama_lengkap'];
                $prefill['handphone']      = $currentMember['handphone'];
                $prefill['tanggal_lahir']  = $currentMember['tanggal_lahir'];
                $prefill['alamat_lengkap'] = $currentMember['alamat_lengkap'];
                $prefill['kode_upa']       = $currentMember['kode_upa'];
                $prefill['nik']            = $currentMember['nik'] ?? '';
            }
        }

        return view('public/forms/show', [
            'title'         => $form['title'],
            'form'          => $form,
            'fields'        => $form['fields'] ?? [],
            'prefill'       => $prefill,
            'isLoggedIn'    => $isLoggedIn,
            'currentMember' => $currentMember,
        ]);
    }

    /**
     * Handle submission of the dynamic form
     */
    public function submit(string $slug)
    {
        $form = $this->formModel->getFormWithFields($slug);
        $isExpired = (!empty($form['closed_at']) && strtotime($form['closed_at']) <= time());

        if (!$form || empty($form['is_active']) || $isExpired) {
            return redirect()->back()->with('error', 'Formulir tidak tersedia atau batas akhir pengisian telah lewat.');
        }

        $fields = $form['fields'] ?? [];
        $answers = [];
        $mappedData = [];
        $errors = [];

        foreach ($fields as $f) {
            $val = $this->request->getPost($f['field_name']);

            // Validate required
            if (!empty($f['is_required'])) {
                if ($val === null || $val === '' || (is_array($val) && empty($val))) {
                    $errors[] = 'Kolom "' . $f['field_label'] . '" wajib diisi.';
                }
            }

            // If array (checkboxes), encode JSON
            $storedVal = is_array($val) ? json_encode($val) : ($val !== null ? trim($val) : null);
            $answers[$f['id']] = $storedVal;

            // Collect mapped values for member matching
            if (!empty($f['map_to_member_field']) && !empty($storedVal)) {
                $mappedData[$f['map_to_member_field']] = $storedVal;
            }

            // SMART AUTO-DETECTION: Even if admin didn't explicitly map the field in builder!
            if (!empty($storedVal) && is_string($storedVal)) {
                $labelLower = strtolower($f['field_label']);
                $nameLower  = strtolower($f['field_name']);

                // Phone auto-detection
                if (empty($mappedData['handphone'])) {
                    if (str_contains($labelLower, 'telepon') || str_contains($labelLower, 'telp') ||
                        str_contains($labelLower, 'hp') || str_contains($labelLower, 'handphone') ||
                        str_contains($labelLower, 'whatsapp') || str_contains($labelLower, 'wa') ||
                        str_contains($nameLower, 'telepon') || str_contains($nameLower, 'telp') ||
                        str_contains($nameLower, 'hp') || str_contains($nameLower, 'handphone') ||
                        str_contains($nameLower, 'notelepon') || str_contains($nameLower, 'whatsapp') ||
                        str_contains($nameLower, 'wa')) {
                        $mappedData['handphone'] = $storedVal;
                    }
                }

                // Name auto-detection
                if (empty($mappedData['nama_lengkap'])) {
                    if (str_contains($labelLower, 'nama') || str_contains($nameLower, 'nama')) {
                        $mappedData['nama_lengkap'] = $storedVal;
                    }
                }

                // NIK auto-detection
                if (empty($mappedData['nik'])) {
                    if (str_contains($labelLower, 'nik') || str_contains($labelLower, 'ktp') ||
                        str_contains($nameLower, 'nik') || str_contains($nameLower, 'ktp')) {
                        $mappedData['nik'] = $storedVal;
                    }
                }

                // UPA auto-detection
                if (empty($mappedData['kode_upa'])) {
                    if (str_contains($labelLower, 'upa') || str_contains($labelLower, 'kode grup') ||
                        str_contains($labelLower, 'grup') || str_contains($nameLower, 'upa') ||
                        str_contains($nameLower, 'grup')) {
                        $mappedData['kode_upa'] = $storedVal;
                    }
                }
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        // Determine respondent details & member linking
        $userId          = auth()->loggedIn() ? auth()->id() : null;
        $memberId        = null;
        $linkStatus      = 'unlinked';
        $respondentName  = $mappedData['nama_lengkap'] ?? $this->request->getPost('nama') ?? null;
        $respondentPhone = $mappedData['handphone'] ?? $this->request->getPost('no_hp') ?? $this->request->getPost('notelepon') ?? $this->request->getPost('telepon') ?? null;
        $respondentEmail = $this->request->getPost('email') ?? null;

        // 1. If user is logged in and linked to member
        if ($userId) {
            $memberModel = new MemberModel();
            $userMember = $memberModel->where('user_id', $userId)->first();
            if ($userMember) {
                $memberId   = $userMember['id'];
                $linkStatus = 'auto_linked';
                if (empty($respondentName)) {
                    $respondentName = $userMember['nama_lengkap'];
                }
                if (empty($respondentPhone)) {
                    $respondentPhone = $userMember['handphone'];
                }
            }
        }

        // 2. If guest or unlinked user, run smart matching algorithm
        if (!$memberId && !empty($form['auto_link_member'])) {
            $matched = $this->responseModel->findMatchingMember($mappedData);
            if ($matched) {
                $memberId   = $matched['id'];
                $linkStatus = 'auto_linked';
            }
        }

        // Insert Response
        $responseId = $this->responseModel->insert([
            'form_id'          => $form['id'],
            'member_id'        => $memberId,
            'user_id'          => $userId,
            'respondent_name'  => $respondentName,
            'respondent_phone' => $respondentPhone,
            'respondent_email' => $respondentEmail,
            'link_status'      => $linkStatus,
            'ip_address'       => $this->request->getIPAddress(),
            'user_agent'       => substr($this->request->getUserAgent()->getAgentString(), 0, 255),
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        // Insert Answers
        foreach ($answers as $fieldId => $answerVal) {
            $this->answerModel->insert([
                'response_id'  => $responseId,
                'field_id'     => $fieldId,
                'answer_value' => $answerVal,
            ]);
        }

        return redirect()->to(base_url('form/' . $slug . '/success'))->with('success_response_id', $responseId);
    }

    /**
     * Form submission thank you page
     */
    public function success(string $slug)
    {
        $form = $this->formModel->where('slug', $slug)->first();
        if (!$form) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Formulir tidak ditemukan.');
        }

        return view('public/forms/success', [
            'title' => 'Terima Kasih — ' . $form['title'],
            'form'  => $form,
        ]);
    }
}
