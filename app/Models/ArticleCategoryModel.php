<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleCategoryModel extends Model
{
    protected $table            = 'article_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_kategori', 'tipe'];
}
