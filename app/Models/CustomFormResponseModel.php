<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomFormResponseModel extends Model
{
    protected $table            = 'custom_form_responses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'form_id',
        'member_id',
        'user_id',
        'respondent_name',
        'respondent_phone',
        'respondent_email',
        'link_status',
        'ip_address',
        'user_agent',
    ];

    /**
     * Normalize Indonesian phone number into standard variants
     */
    public static function normalizePhone(string $phone): array
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (empty($digits)) {
            return [];
        }

        $variants = [$digits];

        if (str_starts_with($digits, '0')) {
            $variants[] = '62' . substr($digits, 1);
            $variants[] = substr($digits, 1); // 812...
        } elseif (str_starts_with($digits, '62')) {
            $variants[] = '0' . substr($digits, 2);
            $variants[] = substr($digits, 2);
        } elseif (str_starts_with($digits, '8')) {
            $variants[] = '0' . $digits;
            $variants[] = '62' . $digits;
        }

        return array_unique($variants);
    }

    /**
     * Smart match respondent to a member in database
     */
    public function findMatchingMember(array $mappedData): ?array
    {
        $memberModel = new MemberModel();

        // 1. Match by Phone / WhatsApp
        if (!empty($mappedData['handphone'])) {
            $variants = self::normalizePhone($mappedData['handphone']);
            if (!empty($variants)) {
                $builder = $memberModel->where('is_active', 1)->groupStart();
                foreach ($variants as $v) {
                    $builder->orWhere('handphone', $v)
                            ->orLike('handphone', $v, 'both');
                }
                $builder->groupEnd();
                $matched = $builder->first();
                if ($matched) {
                    return $matched;
                }
            }
        }

        // 2. Match by NIK (if column exists)
        if (!empty($mappedData['nik']) && strlen(trim($mappedData['nik'])) >= 10) {
            $cleanNik = preg_replace('/[^0-9]/', '', $mappedData['nik']);
            $db = \Config\Database::connect();
            if ($db->fieldExists('nik', 'members')) {
                $matched = $memberModel->where('is_active', 1)
                    ->groupStart()
                        ->where('nik', trim($mappedData['nik']))
                        ->orWhere('nik', $cleanNik)
                    ->groupEnd()
                    ->first();
                if ($matched) {
                    return $matched;
                }
            }
        }

        // 3. Match by Nama (Full or Partial/First Name) and Kode UPA
        if (!empty($mappedData['nama_lengkap']) && !empty($mappedData['kode_upa'])) {
            $nama = strtolower(trim($mappedData['nama_lengkap']));
            $upaDigits = preg_replace('/[^0-9]/', '', $mappedData['kode_upa']);

            // 3a. Exact full name match in this UPA
            $builder = $memberModel->where('is_active', 1)
                ->where('LOWER(nama_lengkap)', $nama)
                ->groupStart()
                    ->where('kode_upa', trim($mappedData['kode_upa']))
                    ->orLike('kode_upa', $upaDigits)
                ->groupEnd();

            $matched = $builder->first();
            if ($matched) {
                return $matched;
            }

            // 3b. Partial/First name match in this UPA (e.g. "Verdian" in "PM4108" matches "Verdian Frimansyah")
            if (strlen($nama) >= 3) {
                $candidates = $memberModel->where('is_active', 1)
                    ->like('LOWER(nama_lengkap)', $nama)
                    ->groupStart()
                        ->where('kode_upa', trim($mappedData['kode_upa']))
                        ->orLike('kode_upa', $upaDigits)
                    ->groupEnd()
                    ->findAll();

                if (count($candidates) === 1) {
                    return $candidates[0];
                }
            }
        }

        // 4. Match by unique Full Name across all active members
        if (!empty($mappedData['nama_lengkap'])) {
            $nama = strtolower(trim($mappedData['nama_lengkap']));
            if (strlen($nama) >= 6 && str_contains($nama, ' ')) {
                $candidates = $memberModel->where('is_active', 1)
                    ->where('LOWER(nama_lengkap)', $nama)
                    ->findAll();

                if (count($candidates) === 1) {
                    return $candidates[0];
                }
            }
        }

        return null;
    }

    /**
     * Get responses for a form with linked member details
     */
    public function getResponsesByForm(int $formId, ?string $linkStatus = null): array
    {
        $builder = $this->select('custom_form_responses.*, m.nama_lengkap as member_name, m.kode_upa as member_upa, m.status_anggota, u.username')
            ->join('members m', 'm.id = custom_form_responses.member_id', 'left')
            ->join('users u', 'u.id = custom_form_responses.user_id', 'left')
            ->where('custom_form_responses.form_id', $formId);

        if (!empty($linkStatus)) {
            $builder->where('custom_form_responses.link_status', $linkStatus);
        }

        $responses = $builder->orderBy('custom_form_responses.id', 'DESC')->findAll();

        if (!empty($responses)) {
            $respIds = array_column($responses, 'id');
            $db = \Config\Database::connect();
            $answers = $db->table('custom_form_answers a')
                ->select('a.response_id, a.answer_value, f.field_label, f.field_name, f.map_to_member_field')
                ->join('custom_form_fields f', 'f.id = a.field_id')
                ->whereIn('a.response_id', $respIds)
                ->get()
                ->getResultArray();

            $answersByResp = [];
            foreach ($answers as $ans) {
                $answersByResp[$ans['response_id']][] = $ans;
            }

            foreach ($responses as &$r) {
                if (empty($r['respondent_phone']) && isset($answersByResp[$r['id']])) {
                    foreach ($answersByResp[$r['id']] as $ans) {
                        $lbl = strtolower($ans['field_label'] . ' ' . $ans['field_name'] . ' ' . ($ans['map_to_member_field'] ?? ''));
                        if (str_contains($lbl, 'telepon') || str_contains($lbl, 'telp') || str_contains($lbl, 'hp') || str_contains($lbl, 'handphone') || str_contains($lbl, 'wa') || str_contains($lbl, 'whatsapp')) {
                            if (!empty($ans['answer_value'])) {
                                $r['respondent_phone'] = $ans['answer_value'];
                                break;
                            }
                        }
                    }
                }
                if (empty($r['respondent_name']) && isset($answersByResp[$r['id']])) {
                    foreach ($answersByResp[$r['id']] as $ans) {
                        $lbl = strtolower($ans['field_label'] . ' ' . $ans['field_name'] . ' ' . ($ans['map_to_member_field'] ?? ''));
                        if (str_contains($lbl, 'nama')) {
                            if (!empty($ans['answer_value'])) {
                                $r['respondent_name'] = $ans['answer_value'];
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $responses;
    }

    /**
     * Get responses linked to a specific member
     */
    public function getResponsesByMember(int $memberId): array
    {
        return $this->select('custom_form_responses.*, f.title as form_title, f.slug as form_slug')
            ->join('custom_forms f', 'f.id = custom_form_responses.form_id', 'left')
            ->where('custom_form_responses.member_id', $memberId)
            ->orderBy('custom_form_responses.created_at', 'DESC')
            ->findAll();
    }
}
