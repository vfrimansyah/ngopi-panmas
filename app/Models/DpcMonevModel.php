<?php

namespace App\Models;

use CodeIgniter\Model;

class DpcMonevModel extends Model
{
    protected $table         = 'dpc_monev_logs';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'proker_id',
        'periode_evaluasi',
        'tanggal_evaluasi',
        'persentase_progres',
        'capaian_kpi',
        'kendala',
        'tindak_lanjut',
        'evaluator_nama',
    ];

    protected $validationRules = [
        'proker_id'        => 'required|is_natural_no_zero',
        'periode_evaluasi' => 'required',
        'tanggal_evaluasi' => 'required|valid_date',
    ];

    public function getLogsByProker(int $prokerId): array
    {
        return $this->where('proker_id', $prokerId)
                    ->orderBy('tanggal_evaluasi', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }

    public function getRecentLogs(int $limit = 10): array
    {
        return $this->builder('dpc_monev_logs m')
            ->select('m.*, p.nama_program, p.bidang_kode, p.pic_nama')
            ->join('dpc_proker p', 'p.id = m.proker_id', 'left')
            ->orderBy('m.tanggal_evaluasi', 'DESC')
            ->orderBy('m.id', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }
}
