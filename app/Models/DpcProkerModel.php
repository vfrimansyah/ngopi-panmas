<?php

namespace App\Models;

use CodeIgniter\Model;

class DpcProkerModel extends Model
{
    protected $table         = 'dpc_proker';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'bidang_kode',
        'nama_program',
        'deskripsi',
        'waktu_frekuensi',
        'kpi_target',
        'pic_nama',
        'target_capaian',
        'realisasi_capaian',
        'status_monev',
        'persentase_progres',
        'is_prioritas',
    ];

    protected $validationRules = [
        'nama_program' => 'required|min_length[3]|max_length[255]',
        'bidang_kode'  => 'required',
    ];

    public function getList(array $filters = []): array
    {
        $builder = $this->builder();

        if (!empty($filters['bidang_kode'])) {
            $builder->where('bidang_kode', $filters['bidang_kode']);
        }
        if (!empty($filters['status_monev'])) {
            $builder->where('status_monev', $filters['status_monev']);
        }
        if (isset($filters['is_prioritas']) && $filters['is_prioritas'] !== '') {
            $builder->where('is_prioritas', (int)$filters['is_prioritas']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('nama_program', $s)
                ->orLike('deskripsi', $s)
                ->orLike('kpi_target', $s)
                ->orLike('pic_nama', $s)
                ->orLike('bidang_kode', $s)
                ->groupEnd();
        }

        return $builder->orderBy('bidang_kode', 'ASC')
                       ->orderBy('id', 'ASC')
                       ->get()->getResultArray();
    }
}
