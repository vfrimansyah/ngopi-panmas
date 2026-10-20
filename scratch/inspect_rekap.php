<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = dirname(__DIR__) . '/_document_project/Kas DPC Panmas - baru (1).xlsx';
$spreadsheet = IOFactory::load($filePath);

foreach (['Rekap per bulan', 'Rekap per jenis per bulan', 'Rekap'] as $sName) {
    $sheet = $spreadsheet->getSheetByName($sName);
    if (!$sheet) continue;
    echo "========================================\n";
    echo "Sheet: $sName\n";
    echo "========================================\n";
    $hRow = min(40, $sheet->getHighestRow());
    $hCol = min('P', $sheet->getHighestColumn());
    for ($r = 1; $r <= $hRow; $r++) {
        $rowVals = [];
        $hasVal = false;
        for ($c = 'A'; $c <= $hCol; $c++) {
            $val = trim((string)$sheet->getCell($c . $r)->getFormattedValue());
            if ($val !== '') $hasVal = true;
            $rowVals[] = $val;
        }
        if ($hasVal) {
            echo "R$r: " . implode(" | ", array_slice($rowVals, 0, 10)) . "\n";
        }
    }
}
