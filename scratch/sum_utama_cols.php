<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = dirname(__DIR__) . '/_document_project/Kas DPC Panmas - baru (1).xlsx';
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getSheetByName('Utama');

$cols = [
    'E' => 'Total Accounts',
    'F' => 'Rek 7212',
    'G' => 'Rek 7137',
    'H' => 'Rek 79100',
    'I' => 'Bea admin',
    'J' => 'Kas Asep',
    'K' => 'DPRa',
    'L' => 'Actionpay',
    'M' => 'BeaKegiatan',
    'N' => 'Konsumsi',
    'O' => 'Ops',
    'P' => 'Taawun',
    'Q' => 'piutang',
];

$sumsWithRow2 = [];
$sumsWithoutRow2 = [];

foreach ($cols as $c => $lbl) {
    $sumsWithRow2[$c] = 0;
    $sumsWithoutRow2[$c] = 0;
}

for ($r = 2; $r <= 663; $r++) {
    foreach ($cols as $c => $lbl) {
        $v = (float)$sheet->getCell($c . $r)->getCalculatedValue();
        $sumsWithRow2[$c] += $v;
        if ($r > 2) {
            $sumsWithoutRow2[$c] += $v;
        }
    }
}

echo "=== SUMS OF ALL COLUMNS (Row 2 to 663 - Including Saldo Awal) ===\n";
foreach ($cols as $c => $lbl) {
    echo sprintf("%-15s (%s): Rp %18s\n", $lbl, $c, number_format($sumsWithRow2[$c], 2, ',', '.'));
}

echo "\n=== SUMS OF ALL COLUMNS (Row 3 to 663 - Transactions Only) ===\n";
foreach ($cols as $c => $lbl) {
    echo sprintf("%-15s (%s): Rp %18s\n", $lbl, $c, number_format($sumsWithoutRow2[$c], 2, ',', '.'));
}
