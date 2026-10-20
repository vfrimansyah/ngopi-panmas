<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterDpraTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'village_id' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => false,
            ],
            'nama_dpra' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'kode_dpra' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'aktif',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('village_id');
        $this->forge->createTable('master_dpra', true);

        // Seed 6 DPRa bawaan Pancoran Mas
        $now = date('Y-m-d H:i:s');
        $initialDpra = [
            ['village_id' => '3276011006', 'nama_dpra' => 'DPRa Depok',               'kode_dpra' => 'DPRA-DPK',  'status' => 'aktif', 'created_at' => $now, 'updated_at' => $now],
            ['village_id' => '3276011007', 'nama_dpra' => 'DPRa Depok Jaya',          'kode_dpra' => 'DPRA-DPKJ', 'status' => 'aktif', 'created_at' => $now, 'updated_at' => $now],
            ['village_id' => '3276011008', 'nama_dpra' => 'DPRa Pancoran Mas',        'kode_dpra' => 'DPRA-PCM',  'status' => 'aktif', 'created_at' => $now, 'updated_at' => $now],
            ['village_id' => '3276011009', 'nama_dpra' => 'DPRa Mampang',             'kode_dpra' => 'DPRA-MPG',  'status' => 'aktif', 'created_at' => $now, 'updated_at' => $now],
            ['village_id' => '3276011011', 'nama_dpra' => 'DPRa Rangkapan Jaya',      'kode_dpra' => 'DPRA-RKJ',  'status' => 'aktif', 'created_at' => $now, 'updated_at' => $now],
            ['village_id' => '3276011010', 'nama_dpra' => 'DPRa Rangkapan Jaya Baru', 'kode_dpra' => 'DPRA-RKB',  'status' => 'aktif', 'created_at' => $now, 'updated_at' => $now],
        ];

        $this->db->table('master_dpra')->insertBatch($initialDpra);
    }

    public function down()
    {
        $this->forge->dropTable('master_dpra', true);
    }
}
