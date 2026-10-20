<?php

namespace App\Models;

use CodeIgniter\Model;

class MutabaahItemModel extends Model
{
    protected $table            = 'mutabaah_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_item', 'kategori', 'tipe_input', 'urutan', 'is_active'];
}
