<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSisirRwTables extends Migration
{
    public function up()
    {
        // ── 1. Tabel Master Bentuk Kegiatan ──────────────────────────────────
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'urutan' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pemilu_bentuk_kegiatan', true);

        // Seed data awal
        $bentukKegiatan = [
            ['nama' => 'Silaturahim', 'urutan' => 1],
            ['nama' => 'Reses',       'urutan' => 2],
            ['nama' => 'Soskom',      'urutan' => 3],
            ['nama' => 'Arisan',      'urutan' => 4],
            ['nama' => 'Bakti Sosial','urutan' => 5],
            ['nama' => 'Pelayanan Kesehatan', 'urutan' => 6],
        ];
        $now = date('Y-m-d H:i:s');
        foreach ($bentukKegiatan as &$b) {
            $b['created_at'] = $now;
            $b['updated_at'] = $now;
        }
        $this->db->table('pemilu_bentuk_kegiatan')->insertBatch($bentukKegiatan);

        // ── 2. Tabel Kegiatan Sisir RW ──────────────────────────────────────
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_kegiatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'rt' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'rw' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            // Kelurahan menggunakan FK ke reg_villages
            'village_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'kelurahan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'kecamatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Pancoran Mas',
            ],
            'bentuk_kegiatan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'jumlah_peserta' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'tokoh_hadir' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'agenda_lanjutan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'keterangan' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['hijau', 'kuning', 'merah'],
                'default'    => 'hijau',
            ],
            'created_by' => [
                'type'    => 'INT',
                'null'    => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('village_id');
        $this->forge->addKey('bentuk_kegiatan_id');
        $this->forge->addKey('status');
        $this->forge->createTable('pemilu_sisir_rw', true);
    }

    public function down()
    {
        $this->forge->dropTable('pemilu_sisir_rw', true);
        $this->forge->dropTable('pemilu_bentuk_kegiatan', true);
    }
}
