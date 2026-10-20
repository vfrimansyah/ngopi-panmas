<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRegionIdsToLembagaProfiles extends Migration
{
    public function up()
    {
        $fields = [
            'province_id' => [
                'type'       => 'CHAR',
                'constraint' => 2,
                'null'       => true,
                'after'      => 'alamat_lengkap',
            ],
            'regency_id' => [
                'type'       => 'CHAR',
                'constraint' => 4,
                'null'       => true,
                'after'      => 'province_id',
            ],
            'district_id' => [
                'type'       => 'CHAR',
                'constraint' => 6,
                'null'       => true,
                'after'      => 'regency_id',
            ],
            'village_id' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'district_id',
            ],
        ];

        foreach ($fields as $col => $attr) {
            if (!$this->db->fieldExists($col, 'lembaga_profiles')) {
                $this->forge->addColumn('lembaga_profiles', [$col => $attr]);
            }
        }
    }

    public function down()
    {
        $cols = ['village_id', 'district_id', 'regency_id', 'province_id'];
        foreach ($cols as $col) {
            if ($this->db->fieldExists($col, 'lembaga_profiles')) {
                $this->forge->dropColumn('lembaga_profiles', $col);
            }
        }
    }
}
