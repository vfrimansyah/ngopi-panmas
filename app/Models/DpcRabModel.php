<?php

namespace App\Models;

use CodeIgniter\Model;

class DpcRabModel extends Model
{
    protected $table         = 'dpc_rab';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'bidang_kode',
        'nama_program',
        'waktu_frekuensi',
        'anggaran',
        'realisasi_anggaran',
        'keterangan',
    ];

    protected $validationRules = [
        'nama_program' => 'required|min_length[3]|max_length[255]',
        'bidang_kode'  => 'required',
        'anggaran'     => 'required|numeric',
    ];

    public function getList(array $filters = []): array
    {
        $builder = $this->builder();

        if (!empty($filters['bidang_kode'])) {
            $builder->where('bidang_kode', $filters['bidang_kode']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('nama_program', $s)
                ->orLike('bidang_kode', $s)
                ->orLike('keterangan', $s)
                ->groupEnd();
        }

        return $builder->orderBy('bidang_kode', 'ASC')
                       ->orderBy('id', 'ASC')
                       ->get()->getResultArray();
    }

    public function getSummaryByBidang(): array
    {
        return $this->builder()
            ->select('bidang_kode, SUM(anggaran) as total_anggaran, SUM(realisasi_anggaran) as total_realisasi, COUNT(id) as total_item')
            ->groupBy('bidang_kode')
            ->orderBy('total_anggaran', 'DESC')
            ->get()->getResultArray();
    }
}
