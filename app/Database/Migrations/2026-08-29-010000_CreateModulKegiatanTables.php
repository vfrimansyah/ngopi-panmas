<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateModulKegiatanTables extends Migration
{
    public function up()
    {
        // ── 1. Master Jenis Kegiatan ──────────────────────────────────────────
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_kegiatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'nomor_urut' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('kegiatan_jenis', true);

        // Seed 28 Master Jenis Kegiatan bawaan
        $now = date('Y-m-d H:i:s');
        $initialJenis = [
            'Silaturahim Tokoh',
            'Fogging',
            'Bank Sampah',
            'Gerai Mijal',
            'Pelayanan Kesehatan',
            'Jumat Berkah',
            'Pelatihan',
            'Pengajian',
            'Seminar',
            'Talkshow',
            'Training Orientasi Partai',
            'Futsal (Turnamen/Latihan)',
            'Senam PKS',
            'Gowes Bareng',
            'Olahraga Bareng Lainnya',
            'Aksi Bantuan Pangan (Sembako)',
            'Qurban',
            'Konsolidasi Internal',
            'Advokasi Aleg - Infrastruktur',
            'Advokasi Aleg - Bantuan Hibah',
            'Support Kegiatan Warga',
            'Lomba',
            'Santunan Dhuafa',
            'Ambulance',
            'Parenting',
            'Konsultasi',
            'Melati',
            'Lainnya',
        ];

        $batchJenis = [];
        $i = 1;
        foreach ($initialJenis as $nama) {
            $batchJenis[] = [
                'nama_kegiatan' => $nama,
                'nomor_urut'    => $i++,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }
        $this->db->table('kegiatan_jenis')->insertBatch($batchJenis);

        // ── 2. Tabel Data Kegiatan ────────────────────────────────────────────
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
            'jenis_kegiatan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'jam_mulai' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_selesai' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'penyelenggara' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'pic' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'jumlah_peserta' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'lokasi_kegiatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'dpra' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
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
            'tokoh_hadir' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tokoh_pks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'link_doc' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'flyer' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'null' => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('jenis_kegiatan_id');
        $this->forge->addKey('tanggal');
        $this->forge->addKey('dpra');
        $this->forge->addKey('rw');
        $this->forge->createTable('kegiatan_list', true);
    }

    public function down()
    {
        $this->forge->dropTable('kegiatan_list', true);
        $this->forge->dropTable('kegiatan_jenis', true);
    }
}
