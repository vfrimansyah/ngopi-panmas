<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CustomFormModel;
use App\Models\CustomFormFieldModel;
use App\Models\CustomFormResponseModel;
use App\Models\CustomFormAnswerModel;
use App\Models\MemberModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class CustomFormController extends BaseController
{
    protected CustomFormModel $formModel;
    protected CustomFormFieldModel $fieldModel;
    protected CustomFormResponseModel $responseModel;
    protected CustomFormAnswerModel $answerModel;
    protected MemberModel $memberModel;

    public function __construct()
    {
        $this->formModel     = new CustomFormModel();
        $this->fieldModel    = new CustomFormFieldModel();
        $this->responseModel = new CustomFormResponseModel();
        $this->answerModel   = new CustomFormAnswerModel();
        $this->memberModel   = new MemberModel();
    }

    /**
     * List all custom forms
     */
    public function index()
    {
        $forms = $this->formModel->getFormsWithStats();

        return view('admin/forms/index', [
            'title' => 'Kelola Formulir & Survei',
            'forms' => $forms,
        ]);
    }

    /**
     * Create form view
     */
    public function create()
    {
        return view('admin/forms/form', [
            'title' => 'Buat Formulir Baru',
            'form'  => null,
        ]);
    }

    /**
     * Store new form
     */
    public function store()
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $title = trim($this->request->getPost('title'));
        $slug  = trim($this->request->getPost('slug'));

        if (empty($slug)) {
            $slug = url_title($title, '-', true);
        } else {
            $slug = url_title($slug, '-', true);
        }

        // Ensure unique slug
        $baseSlug = $slug;
        $count = 1;
        while ($this->formModel->where('slug', $slug)->first()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        $closedAt = $this->request->getPost('closed_at');
        $closedAt = !empty($closedAt) ? date('Y-m-d H:i:s', strtotime($closedAt)) : null;

        $formId = $this->formModel->insert([
            'title'            => $title,
            'slug'             => $slug,
            'description'      => $this->request->getPost('description'),
            'is_public'        => $this->request->getPost('is_public') ? 1 : 0,
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
            'closed_at'        => $closedAt,
            'allow_multiple'   => $this->request->getPost('allow_multiple') ? 1 : 0,
            'auto_link_member' => $this->request->getPost('auto_link_member') ? 1 : 0,
            'success_message'  => $this->request->getPost('success_message') ?: 'Terima kasih, data Anda telah berhasil disimpan.',
            'created_by'       => auth()->id(),
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('admin/forms/' . $formId . '/builder'))
            ->with('success', 'Formulir berhasil dibuat! Sekarang Anda dapat menambahkan daftar pertanyaan.');
    }

    /**
     * Edit form metadata
     */
    public function edit(int $id)
    {
        $form = $this->formModel->find($id);
        if (!$form) {
            return redirect()->to(base_url('admin/forms'))->with('error', 'Formulir tidak ditemukan.');
        }

        return view('admin/forms/form', [
            'title' => 'Edit Pengaturan Formulir: ' . $form['title'],
            'form'  => $form,
        ]);
    }

    /**
     * Update form metadata
     */
    public function update(int $id)
    {
        $form = $this->formModel->find($id);
        if (!$form) {
            return redirect()->to(base_url('admin/forms'))->with('error', 'Formulir tidak ditemukan.');
        }

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $title = trim($this->request->getPost('title'));
        $slug  = trim($this->request->getPost('slug'));

        if (empty($slug)) {
            $slug = url_title($title, '-', true);
        } else {
            $slug = url_title($slug, '-', true);
        }

        // Unique check excluding current ID
        $baseSlug = $slug;
        $count = 1;
        while ($this->formModel->where('slug', $slug)->where('id !=', $id)->first()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        $closedAt = $this->request->getPost('closed_at');
        $closedAt = !empty($closedAt) ? date('Y-m-d H:i:s', strtotime($closedAt)) : null;

        $this->formModel->update($id, [
            'title'            => $title,
            'slug'             => $slug,
            'description'      => $this->request->getPost('description'),
            'is_public'        => $this->request->getPost('is_public') ? 1 : 0,
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
            'closed_at'        => $closedAt,
            'allow_multiple'   => $this->request->getPost('allow_multiple') ? 1 : 0,
            'auto_link_member' => $this->request->getPost('auto_link_member') ? 1 : 0,
            'success_message'  => $this->request->getPost('success_message'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('admin/forms'))->with('success', 'Pengaturan formulir berhasil diperbarui.');
    }

    /**
     * AJAX endpoint to check slug availability and suggest unique slug
     */
    public function checkSlug()
    {
        $raw = ($this->request instanceof \CodeIgniter\HTTP\IncomingRequest) 
            ? (string)$this->request->getVar('slug') 
            : (string)($_GET['slug'] ?? '');
        $id  = ($this->request instanceof \CodeIgniter\HTTP\IncomingRequest) 
            ? (int)$this->request->getVar('id') 
            : (int)($_GET['id'] ?? 0);
        $raw = trim($raw);
        $slug = url_title($raw, '-', true);

        if (empty($slug)) {
            return $this->response->setJSON([
                'status'    => 'empty',
                'slug'      => '',
                'available' => true,
                'message'   => '',
            ]);
        }

        $builder = $this->formModel->where('slug', $slug);
        if ($id > 0) {
            $builder->where('id !=', $id);
        }
        $exists = $builder->first();

        if ($exists) {
            $baseSlug = $slug;
            $count = 1;
            while (true) {
                $candidate = $baseSlug . '-' . $count;
                $chk = $this->formModel->where('slug', $candidate);
                if ($id > 0) {
                    $chk->where('id !=', $id);
                }
                if (!$chk->first()) {
                    $slug = $candidate;
                    break;
                }
                $count++;
            }

            return $this->response->setJSON([
                'status'    => 'taken',
                'available' => false,
                'slug'      => $slug,
                'message'   => 'URL ini sudah pernah ada. Otomatis disesuaikan menjadi: ' . $slug,
            ]);
        }

        return $this->response->setJSON([
            'status'    => 'available',
            'available' => true,
            'slug'      => $slug,
            'message'   => 'URL tersedia',
        ]);
    }

    /**
     * Delete form and associated data
     */
    public function delete(int $id)
    {
        $form = $this->formModel->find($id);
        if ($form) {
            $this->formModel->delete($id);
            return redirect()->to(base_url('admin/forms'))->with('success', 'Formulir berhasil dihapus.');
        }
        return redirect()->to(base_url('admin/forms'))->with('error', 'Formulir tidak ditemukan.');
    }

    /**
     * Toggle form active status
     */
    public function toggleStatus(int $id)
    {
        $form = $this->formModel->find($id);
        if ($form) {
            $newStatus = empty($form['is_active']) ? 1 : 0;
            $this->formModel->update($id, ['is_active' => $newStatus]);
            $msg = $newStatus ? 'Formulir sekarang DIBUKA / AKTIF.' : 'Formulir sekarang DITUTUP.';
            return redirect()->back()->with('success', $msg);
        }
        return redirect()->back()->with('error', 'Formulir tidak ditemukan.');
    }

    /**
     * Interactive Form Builder (Questions & Fields)
     */
    public function builder(int $id)
    {
        $form = $this->formModel->getFormWithFields($id);
        if (!$form) {
            return redirect()->to(base_url('admin/forms'))->with('error', 'Formulir tidak ditemukan.');
        }

        return view('admin/forms/builder', [
            'title'  => 'Form Builder: ' . $form['title'],
            'form'   => $form,
            'fields' => $form['fields'] ?? [],
        ]);
    }

    /**
     * Add or update a question/field
     */
    public function saveField(int $formId)
    {
        $form = $this->formModel->find($formId);
        if (!$form) {
            return redirect()->back()->with('error', 'Formulir tidak ditemukan.');
        }

        $fieldId = (int)$this->request->getPost('field_id');
        $label   = trim($this->request->getPost('field_label'));
        $type    = $this->request->getPost('field_type') ?: 'text';
        $name    = trim($this->request->getPost('field_name'));

        if (empty($name)) {
            $name = url_title(strtolower($label), '_', true);
        }

        // Process options for radio/checkbox/select
        $optionsRaw = $this->request->getPost('field_options');
        $fieldOptions = null;
        if (in_array($type, ['radio', 'checkbox', 'select']) && !empty($optionsRaw)) {
            // Split by comma or newline
            $lines = preg_split('/[\r\n,]+/', $optionsRaw);
            $cleanOpts = [];
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed !== '') {
                    $cleanOpts[] = $trimmed;
                }
            }
            $fieldOptions = json_encode($cleanOpts);
        }

        $data = [
            'form_id'             => $formId,
            'field_label'         => $label,
            'field_name'          => $name,
            'field_type'          => $type,
            'field_options'       => $fieldOptions,
            'default_value'       => trim($this->request->getPost('default_value') ?? '') ?: null,
            'help_text'           => $this->request->getPost('help_text'),
            'is_required'         => $this->request->getPost('is_required') ? 1 : 0,
            'map_to_member_field' => $this->request->getPost('map_to_member_field') ?: null,
            'sort_order'          => (int)$this->request->getPost('sort_order') ?: 0,
        ];

        if ($fieldId > 0) {
            $this->fieldModel->update($fieldId, $data);
            $msg = 'Pertanyaan berhasil diperbarui.';
        } else {
            $this->fieldModel->insert($data);
            $msg = 'Pertanyaan baru berhasil ditambahkan.';
        }

        return redirect()->to(base_url('admin/forms/' . $formId . '/builder'))->with('success', $msg);
    }

    /**
     * Delete a question/field
     */
    public function deleteField(int $formId, int $fieldId)
    {
        $field = $this->fieldModel->where('id', $fieldId)->where('form_id', $formId)->first();
        if ($field) {
            $this->fieldModel->delete($fieldId);
            return redirect()->to(base_url('admin/forms/' . $formId . '/builder'))->with('success', 'Pertanyaan berhasil dihapus.');
        }
        return redirect()->to(base_url('admin/forms/' . $formId . '/builder'))->with('error', 'Pertanyaan tidak ditemukan.');
    }

    /**
     * View responses of a form
     */
    public function responses(int $formId)
    {
        $form = $this->formModel->getFormWithFields($formId);
        if (!$form) {
            return redirect()->to(base_url('admin/forms'))->with('error', 'Formulir tidak ditemukan.');
        }

        $statusFilter = $this->request->getGet('status');
        $responses    = $this->responseModel->getResponsesByForm($formId, $statusFilter);

        // Fetch unlinked active members for manual link dropdown
        $allMembers = $this->memberModel
            ->select('id, nama_lengkap, kode_upa, status_anggota')
            ->where('is_active', 1)
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        return view('admin/forms/responses', [
            'title'        => 'Respon: ' . $form['title'],
            'form'         => $form,
            'fields'       => $form['fields'] ?? [],
            'responses'    => $responses,
            'statusFilter' => $statusFilter,
            'allMembers'   => $allMembers,
        ]);
    }

    /**
     * Get response details via JSON (for modal)
     */
    public function responseDetail(int $responseId)
    {
        $response = $this->responseModel->find($responseId);
        if (!$response) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Respon tidak ditemukan']);
        }

        $form = $this->formModel->find($response['form_id']);
        $answers = $this->answerModel->getAnswersByResponse($responseId);

        $member = null;
        if (!empty($response['member_id'])) {
            $member = $this->memberModel->find($response['member_id']);
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'response' => $response,
            'form'     => $form,
            'answers'  => $answers,
            'member'   => $member,
        ]);
    }

    /**
     * Manually link a response to a member
     */
    public function linkResponse()
    {
        $responseId = (int)$this->request->getPost('response_id');
        $memberId   = (int)$this->request->getPost('member_id');

        $response = $this->responseModel->find($responseId);
        if (!$response) {
            return redirect()->back()->with('error', 'Respon tidak ditemukan.');
        }

        if ($memberId > 0) {
            $member = $this->memberModel->find($memberId);
            if (!$member) {
                return redirect()->back()->with('error', 'Anggota tidak ditemukan.');
            }

            $this->responseModel->update($responseId, [
                'member_id'   => $memberId,
                'link_status' => 'manual_linked',
            ]);
            $msg = 'Respon berhasil ditautkan ke anggota: ' . $member['nama_lengkap'];
        } else {
            // Unlink
            $this->responseModel->update($responseId, [
                'member_id'   => null,
                'link_status' => 'unlinked',
            ]);
            $msg = 'Tautan anggota pada respon ini telah dilepas.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Delete a single response and its answers
     */
    public function deleteResponse(int $formId, int $responseId)
    {
        $response = $this->responseModel->where('form_id', $formId)->find($responseId);
        if (!$response) {
            return redirect()->to(base_url("admin/forms/{$formId}/responses"))
                ->with('error', 'Data respon tidak ditemukan.');
        }

        // Delete associated answers
        $this->answerModel->where('response_id', $responseId)->delete();
        // Delete response record
        $this->responseModel->delete($responseId);

        $name = $response['respondent_name'] ?: 'Responden';
        return redirect()->to(base_url("admin/forms/{$formId}/responses"))
            ->with('success', "Data respon dari \"{$name}\" berhasil dihapus.");
    }

    /**
     * Export all responses to Excel
     */
    public function export(int $formId)
    {
        $form = $this->formModel->getFormWithFields($formId);
        if (!$form) {
            return redirect()->to(base_url('admin/forms'))->with('error', 'Formulir tidak ditemukan.');
        }

        $fields = $form['fields'] ?? [];
        $responses = $this->responseModel->getResponsesByForm($formId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Respon Form');

        // Headers
        $headers = [
            'No',
            'Tanggal Submit',
            'Status Tautan',
            'Nama Anggota Tertaut',
            'UPA Anggota',
            'Nama Responden',
            'No HP Responden',
            'Email',
        ];

        foreach ($fields as $f) {
            $headers[] = $f['field_label'];
        }

        // Write Headers
        $colIndex = 1;
        foreach ($headers as $h) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter . '1', $h);
            $colIndex++;
        }

        // Style Header Row
        $highestCol = Coordinate::stringFromColumnIndex(count($headers));
        $headerRange = 'A1:' . $highestCol . '1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE6C687'); // Gold tint

        // Data Rows
        $rowNum = 2;
        foreach ($responses as $idx => $r) {
            // Fetch answers for this response keyed by field_id
            $answers = $this->answerModel->where('response_id', $r['id'])->findAll();
            $answerMap = [];
            foreach ($answers as $a) {
                $val = $a['answer_value'];
                // Check if JSON
                $decoded = json_decode($val, true);
                if (is_array($decoded)) {
                    $val = implode(', ', $decoded);
                }
                $answerMap[$a['field_id']] = $val;
            }

            $statusLabel = match($r['link_status']) {
                'auto_linked'   => 'Auto Linked (Cocok)',
                'manual_linked' => 'Manual Linked',
                default         => 'Belum Tertaut (Tamu)',
            };

            $sheet->setCellValue('A' . $rowNum, $idx + 1);
            $sheet->setCellValue('B' . $rowNum, date('d/m/Y H:i', strtotime($r['created_at'])));
            $sheet->setCellValue('C' . $rowNum, $statusLabel);
            $sheet->setCellValue('D' . $rowNum, $r['member_name'] ?? '-');
            $sheet->setCellValue('E' . $rowNum, $r['member_upa'] ?? '-');
            $sheet->setCellValue('F' . $rowNum, $r['respondent_name'] ?? '-');
            $sheet->setCellValue('G' . $rowNum, $r['respondent_phone'] ?? '-');
            $sheet->setCellValue('H' . $rowNum, $r['respondent_email'] ?? '-');

            $cIdx = 9;
            foreach ($fields as $f) {
                $cLetter = Coordinate::stringFromColumnIndex($cIdx);
                $sheet->setCellValue($cLetter . $rowNum, $answerMap[$f['id']] ?? '');
                $cIdx++;
            }

            $rowNum++;
        }

        // Auto-fit column widths
        $totalCols = count($headers);
        for ($col = 1; $col <= $totalCols; $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $filename = 'Respon_' . url_title($form['slug'], '_') . '_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
