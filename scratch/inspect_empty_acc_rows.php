<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = dirname(__DIR__) . '/_document_project/Kas DPC Panmas - baru (1).xlsx';
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getSheetByName('Utama');

$testRows = [471];
foreach ($testRows as $r) {
    echo "=== ROW $r ===\n";
    for ($c = 'A'; $c !== 'T'; $c++) {
        $val = $sheet->getCell($c . $r)->getValue();
        $fmt = $sheet->getCell($c . $r)->getFormattedValue();
        if ($val !== null && $val !== '') {
            $header = $sheet->getCell($c . '1')->getValue();
            echo "  $c ($header): raw=[$val], fmt=[$fmt]\n";
        }
    }
}
