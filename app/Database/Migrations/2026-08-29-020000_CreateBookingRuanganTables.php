<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBookingRuanganTables extends Migration
{
    public function up()
    {
        // ── 1. Master Ruangan ─────────────────────────────────────────────────
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_ruangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'nomor_urut' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'kapasitas' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('booking_ruangan_master', true);

        // Seed default Ruangan (from PDF Page 5)
        $now = date('Y-m-d H:i:s');
        $initialRuangan = [
            ['nama_ruangan' => 'R. Meeting (Lt 1)', 'nomor_urut' => 1, 'kapasitas' => 20, 'created_at' => $now, 'updated_at' => $now],
            ['nama_ruangan' => 'R. Utama (Lt 2)',   'nomor_urut' => 2, 'kapasitas' => 80, 'created_at' => $now, 'updated_at' => $now],
            ['nama_ruangan' => 'R. Atas (Lt 3)',    'nomor_urut' => 3, 'kapasitas' => 40, 'created_at' => $now, 'updated_at' => $now],
        ];
        $this->db->table('booking_ruangan_master')->insertBatch($initialRuangan);

        // ── 2. Tabel Booking Ruangan ─────────────────────────────────────────
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tgl_kegiatan' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'jam_mulai' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'jam_selesai' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'nama_kegiatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'penyelenggara' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'ruangan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'pj_kegiatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'null' => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('ruangan_id');
        $this->forge->addKey('tgl_kegiatan');
        $this->forge->createTable('booking_ruangan', true);
    }

    public function down()
    {
        $this->forge->dropTable('booking_ruangan', true);
        $this->forge->dropTable('booking_ruangan_master', true);
    }
}
