<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = dirname(__DIR__) . '/_document_project/Kas DPC Panmas - baru (1).xlsx';
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getSheetByName('Rekap per jenis per bulan');

$hRow = $sheet->getHighestRow();
$hCol = $sheet->getHighestColumn();
echo "Highest row: $hRow, Highest col: $hCol\n";

for ($r = 34; $r <= 37; $r++) {
    for ($c = 'I'; $c <= 'M'; $c++) {
        $val = $sheet->getCell($c . $r)->getValue();
        $fmt = $sheet->getCell($c . $r)->getFormattedValue();
        if ($val !== null && $val !== '') {
            echo "  $c$r: val=[$val] fmt=[$fmt]\n";
        }
    }
}
