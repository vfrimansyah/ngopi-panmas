<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FinTransaksiModel;
use App\Models\FinRekeningModel;
use App\Models\FinAccountModel;

class KeuanganDashboardController extends BaseController
{
    public function index()
    {
        $transaksiModel = new FinTransaksiModel();
        $rekeningModel  = new FinRekeningModel();

        $bulanIni = (int) date('n');
        $tahunIni = (int) date('Y');

        $pemasukanBulanIni   = $transaksiModel->getTotalPemasukan($bulanIni, $tahunIni);
        $pengeluaranBulanIni = $transaksiModel->getTotalPengeluaran($bulanIni, $tahunIni);
        $pemasukanTotal      = $transaksiModel->getTotalPemasukan();
        $pengeluaranTotal    = $transaksiModel->getTotalPengeluaran();

        // Rekening & saldo
        $rekenings = $rekeningModel->getWithAkun();
        foreach ($rekenings as &$r) {
            $r['saldo_berjalan'] = $rekeningModel->getSaldoBerjalan($r['id']);
        }
        $totalSaldo = array_sum(array_column($rekenings, 'saldo_berjalan'));

        // Grafik 6 bulan
        $grafik = $transaksiModel->getGrafik6Bulan();

        // 5 transaksi terakhir
        $transaksiTerakhir = $transaksiModel->getList(['status' => 'posted']);
        $transaksiTerakhir = array_slice($transaksiTerakhir, 0, 5);

        return view('admin/keuangan/dashboard/index', [
            'title'                => 'Dashboard Keuangan',
            'pemasukanBulanIni'    => $pemasukanBulanIni,
            'pengeluaranBulanIni'  => $pengeluaranBulanIni,
            'labaRugiBulanIni'     => $pemasukanBulanIni - $pengeluaranBulanIni,
            'totalSaldo'           => $totalSaldo,
            'rekenings'            => $rekenings,
            'grafik'               => $grafik,
            'transaksiTerakhir'    => $transaksiTerakhir,
            'bulanLabel'           => date('F Y'),
        ]);
    }
}
