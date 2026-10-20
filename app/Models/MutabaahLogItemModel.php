<?php

namespace App\Models;

use CodeIgniter\Model;

class MutabaahLogItemModel extends Model
{
    protected $table            = 'mutabaah_log_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['mutabaah_log_id', 'mutabaah_item_id', 'nilai'];
}
