<?php

namespace App\Models;

use CodeIgniter\Model;

class DonationPreferenceModel extends Model
{
    protected $table            = 'member_donation_preferences';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'member_id',
        'kepatuhan_iuran',
        'kendala_iuran',
        'kesediaan_donasi',
        'bentuk_donasi_lain',
        'metode_donasi',
        'donasi_anonim',
    ];
}
