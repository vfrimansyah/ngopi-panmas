<?php

namespace App\Models;

use CodeIgniter\Model;

class RegionUpaModel extends Model
{
    protected $table            = 'regions_upa';
    protected $primaryKey       = 'kode_upa';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'kode_upa', 'nama_group', 'kelurahan_id', 'kecamatan_id', 'kota_kabupaten_id', 
        'pembimbing_id', 'pembimbing_nama', 'jadwal_rutin', 'tempat_pertemuan', 'is_active', 'keterangan'
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
}
