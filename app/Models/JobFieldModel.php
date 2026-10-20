<?php

namespace App\Models;

use CodeIgniter\Model;

class JobFieldModel extends Model
{
    protected $table            = 'job_fields';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name'];
}
