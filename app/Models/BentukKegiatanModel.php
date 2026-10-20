<?php

namespace App\Models;

use CodeIgniter\Model;

class BentukKegiatanModel extends Model
{
    protected $table         = 'pemilu_bentuk_kegiatan';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['nama', 'urutan'];
}
