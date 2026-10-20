<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FinAccountModel;
use App\Models\FinJurnalDetailModel;

class KeuanganLaporanController extends BaseController
{
    public function index()
    {
        $bulan = (int) ($this->request->getGet('bulan') ?? date('n'));
        $tahun = (int) ($this->request->getGet('tahun') ?? date('Y'));
        $dari  = $this->request->getGet('dari')   ?? date('Y-m-01');
        $sampai= $this->request->getGet('sampai') ?? date('Y-m-t');

        $db = \Config\Database::connect();

        // ── Pendapatan ────────────────────────────────────────────────────
        $pendapatanAkuns = (new FinAccountModel())->getPendapatan();
        $pendapatanRows  = [];
        $totalPendapatan = 0;

        foreach ($pendapatanAkuns as $akun) {
            // Kredit side dari jurnal detail
            $row = $db->query("
                SELECT COALESCE(SUM(jd.kredit),0) - COALESCE(SUM(jd.debit),0) AS saldo
                FROM fin_jurnal_detail jd
                JOIN fin_jurnal j ON j.id = jd.jurnal_id
                WHERE jd.akun_id = ? AND j.tanggal BETWEEN ? AND ?
            ", [$akun['id'], $dari, $sampai])->getRowArray();

            $saldo = (float) ($row['saldo'] ?? 0);
            if ($saldo != 0) {
                $pendapatanRows[]  = array_merge($akun, ['saldo' => $saldo]);
                $totalPendapatan  += $saldo;
            }
        }

        // ── Beban ─────────────────────────────────────────────────────────
        $bebanAkuns = (new FinAccountModel())->getBeban();
        $bebanRows  = [];
        $totalBeban = 0;

        foreach ($bebanAkuns as $akun) {
            $row = $db->query("
                SELECT COALESCE(SUM(jd.debit),0) - COALESCE(SUM(jd.kredit),0) AS saldo
                FROM fin_jurnal_detail jd
                JOIN fin_jurnal j ON j.id = jd.jurnal_id
                WHERE jd.akun_id = ? AND j.tanggal BETWEEN ? AND ?
            ", [$akun['id'], $dari, $sampai])->getRowArray();

            $saldo = (float) ($row['saldo'] ?? 0);
            if ($saldo != 0) {
                $bebanRows[]  = array_merge($akun, ['saldo' => $saldo]);
                $totalBeban  += $saldo;
            }
        }

        $labaRugi = $totalPendapatan - $totalBeban;

        // Daftar tahun tersedia
        $tahunList = [];
        for ($y = date('Y'); $y >= date('Y') - 5; $y--) $tahunList[] = $y;

        return view('admin/keuangan/laporan/laba_rugi', [
            'title'           => 'Laporan Laba Rugi',
            'dari'            => $dari,
            'sampai'          => $sampai,
            'bulan'           => $bulan,
            'tahun'           => $tahun,
            'pendapatanRows'  => $pendapatanRows,
            'totalPendapatan' => $totalPendapatan,
            'bebanRows'       => $bebanRows,
            'totalBeban'      => $totalBeban,
            'labaRugi'        => $labaRugi,
            'tahunList'       => $tahunList,
        ]);
    }
}
