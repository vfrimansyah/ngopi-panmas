<?php

namespace App\Models;

use CodeIgniter\Model;

class PoliticalProfileModel extends Model
{
    protected $table            = 'member_political_profiles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['member_id', 'status_pemilih_tetap', 'frekuensi_berita_politik', 'harapan_struktur'];
}
