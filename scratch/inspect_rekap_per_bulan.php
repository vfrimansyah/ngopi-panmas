<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = dirname(__DIR__) . '/_document_project/Kas DPC Panmas - baru (1).xlsx';
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getSheetByName('Rekap per bulan');

$hRow = min(50, $sheet->getHighestRow());
$hCol = min('P', $sheet->getHighestColumn());

for ($r = 1; $r <= $hRow; $r++) {
    $rowVals = [];
    $hasVal = false;
    for ($c = 'A'; $c <= $hCol; $c++) {
        $val = trim((string)$sheet->getCell($c . $r)->getCalculatedValue());
        $fmt = trim((string)$sheet->getCell($c . $r)->getFormattedValue());
        if ($val !== '') {
            $hasVal = true;
            $rowVals[] = "$c: $fmt";
        }
    }
    if ($hasVal) {
        echo "R$r: " . implode(" | ", $rowVals) . "\n";
    }
}
