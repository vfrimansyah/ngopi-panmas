<?php

namespace App\Models;

use CodeIgniter\Model;

class ContributionModel extends Model
{
    protected $table            = 'member_contributions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['member_id', 'bersedia_sukseskan_program', 'catatan_kesediaan', 'bidang_kontribusi', 'jaringan_komunitas'];
}
