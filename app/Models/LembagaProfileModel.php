<?php

namespace App\Models;

use CodeIgniter\Model;

class LembagaProfileModel extends Model
{
    protected $table            = 'lembaga_profiles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'member_id',
        'nama_lembaga',
        'jenis_lembaga',
        'kategori_bidang',
        'alamat_lengkap',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'kelurahan',
        'kecamatan',
        'kota',
        'kode_pos',
        'no_kontak',
        'email',
        'website',
        'nama_ketua',
        'nik_ketua',
        'hp_ketua',
        'npwp_ketua',
        'nama_sekretaris',
        'nama_bendahara',
        'no_akta_notaris',
        'nama_notaris',
        'tgl_akta',
        'no_sk_kemenkumham',
        'npwp_lembaga',
        'nib',
        'nama_bank',
        'no_rekening',
        'atas_nama_rekening',
        'file_logo',
        'file_ktp_ketua',
        'file_npwp_ketua',
        'file_npwp_lembaga',
        'file_akta',
        'file_sk_kemenkumham',
        'file_nib',
        'file_buku_rekening',
        'file_domisili',
        'file_susunan_pengurus',
        'authorized_members',
        'is_public',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get lembaga list for a user (or all if admin)
     * Includes:
     * - Lembaga created by this user/member (Owner)
     * - Lembaga where member is in authorized_members
     * - Lembaga marked as is_public (Community/Shared)
     */
    public function getByUser(?int $userId, ?int $memberId = null, bool $isAdmin = false)
    {
        $builder = $this->builder();
        if (!$isAdmin) {
            $builder->groupStart();
            if ($userId) {
                $builder->where('user_id', $userId);
            }
            if ($memberId) {
                $builder->orWhere('member_id', $memberId);
                // Exact string match inside JSON array e.g. ["2","5"]
                $builder->orLike('authorized_members', '"' . $memberId . '"');
            }
            $builder->orWhere('is_public', 1);
            $builder->groupEnd();
        }
        return $builder->orderBy('nama_lembaga', 'ASC')->get()->getResultArray();
    }

    /**
     * Check if user is the original creator / owner or admin
     */
    public function isOwner(array $lembaga, ?int $userId, ?int $memberId = null, bool $isAdmin = false): bool
    {
        if ($isAdmin) {
            return true;
        }
        if ($userId && !empty($lembaga['user_id']) && (int)$lembaga['user_id'] === (int)$userId) {
            return true;
        }
        if ($memberId && !empty($lembaga['member_id']) && (int)$lembaga['member_id'] === (int)$memberId) {
            return true;
        }
        return false;
    }

    /**
     * Check if user is authorized to create proposals with this lembaga
     */
    public function isAuthorized(array $lembaga, ?int $userId, ?int $memberId = null, bool $isAdmin = false): bool
    {
        if ($this->isOwner($lembaga, $userId, $memberId, $isAdmin)) {
            return true;
        }
        if (!empty($lembaga['is_public']) && (int)$lembaga['is_public'] === 1) {
            return true;
        }
        if ($memberId && !empty($lembaga['authorized_members'])) {
            $list = json_decode($lembaga['authorized_members'], true) ?: [];
            if (in_array((string)$memberId, array_map('strval', $list), true)) {
                return true;
            }
        }
        return false;
    }
}

