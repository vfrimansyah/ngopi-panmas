<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = dirname(__DIR__) . '/_document_project/Kas DPC Panmas - baru (1).xlsx';
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getSheetByName('Rekap per jenis per bulan');

echo "K35 formula: " . $sheet->getCell('K35')->getValue() . "\n";
echo "K36 formula: " . $sheet->getCell('K36')->getValue() . "\n";

// Let's also check all cells around K20-K37
for ($r = 20; $r <= 37; $r++) {
    for ($c = 'A'; $c <= 'N'; $c++) {
        $val = $sheet->getCell($c . $r)->getValue();
        if ($val && strpos((string)$val, '=') === 0) {
            echo "  $c$r: $val\n";
        }
    }
}
