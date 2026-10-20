<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterDpraModel extends Model
{
    protected $table         = 'master_dpra';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'village_id',
        'nama_dpra',
        'kode_dpra',
        'keterangan',
        'status',
    ];

    protected $validationRules = [
        'village_id' => 'required',
        'nama_dpra'  => 'required|min_length[3]|max_length[150]',
    ];

    /**
     * Get DPRa list joined with reg_villages
     */
    public function getList(array $filters = []): array
    {
        $builder = $this->db->table('master_dpra d')
            ->select('d.*, v.name AS nama_kelurahan, dist.name AS nama_kecamatan')
            ->join('reg_villages v', 'v.id = d.village_id', 'left')
            ->join('reg_districts dist', 'dist.id = v.district_id', 'left');

        if (!empty($filters['status'])) {
            $builder->where('d.status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('d.nama_dpra', $s)
                ->orLike('d.kode_dpra', $s)
                ->orLike('v.name', $s)
                ->groupEnd();
        }

        return $builder->orderBy('d.status', 'ASC')
                       ->orderBy('d.nama_dpra', 'ASC')
                       ->get()->getResultArray();
    }
}
