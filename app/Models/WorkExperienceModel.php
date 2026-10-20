<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkExperienceModel extends Model
{
    protected $table            = 'member_work_experiences';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['member_id', 'jabatan', 'nama_perusahaan', 'bidang_pekerjaan_id', 'pendapatan_range', 'alamat_kerja', 'rentang_tahun'];
}
