<?php

namespace App\Models;

use CodeIgniter\Model;

class FamilyMatchCandidateModel extends Model
{
    protected $table            = 'family_match_candidates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'member_a_id',
        'member_b_id',
        'relation_type',
        'score',
        'status',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
