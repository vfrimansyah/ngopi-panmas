<?php

namespace App\Models;

use CodeIgniter\Model;

class KegiatanJenisModel extends Model
{
    protected $table         = 'kegiatan_jenis';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'nama_kegiatan',
        'nomor_urut',
    ];

    protected $validationRules = [
        'nama_kegiatan' => 'required|min_length[2]|max_length[150]',
    ];
}
