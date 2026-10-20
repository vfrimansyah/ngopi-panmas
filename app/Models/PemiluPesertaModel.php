<?php

namespace App\Models;

use CodeIgniter\Model;

class PemiluPesertaModel extends Model
{
    protected $table            = 'pemilu_peserta';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tahun',
        'partai_id',
        'no_urut',
        'ketua_partai'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'tahun'        => 'required|integer',
        'partai_id'    => 'required|integer',
        'no_urut'      => 'required|integer',
        'ketua_partai' => 'required|min_length[3]|max_length[255]'
    ];
}
