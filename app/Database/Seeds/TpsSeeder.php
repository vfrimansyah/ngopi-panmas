<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TpsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        $filePath = ROOTPATH . '_document_project/data_tps.xlsx';
        if (!file_exists($filePath)) {
            echo "Error: Excel file not found at {$filePath}\n";
            return;
        }

        // Truncate table first to prevent duplicate entries
        $db->table('pemilu_tps')->truncate();
        echo "Truncated pemilu_tps table.\n";

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $kelurahanMapping = [
            'Depok'               => '3276011006',
            'Depok Jaya'          => '3276011007',
            'Pancoran Mas'        => '3276011008',
            'Mampang'             => '3276011009',
            'Rangkapan Jaya Baru' => '3276011010',
            'Rangkapan Jaya'      => '3276011011',
        ];

        $batch = [];
        $now = date('Y-m-d H:i:s');
        $count = 0;

        for ($row = 2; $row <= $highestRow; $row++) {
            $kelName = trim($sheet->getCell([1, $row])->getValue() ?? '');
            if (empty($kelName)) {
                continue;
            }

            $villageId = $kelurahanMapping[$kelName] ?? null;
            if (!$villageId) {
                echo "Warning: Kelurahan '$kelName' at row $row has no matching ID in mapping. Skipping...\n";
                continue;
            }

            $rwVal = trim($sheet->getCell([2, $row])->getValue() ?? '');
            $rtVal = trim($sheet->getCell([3, $row])->getValue() ?? '');
            $tpsVal = trim($sheet->getCell([4, $row])->getValue() ?? '');
            $dptVal = trim($sheet->getCell([5, $row])->getValue() ?? '');

            // Format RT/RW to be 2-digit padded strings (e.g. 1 -> 01)
            $rw = ($rwVal !== '') ? str_pad($rwVal, 2, '0', STR_PAD_LEFT) : null;
            $rt = ($rtVal !== '') ? str_pad($rtVal, 2, '0', STR_PAD_LEFT) : null;
            // Format TPS to be TPS + 3-digit padded number (e.g. 1 -> TPS 001)
            $tps = ($tpsVal !== '') ? 'TPS ' . str_pad($tpsVal, 3, '0', STR_PAD_LEFT) : '';

            $batch[] = [
                'tahun'       => 2024,
                'province_id' => '32',
                'regency_id'  => '3276',
                'district_id' => '327601',
                'village_id'  => $villageId,
                'rw'          => $rw,
                'rt'          => $rt,
                'tps'         => $tps,
                'dapil_kota'  => '1',
                'dapil_prov'  => 'VIII',
                'dapil_ri'    => 'Jawa Barat VI',
                'dpt'         => ($dptVal !== '') ? intval($dptVal) : null,
                'created_at'  => $now,
                'updated_at'  => $now
            ];

            $count++;
            
            // Insert in batches of 100 rows to optimize performance
            if (count($batch) >= 100) {
                $db->table('pemilu_tps')->insertBatch($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            $db->table('pemilu_tps')->insertBatch($batch);
        }

        echo "Successfully imported $count TPS records from Excel.\n";
    }
}
