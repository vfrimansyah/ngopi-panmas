<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

$filePath = dirname(__DIR__) . '/_document_project/Kas DPC Panmas - baru (1).xlsx';
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getSheetByName('Utama');

$highestRow = $sheet->getHighestRow();
echo "Highest row: $highestRow\n";

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

$multiAccountRows = [];
$emptyAccountRows = [];
$saldoRows = [];
$totalRows = 0;
$earliestDate = null;
$latestDate = null;

for ($r = 2; $r <= $highestRow; $r++) {
    $tglRaw = $sheet->getCell('A' . $r)->getValue();
    $kegiatan = trim((string)$sheet->getCell('B' . $r)->getValue());
    $grup = trim((string)$sheet->getCell('C' . $r)->getValue());
    $jenis = trim((string)$sheet->getCell('D' . $r)->getValue());

    // check accounts
    $rowAccs = [];
    foreach ($accColumns as $col => $name) {
        $val = (float)$sheet->getCell($col . $r)->getValue();
        if ($val != 0) {
            $rowAccs[$name] = $val;
            $totals[$name] += $val;
            $counts[$name]++;
        }
    }

    if (empty($tglRaw) && empty($kegiatan) && empty($jenis) && empty($rowAccs)) {
        continue;
    }

    $totalRows++;

    if (is_numeric($tglRaw)) {
        $dt = Date::excelToDateTimeObject($tglRaw)->format('Y-m-d');
        if (!$earliestDate || $dt < $earliestDate) $earliestDate = $dt;
        if (!$latestDate || $dt > $latestDate) $latestDate = $dt;
    }

    if (strtolower($jenis) === 'saldo') {
        $saldoRows[] = ['row' => $r, 'accs' => $rowAccs];
    } elseif (count($rowAccs) > 1) {
        $multiAccountRows[] = ['row' => $r, 'jenis' => $jenis, 'kegiatan' => $kegiatan, 'accs' => $rowAccs];
    } elseif (count($rowAccs) === 0) {
        $emptyAccountRows[] = ['row' => $r, 'kegiatan' => $kegiatan, 'jenis' => $jenis];
    }
}

echo "Active transaction rows: $totalRows\n";
echo "Date range: $earliestDate to $latestDate\n\n";

echo "Account Summary (from Row 2 to $highestRow):\n";
foreach ($totals as $name => $sum) {
    echo "  $name: {$counts[$name]} rows, Sum: Rp " . number_format($sum, 2, ',', '.') . "\n";
}

echo "\nSaldo rows (" . count($saldoRows) . "):\n";
print_r($saldoRows);

echo "\nMulti-account rows count (Transfers/Splits): " . count($multiAccountRows) . "\n";
echo "First 5 multi-account rows:\n";
print_r(array_slice($multiAccountRows, 0, 5));

echo "\nEmpty-account rows count: " . count($emptyAccountRows) . "\n";
if (!empty($emptyAccountRows)) {
    print_r(array_slice($emptyAccountRows, 0, 5));
}
