<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $table            = 'members';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'account_status',
        'father_member_id',
        'mother_member_id',
        'spouse_member_id',
        'spouse_pairing_status',
        'nama_lengkap',
        'email',
        'handphone',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'usia_range',
        'status_anggota',
        'kode_upa',
        'status_perkawinan',
        'alamat_lengkap',
        'rt',
        'rw',
        'kelurahan_id',
        'kecamatan_id',
        'kota_kabupaten_id',
        'pekerjaan',
        'nama_instansi',
        'alamat_kerja',
        'bidang_pekerjaan',
        'keahlian',
        'pendapatan_range',
        'pendidikan_terakhir',
        'pendidikan_saat_ini',
        'sekolah_universitas',
        'jurusan',
        'judul_skripsi',
        'status_kerja',
        'mengikuti_mentoring',
        'hobi',
        'is_active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'nama_lengkap' => 'required|max_length[255]',
        'jenis_kelamin' => 'permit_empty|in_list[L,P]',
        'account_status' => 'required|in_list[no_account,invited,active]',
        'spouse_pairing_status' => 'required|in_list[unmatched,suggested,confirmed,no_pair]',
    ];

    protected $validationMessages   = [];
    protected $skipValidation       = false;
}
