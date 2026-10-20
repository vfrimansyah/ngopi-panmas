<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePemiluTpsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tahun' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'province_id' => [
                'type'       => 'CHAR',
                'constraint' => 2,
            ],
            'regency_id' => [
                'type'       => 'CHAR',
                'constraint' => 4,
            ],
            'district_id' => [
                'type'       => 'CHAR',
                'constraint' => 6,
            ],
            'village_id' => [
                'type'       => 'CHAR',
                'constraint' => 10,
            ],
            'rw' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'rt' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'tps' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'dapil_kota' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'dapil_prov' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'dapil_ri' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'dpt' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        
        // Add foreign keys targeting your existing geographic tables
        $this->forge->addForeignKey('province_id', 'reg_provinces', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('regency_id', 'reg_regencies', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('district_id', 'reg_districts', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('village_id', 'reg_villages', 'id', 'RESTRICT', 'CASCADE');

        $attributes = [
            'ENGINE'        => 'InnoDB',
            'CHARACTER SET' => 'utf8mb3',
            'COLLATE'       => 'utf8mb3_general_ci',
        ];
        $this->forge->createTable('pemilu_tps', false, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('pemilu_tps');
    }
}
