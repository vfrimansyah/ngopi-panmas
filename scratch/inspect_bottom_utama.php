<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = dirname(__DIR__) . '/_document_project/Kas DPC Panmas - baru (1).xlsx';
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getSheetByName('Utama');

$highestRow = $sheet->getHighestRow();
echo "Highest row: $highestRow\n";

for ($r = 650; $r <= 670; $r++) {
    $tgl = $sheet->getCell('A' . $r)->getFormattedValue();
    $keg = $sheet->getCell('B' . $r)->getFormattedValue();
    $tot = $sheet->getCell('E' . $r)->getFormattedValue();
    if ($tgl || $keg || $tot) {
        echo "Row $r: Tgl=[$tgl], Keg=[$keg], Tot=[$tot]\n";
    }
}

for ($r = 1200; $r <= 1215; $r++) {
    $hasAny = false;
    $rowVals = [];
    for ($c = 'A'; $c !== 'S'; $c++) {
        $v = $sheet->getCell($c . $r)->getFormattedValue();
        if ($v !== '') {
            $hasAny = true;
            $rowVals[] = "$c: $v";
        }
    }
    if ($hasAny) {
        echo "Row $r: " . implode(" | ", $rowVals) . "\n";
    }
}
