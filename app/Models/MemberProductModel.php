<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberProductModel extends Model
{
    protected $table            = 'member_products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'member_id',
        'kategori_id',
        'nama_produk',
        'deskripsi',
        'harga',
        'satuan',
        'foto_urls',
        'kontak_whatsapp',
        'link_eksternal',
        'status',
    ];
    protected $useTimestamps = true;
}
