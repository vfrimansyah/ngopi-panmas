<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

$filePath = dirname(__DIR__) . '/_document_project/Kas DPC Panmas - baru (1).xlsx';
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getSheetByName('Utama');

$accColumns = [
    'F' => 'Rek 7212',
    'G' => 'Rek 7137',
    'H' => 'Rek 79100',
    'J' => 'Kas Asep',
    'L' => 'Actionpay',
];

$saldoAwal = [
    'Rek 7212' => 67879398.79,
    'Rek 7137' => 29483261.99,
    'Rek 79100' => 0.0,
    'Kas Asep' => 0.0,
    'Actionpay' => 0.0,
];

$rekeningPemasukan = [
    'Rek 7212' => 0.0,
    'Rek 7137' => 0.0,
    'Rek 79100' => 0.0,
    'Kas Asep' => 0.0,
    'Actionpay' => 0.0,
];

$rekeningPengeluaran = [
    'Rek 7212' => 0.0,
    'Rek 7137' => 0.0,
    'Rek 79100' => 0.0,
    'Kas Asep' => 0.0,
    'Actionpay' => 0.0,
];

$simulatedTrx = [];
$totalRowsProcessed = 0;

for ($r = 3; $r <= 663; $r++) {
    $tglRaw = $sheet->getCell('A' . $r)->getValue();
    $kegiatan = trim((string)$sheet->getCell('B' . $r)->getValue());
    $grup = trim((string)$sheet->getCell('C' . $r)->getValue());
    $jenis = trim((string)$sheet->getCell('D' . $r)->getValue());
    $beaAdmin = (float)$sheet->getCell('I' . $r)->getCalculatedValue();

    $rowAccs = [];
    foreach ($accColumns as $col => $name) {
        $val = $sheet->getCell($col . $r)->getCalculatedValue();
        if (is_numeric($val) && abs((float)$val) > 0.0001) {
            $rowAccs[$name] = (float)$val;
        }
    }

    if (empty($rowAccs)) continue;
    $totalRowsProcessed++;

    $pos = [];
    $neg = [];
    foreach ($rowAccs as $a => $v) {
        if ($v > 0) $pos[$a] = $v;
        else $neg[$a] = abs($v);
    }

    // Check if internal transfer between accounts
    if (count($pos) === 1 && count($neg) === 1) {
        $fromAcc = array_key_first($neg);
        $fromAmt = $neg[$fromAcc];
        $toAcc = array_key_first($pos);
        $toAmt = $pos[$toAcc];

        // Outflow from fromAcc
        $rekeningPengeluaran[$fromAcc] += $fromAmt;
        $simulatedTrx[] = [
            'row' => $r,
            'rekening' => $fromAcc,
            'tipe' => 'pengeluaran',
            'jumlah' => $fromAmt,
            'kategori' => $jenis ?: 'Transfer',
            'deskripsi' => ($kegiatan ?: "Transfer ke $toAcc") . ($grup ? " ($grup)" : ""),
            'is_transfer' => true,
        ];

        // Inflow to toAcc
        $rekeningPemasukan[$toAcc] += $toAmt;
        $simulatedTrx[] = [
            'row' => $r,
            'rekening' => $toAcc,
            'tipe' => 'pemasukan',
            'jumlah' => $toAmt,
            'kategori' => $jenis ?: 'Transfer',
            'deskripsi' => ($kegiatan ?: "Transfer dari $fromAcc") . ($grup ? " ($grup)" : ""),
            'is_transfer' => true,
        ];
    } else {
        // Multi account or single account
        foreach ($pos as $acc => $amt) {
            $rekeningPemasukan[$acc] += $amt;
            $simulatedTrx[] = [
                'row' => $r,
                'rekening' => $acc,
                'tipe' => 'pemasukan',
                'jumlah' => $amt,
                'kategori' => $jenis ?: 'Penerimaan',
                'deskripsi' => ($kegiatan ?: 'Pemasukan') . ($grup ? " ($grup)" : ""),
                'is_transfer' => false,
            ];
        }
        foreach ($neg as $acc => $amt) {
            $rekeningPengeluaran[$acc] += $amt;
            $simulatedTrx[] = [
                'row' => $r,
                'rekening' => $acc,
                'tipe' => 'pengeluaran',
                'jumlah' => $amt,
                'kategori' => $jenis ?: 'Pengeluaran',
                'deskripsi' => ($kegiatan ?: 'Pengeluaran') . ($grup ? " ($grup)" : ""),
                'is_transfer' => false,
            ];
        }
    }
}

echo "Total Excel rows processed: $totalRowsProcessed\n";
echo "Total simulated transactions: " . count($simulatedTrx) . "\n\n";

echo "=== REKENING BALANCES AFTER ALL TRANSACTIONS ===\n";
$grandSaldo = 0;
foreach ($accColumns as $col => $name) {
    $awal = $saldoAwal[$name];
    $masuk = $rekeningPemasukan[$name];
    $keluar = $rekeningPengeluaran[$name];
    $akhir = $awal + $masuk - $keluar;
    $grandSaldo += $akhir;
    echo "Account: $name\n";
    echo "  Saldo Awal   : Rp " . number_format($awal, 2, ',', '.') . "\n";
    echo "  Pemasukan    : Rp " . number_format($masuk, 2, ',', '.') . "\n";
    echo "  Pengeluaran  : Rp " . number_format($keluar, 2, ',', '.') . "\n";
    echo "  Saldo Akhir  : Rp " . number_format($akhir, 2, ',', '.') . "\n\n";
}

echo "Grand Total Saldo across all accounts: Rp " . number_format($grandSaldo, 2, ',', '.') . "\n";
