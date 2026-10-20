<?php

namespace App\Models;

use CodeIgniter\Model;

class PemiluTpsModel extends Model
{
    protected $table            = 'pemilu_tps';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tahun',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'rw',
        'rt',
        'tps',
        'dapil_kota',
        'dapil_prov',
        'dapil_ri',
        'dpt'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'tahun'       => 'required|integer',
        'province_id' => 'required',
        'regency_id'  => 'required',
        'district_id' => 'required',
        'village_id'  => 'required',
        'tps'         => 'required|min_length[1]|max_length[100]',
        'dapil_kota'  => 'required|min_length[1]|max_length[100]',
        'dapil_prov'  => 'required|min_length[1]|max_length[100]',
        'dapil_ri'    => 'required|min_length[1]|max_length[100]',
        'dpt'         => 'permit_empty|integer'
    ];
}
