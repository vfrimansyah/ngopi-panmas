<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

$filePath = dirname(__DIR__) . '/_document_project/Kas DPC Panmas - baru (1).xlsx';
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getSheetByName('Utama');

$highestRow = $sheet->getHighestRow();

$accColumns = [
    'F' => 'Rek 7212',
    'G' => 'Rek 7137',
    'H' => 'Rek 79100',
    'J' => 'Kas Asep',
    'L' => 'Actionpay',
];

$totals = [
    'Rek 7212' => 0.0,
    'Rek 7137' => 0.0,
    'Rek 79100' => 0.0,
    'Kas Asep' => 0.0,
    'Actionpay' => 0.0,
];

$counts = [
    'Rek 7212' => 0,
    'Rek 7137' => 0,
    'Rek 79100' => 0,
    'Kas Asep' => 0,
    'Actionpay' => 0,
];

$transfers = [];
$incomes = [];
$expenses = [];
$zeroAmountRows = [];

for ($r = 2; $r <= $highestRow; $r++) {
    $tglRaw = $sheet->getCell('A' . $r)->getValue();
    $kegiatan = trim((string)$sheet->getCell('B' . $r)->getValue());
    $grup = trim((string)$sheet->getCell('C' . $r)->getValue());
    $jenis = trim((string)$sheet->getCell('D' . $r)->getValue());

    // check accounts with getCalculatedValue()
    $rowAccs = [];
    foreach ($accColumns as $col => $name) {
        $val = $sheet->getCell($col . $r)->getCalculatedValue();
        if (is_numeric($val) && abs((float)$val) > 0.0001) {
            $rowAccs[$name] = (float)$val;
            $totals[$name] += (float)$val;
            $counts[$name]++;
        }
    }

    if (empty($tglRaw) && empty($kegiatan) && empty($jenis) && empty($rowAccs)) {
        continue;
    }

    if (strtolower($jenis) === 'saldo') {
        continue; // Saldo awal
    }

    if (empty($rowAccs)) {
        $zeroAmountRows[] = ['row' => $r, 'tgl' => $tglRaw, 'kegiatan' => $kegiatan, 'grup' => $grup, 'jenis' => $jenis];
        continue;
    }

    // Check if transfer (e.g., rekonsil or one positive and one negative)
    $posAccs = [];
    $negAccs = [];
    foreach ($rowAccs as $acc => $val) {
        if ($val > 0) $posAccs[$acc] = $val;
        else $negAccs[$acc] = $val;
    }

    if (count($posAccs) > 0 && count($negAccs) > 0) {
        $transfers[] = ['row' => $r, 'kegiatan' => $kegiatan, 'grup' => $grup, 'jenis' => $jenis, 'from' => $negAccs, 'to' => $posAccs];
    } elseif (count($posAccs) > 0) {
        $incomes[] = ['row' => $r, 'kegiatan' => $kegiatan, 'grup' => $grup, 'jenis' => $jenis, 'accs' => $posAccs];
    } else {
        $expenses[] = ['row' => $r, 'kegiatan' => $kegiatan, 'grup' => $grup, 'jenis' => $jenis, 'accs' => $negAccs];
    }
}

echo "=== CALCULATION RESULTS ===\n";
echo "Account Balances (Cumulative with initial saldo row 2):\n";
foreach ($totals as $name => $sum) {
    echo "  $name: {$counts[$name]} rows, Net: Rp " . number_format($sum, 2, ',', '.') . "\n";
}

echo "\nSummary of Transactions:\n";
echo "  Incomes (Pemasukan) count: " . count($incomes) . "\n";
echo "  Expenses (Pengeluaran) count: " . count($expenses) . "\n";
echo "  Transfers/Rekonsil count: " . count($transfers) . "\n";
echo "  Zero Amount / Memo rows count: " . count($zeroAmountRows) . "\n";

echo "\nSample Zero Amount Rows (first 10):\n";
print_r(array_slice($zeroAmountRows, 0, 10));

echo "\nSample Transfers (first 5):\n";
print_r(array_slice($transfers, 0, 5));
