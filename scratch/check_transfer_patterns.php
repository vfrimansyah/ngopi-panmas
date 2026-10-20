<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

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

$multiPosCount = 0;
$multiNegCount = 0;
$adminFeeWithTransfers = 0;
$otherTransfers = [];

for ($r = 3; $r <= 663; $r++) {
    $rowAccs = [];
    foreach ($accColumns as $col => $name) {
        $val = $sheet->getCell($col . $r)->getCalculatedValue();
        if (is_numeric($val) && abs((float)$val) > 0.0001) {
            $rowAccs[$name] = (float)$val;
        }
    }
    if (empty($rowAccs)) continue;

    $pos = [];
    $neg = [];
    foreach ($rowAccs as $a => $v) {
        if ($v > 0) $pos[$a] = $v;
        else $neg[$a] = $v;
    }

    if (count($pos) > 1) {
        $multiPosCount++;
        echo "Row $r has multiple positive accounts: " . json_encode($pos) . "\n";
    }
    if (count($neg) > 1) {
        $multiNegCount++;
        echo "Row $r has multiple negative accounts: " . json_encode($neg) . "\n";
    }

    if (count($pos) > 0 && count($neg) > 0) {
        $beaAdmin = (float)$sheet->getCell('I' . $r)->getCalculatedValue();
        $sumNeg = abs(array_sum($neg));
        $sumPos = array_sum($pos);
        $diff = abs($sumNeg - $sumPos);
        if ($beaAdmin > 0) {
            $adminFeeWithTransfers++;
        }
        if ($diff > 0.01 && abs($diff - $beaAdmin) > 0.01) {
            echo "Row $r transfer difference not matching admin fee: Neg=$sumNeg, Pos=$sumPos, Diff=$diff, BeaAdmin=$beaAdmin, Keg=" . $sheet->getCell('B' . $r)->getValue() . "\n";
        }
    }
}

echo "\nSummary:\n";
echo "Multi-pos rows: $multiPosCount\n";
echo "Multi-neg rows: $multiNegCount\n";
echo "Admin fee with transfers: $adminFeeWithTransfers\n";
