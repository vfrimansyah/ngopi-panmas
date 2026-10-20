<?php

namespace App\Models;

use CodeIgniter\Model;

class DpcPengurusModel extends Model
{
    protected $table         = 'dpc_pengurus';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'nama',
        'institusi',
        'jabatan',
        'bidang_kode',
        'no_hp',
        'sort_order',
        'periode',
    ];

    protected $validationRules = [
        'nama'      => 'required|min_length[2]|max_length[150]',
        'institusi' => 'required',
        'jabatan'   => 'required',
    ];

    public function getList(array $filters = []): array
    {
        $builder = $this->builder();

        if (!empty($filters['institusi'])) {
            $builder->where('institusi', $filters['institusi']);
        }
        if (!empty($filters['jabatan'])) {
            $builder->like('jabatan', $filters['jabatan']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('nama', $s)
                ->orLike('jabatan', $s)
                ->orLike('institusi', $s)
                ->orLike('bidang_kode', $s)
                ->groupEnd();
        }

        return $builder->orderBy('sort_order', 'ASC')
                       ->orderBy('id', 'ASC')
                       ->get()->getResultArray();
    }
}
