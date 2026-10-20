<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleModel extends Model
{
    protected $table            = 'articles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'judul',
        'slug',
        'tipe',
        'kategori_id',
        'konten',
        'foto_cover',
        'author_member_id',
        'created_by',
        'status',
        'views_count',
        'published_at',
    ];
    protected $useTimestamps = true;
}
