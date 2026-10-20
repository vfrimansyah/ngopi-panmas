<?php

namespace App\Models;

use CodeIgniter\Model;

class DiscussionModel extends Model
{
    protected $table            = 'discussions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'judul',
        'slug',
        'kategori_id',
        'konten',
        'author_member_id',
        'views_count',
        'upvotes_count',
        'replies_count',
        'status',
        'is_pinned',
    ];
    protected $useTimestamps    = true;
}
