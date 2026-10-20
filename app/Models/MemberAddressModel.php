<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberAddressModel extends Model
{
    protected $table            = 'member_addresses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'member_id',
        'label',
        'alamat_lengkap',
        'rt',
        'rw',
        'kota_kabupaten_id',
        'kecamatan_id',
        'kelurahan_id'
    ];
}
