<?php

namespace App\Models;

use CodeIgniter\Model;

class DiscussionReplyModel extends Model
{
    protected $table            = 'discussion_replies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'discussion_id',
        'parent_reply_id',
        'member_id',
        'konten',
        'upvotes_count',
        'is_best_answer',
    ];
    protected $useTimestamps    = true;
}
