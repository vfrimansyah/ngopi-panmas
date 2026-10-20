<?php

namespace App\Models;

use CodeIgniter\Model;

class DpcBidangModel extends Model
{
    protected $table         = 'dpc_bidang';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'kode',
        'nama_bidang',
        'pic_nama',
        'fokus',
        'link_match',
        'monev_keterangan',
        'reward_keterangan',
        'sort_order',
    ];

    public function getWithStats(): array
    {
        $db = \Config\Database::connect();
        $bidangs = $this->orderBy('sort_order', 'ASC')->findAll();

        foreach ($bidangs as &$b) {
            $totalProker = $db->table('dpc_proker')->where('bidang_kode', $b['kode'])->countAllResults();
            $avgProgres = $db->table('dpc_proker')->where('bidang_kode', $b['kode'])->selectAvg('persentase_progres')->get()->getRow()->persentase_progres ?? 0;
            $totalAnggaran = $db->table('dpc_rab')->where('bidang_kode', $b['kode'])->selectSum('anggaran')->get()->getRow()->anggaran ?? 0;
            $totalRealisasi = $db->table('dpc_rab')->where('bidang_kode', $b['kode'])->selectSum('realisasi_anggaran')->get()->getRow()->realisasi_anggaran ?? 0;

            $b['total_proker']     = $totalProker;
            $b['avg_progres']      = round((float)$avgProgres, 1);
            $b['total_anggaran']   = (float)$totalAnggaran;
            $b['total_realisasi']  = (float)$totalRealisasi;
        }

        return $bidangs;
    }
}
