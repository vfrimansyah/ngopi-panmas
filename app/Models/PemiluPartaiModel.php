<?php

namespace App\Models;

use CodeIgniter\Model;

class PemiluPartaiModel extends Model
{
    protected $table            = 'pemilu_partai';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_partai',
        'lambang_partai',
        'tahun_berdiri',
        'azas_ideologi',
        'alamat',
        'cakupan'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama_partai'   => 'required|min_length[3]|max_length[255]',
        'tahun_berdiri' => 'permit_empty|integer',
        'cakupan'       => 'required|in_list[Nasional,Lokal]'
    ];
}
