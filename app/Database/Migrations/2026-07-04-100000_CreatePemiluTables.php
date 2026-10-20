<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePemiluTables extends Migration
{
    public function up()
    {
        // 1. Table pemilu_partai
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_partai' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'lambang_partai' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'tahun_berdiri' => [
                'type'       => 'INT',
                'null'       => true,
            ],
            'azas_ideologi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'cakupan' => [
                'type'       => 'ENUM',
                'constraint' => ['Nasional', 'Lokal'],
                'default'    => 'Nasional',
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
        $this->forge->createTable('pemilu_partai');

        // 2. Table pemilu_peserta
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
            'ketua_partai' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addForeignKey('partai_id', 'pemilu_partai', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pemilu_peserta');
    }

    public function down()
    {
        $this->forge->dropTable('pemilu_peserta');
        $this->forge->dropTable('pemilu_partai');
    }
}
