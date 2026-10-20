<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = dirname(__DIR__) . '/_document_project/Kas DPC Panmas - baru (1).xlsx';
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getSheetByName('Rekap per jenis per bulan');

for ($r = 1; $r <= 35; $r++) {
    $rowVals = [];
    for ($c = 'A'; $c <= 'H'; $c++) {
        $val = $sheet->getCell($c . $r)->getFormattedValue();
        if ($val !== '') {
            $rowVals[] = "$c: $val";
        }
    }
    if (!empty($rowVals)) {
        echo "R$r: " . implode(" | ", $rowVals) . "\n";
    }
}
