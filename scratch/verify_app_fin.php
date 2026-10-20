<?php
/**
 * Test Controller Queries and Output
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';

define('FCPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
chdir(FCPATH);
require FCPATH . '../app/Config/Paths.php';
$paths = new \Config\Paths();
require $paths->systemDirectory . '/Boot.php';
\CodeIgniter\Boot::preload($paths);
\CodeIgniter\Boot::bootTest($paths);

echo "=== TESTING MODELS & CONTROLLERS ===\n";

$transaksiModel = new \App\Models\FinTransaksiModel();
$rekeningModel  = new \App\Models\FinRekeningModel();
$accountModel   = new \App\Models\FinAccountModel();
$jurnalModel    = new \App\Models\FinJurnalModel();

// 1. Dashboard metrics
echo "1. Dashboard Metrics:\n";
$bulanIni = (int) date('n');
$tahunIni = (int) date('Y');
$pemasukanBulanIni   = $transaksiModel->getTotalPemasukan($bulanIni, $tahunIni);
$pengeluaranBulanIni = $transaksiModel->getTotalPengeluaran($bulanIni, $tahunIni);
$pemasukanTotal      = $transaksiModel->getTotalPemasukan();
$pengeluaranTotal    = $transaksiModel->getTotalPengeluaran();
echo "  Pemasukan Total  : Rp " . number_format($pemasukanTotal, 2, ',', '.') . "\n";
echo "  Pengeluaran Total: Rp " . number_format($pengeluaranTotal, 2, ',', '.') . "\n";
echo "  Pemasukan Bulan Ini: Rp " . number_format($pemasukanBulanIni, 2, ',', '.') . "\n";

// 2. Rekening & Saldo
echo "\n2. Rekening & Saldo Berjalan:\n";
$rekenings = $rekeningModel->getWithAkun();
foreach ($rekenings as $r) {
    $saldo = $rekeningModel->getSaldoBerjalan($r['id']);
    echo "  [{$r['id']}] {$r['nama_rekening']} ({$r['bank']}): Saldo = Rp " . number_format($saldo, 2, ',', '.') . "\n";
}
$totalSaldo = $rekeningModel->getTotalSaldo();
echo "  Total Saldo: Rp " . number_format($totalSaldo, 2, ',', '.') . "\n";

// 3. Transactions list
echo "\n3. Transactions Sample (first 5):\n";
$list = $transaksiModel->getList(['limit' => 5]);
echo "  Total records in getList: " . count($list) . "\n";
foreach (array_slice($list, 0, 5) as $t) {
    echo "  {$t['nomor_transaksi']} | {$t['tanggal']} | {$t['tipe']} | {$t['kategori']} | Rp " . number_format($t['jumlah'], 2, ',', '.') . " | {$t['nama_rekening']} | {$t['deskripsi']}\n";
}

// 4. Jurnal list
echo "\n4. Jurnal Sample (first 5):\n";
$jList = $jurnalModel->getList(['limit' => 5]);
echo "  Total journals in getList: " . count($jList) . "\n";
foreach (array_slice($jList, 0, 5) as $j) {
    echo "  {$j['nomor_jurnal']} | {$j['tanggal']} | {$j['deskripsi']}\n";
}

// 5. Laba Rugi
echo "\n5. Laporan Laba Rugi (Pendapatan & Beban):\n";
$db = \Config\Database::connect();
$pendapatanAkuns = $accountModel->getPendapatan();
$totPendapatan = 0;
foreach ($pendapatanAkuns as $a) {
    $row = $db->query("
        SELECT COALESCE(SUM(jd.kredit),0) - COALESCE(SUM(jd.debit),0) AS saldo
        FROM fin_jurnal_detail jd
        JOIN fin_jurnal j ON j.id = jd.jurnal_id
        WHERE jd.akun_id = ?
    ", [$a['id']])->getRowArray();
    $saldo = (float)($row['saldo'] ?? 0);
    if ($saldo != 0) {
        echo "  Pendapatan: {$a['kode_akun']} - {$a['nama_akun']}: Rp " . number_format($saldo, 2, ',', '.') . "\n";
        $totPendapatan += $saldo;
    }
}
echo "  TOTAL PENDAPATAN: Rp " . number_format($totPendapatan, 2, ',', '.') . "\n";

$bebanAkuns = $accountModel->getBeban();
$totBeban = 0;
foreach ($bebanAkuns as $a) {
    $row = $db->query("
        SELECT COALESCE(SUM(jd.debit),0) - COALESCE(SUM(jd.kredit),0) AS saldo
        FROM fin_jurnal_detail jd
        JOIN fin_jurnal j ON j.id = jd.jurnal_id
        WHERE jd.akun_id = ?
    ", [$a['id']])->getRowArray();
    $saldo = (float)($row['saldo'] ?? 0);
    if ($saldo != 0) {
        echo "  Beban: {$a['kode_akun']} - {$a['nama_akun']}: Rp " . number_format($saldo, 2, ',', '.') . "\n";
        $totBeban += $saldo;
    }
}
echo "  TOTAL BEBAN: Rp " . number_format($totBeban, 2, ',', '.') . "\n";
echo "  SURPLUS / DEFISIT: Rp " . number_format($totPendapatan - $totBeban, 2, ',', '.') . "\n";

echo "\nALL MODELS & REPORT QUERIES EXECUTED SUCCESSFULLY!\n";
