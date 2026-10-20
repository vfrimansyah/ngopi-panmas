<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePemiluResultsTables extends Migration
{
    public function up()
    {
        // 1. Table pemilu_caleg
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tahun' => [
                'type' => 'INT',
            ],
            'partai_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'no_urut' => [
                'type' => 'INT',
            ],
            'nama_caleg' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'tingkat_pemilu' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'DPRD_KOTA',
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
        $this->forge->addForeignKey('partai_id', 'pemilu_partai', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pemilu_caleg');

        // 2. Table pemilu_suara_partai
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tahun' => [
                'type' => 'INT',
            ],
            'tps_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'partai_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'tingkat_pemilu' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'DPRD_KOTA',
            ],
            'jumlah_suara' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 0,
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
        $this->forge->addForeignKey('tps_id', 'pemilu_tps', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('partai_id', 'pemilu_partai', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pemilu_suara_partai');

        // 3. Table pemilu_suara_caleg
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tahun' => [
                'type' => 'INT',
            ],
            'tps_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'caleg_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'jumlah_suara' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 0,
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
        $this->forge->addForeignKey('tps_id', 'pemilu_tps', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('caleg_id', 'pemilu_caleg', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pemilu_suara_caleg');

        // 4. Table pemilu_suara_tps
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tahun' => [
                'type' => 'INT',
            ],
            'tps_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'tingkat_pemilu' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'DPRD_KOTA',
            ],
            'suara_sah' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'suara_tidak_sah' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'suara_total' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
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
        $this->forge->addForeignKey('tps_id', 'pemilu_tps', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pemilu_suara_tps');
    }

    public function down()
    {
        $this->forge->dropTable('pemilu_suara_tps');
        $this->forge->dropTable('pemilu_suara_caleg');
        $this->forge->dropTable('pemilu_suara_partai');
        $this->forge->dropTable('pemilu_caleg');
    }
}
