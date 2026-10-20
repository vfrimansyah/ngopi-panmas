<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingMasterModel extends Model
{
    protected $table         = 'booking_ruangan_master';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'nama_ruangan',
        'nomor_urut',
        'kapasitas',
        'keterangan',
    ];

    protected $validationRules = [
        'nama_ruangan' => 'required|min_length[2]|max_length[150]',
    ];
}
