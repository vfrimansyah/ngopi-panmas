<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssetManagementTables extends Migration
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
            'kode_aset' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'nama_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'penanggung_jawab' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'kondisi' => [
                'type'       => 'ENUM',
                'constraint' => ['baik', 'kurang_baik', 'rusak', 'hilang'],
                'default'    => 'baik',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('lokasi');
        $this->forge->addKey('kondisi');
        $this->forge->createTable('assets', true);

        // Seed 32 initial assets from pengurus spreadsheet
        $now = date('Y-m-d H:i:s');
        $assets = [
            // ── LANTAI ATAS ──────────────────────────────────────────────
            ['kode_aset' => 'AST-001', 'nama_barang' => 'AC 2PK',                       'lokasi' => 'Lantai Atas', 'jumlah' => 2, 'kondisi' => 'baik',       'keterangan' => 'Beli saat kepengurusan Pa Ilham',                       'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-002', 'nama_barang' => 'Router (Perangkat Jaringan)',   'lokasi' => 'Lantai Atas', 'jumlah' => 1, 'kondisi' => 'baik',       'keterangan' => 'Jamannya Pa Ilham',                                     'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-003', 'nama_barang' => 'TV 52 Inch',                    'lokasi' => 'Lantai Atas', 'jumlah' => 1, 'kondisi' => 'rusak',      'keterangan' => 'Ketiban pelapon',                                       'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-004', 'nama_barang' => 'Bracket TV',                    'lokasi' => 'Lantai Atas', 'jumlah' => 1, 'kondisi' => 'baik',       'keterangan' => 'Beli saat kepengurusan Pa KW',                          'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-005', 'nama_barang' => 'Lemari Besi (Loker DPRa)',      'lokasi' => 'Lantai Atas', 'jumlah' => 1, 'kondisi' => 'baik',       'keterangan' => 'Beli saat kepengurusan Pa Imam Musanto',                'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-006', 'nama_barang' => 'Kursi Sofa',                    'lokasi' => 'Lantai Atas', 'jumlah' => 1, 'kondisi' => 'baik',       'keterangan' => 'Sumbangan dari Pa Baron',                               'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-007', 'nama_barang' => 'Gorden Roll',                   'lokasi' => 'Lantai Atas', 'jumlah' => 3, 'kondisi' => 'baik',       'keterangan' => 'Beli saat kepengurusan Pa KW',                          'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-008', 'nama_barang' => 'AC Portabel',                   'lokasi' => 'Lantai Atas', 'jumlah' => 1, 'kondisi' => 'baik',       'keterangan' => 'Beli dari Bu Ika',                                      'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-009', 'nama_barang' => 'Karpet Merah',                  'lokasi' => 'Lantai Atas', 'jumlah' => 1, 'kondisi' => 'kurang_baik','keterangan' => 'Beli saat kepengurusan Pa Imam Musanto',                'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-010', 'nama_barang' => 'LCD',                           'lokasi' => 'Lantai Atas', 'jumlah' => 2, 'kondisi' => 'rusak',      'keterangan' => 'Sumbangan dari ZS dan hibah dari DPD',                  'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-011', 'nama_barang' => 'Layar LCD',                     'lokasi' => 'Lantai Atas', 'jumlah' => 1, 'kondisi' => 'baik',       'keterangan' => 'Beli',                                                  'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-012', 'nama_barang' => 'Pemanas Air',                   'lokasi' => 'Lantai Atas', 'jumlah' => 1, 'kondisi' => 'baik',       'keterangan' => 'Sumbangan dari Pa Jito',                                'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-013', 'nama_barang' => 'Dispenser',                     'lokasi' => 'Lantai Atas', 'jumlah' => 1, 'kondisi' => 'baik',       'keterangan' => 'Sumbangan dari Pa Ilham',                               'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-014', 'nama_barang' => 'Server',                        'lokasi' => 'Lantai Atas', 'jumlah' => 1, 'kondisi' => 'baik',       'keterangan' => 'Dari Pa Hafid',                                         'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-015', 'nama_barang' => 'Meja',                          'lokasi' => 'Lantai Atas', 'jumlah' => 1, 'kondisi' => 'baik',       'keterangan' => 'Beli',                                                  'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-016', 'nama_barang' => 'Gayung',                        'lokasi' => 'Lantai Atas', 'jumlah' => 1, 'kondisi' => 'baik',       'keterangan' => 'Beli',                                                  'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-017', 'nama_barang' => 'Ember',                         'lokasi' => 'Lantai Atas', 'jumlah' => 1, 'kondisi' => 'baik',       'keterangan' => 'Beli',                                                  'created_at' => $now, 'updated_at' => $now],

            // ── LANTAI BAWAH (WARUNG) ────────────────────────────────────
            ['kode_aset' => 'AST-018', 'nama_barang' => 'AC',                            'lokasi' => 'Lantai Bawah (Warung)', 'jumlah' => 1, 'kondisi' => 'rusak', 'keterangan' => 'Hibah dari DPD',                               'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-019', 'nama_barang' => 'Meja Lipat',                    'lokasi' => 'Lantai Bawah (Warung)', 'jumlah' => 4, 'kondisi' => 'baik',  'keterangan' => 'Beli',                                          'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-020', 'nama_barang' => 'Kursi Lipat',                   'lokasi' => 'Lantai Bawah (Warung)', 'jumlah' => 9, 'kondisi' => 'baik',  'keterangan' => 'Beli',                                          'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-021', 'nama_barang' => 'Kursi Oranye',                  'lokasi' => 'Lantai Bawah (Warung)', 'jumlah' => 2, 'kondisi' => 'baik',  'keterangan' => 'Hibah dari Pa Baron',                           'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-022', 'nama_barang' => 'Kursi Kayu',                    'lokasi' => 'Lantai Bawah (Warung)', 'jumlah' => 4, 'kondisi' => 'baik',  'keterangan' => 'Beli',                                          'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-023', 'nama_barang' => 'Kursi Putih',                   'lokasi' => 'Lantai Bawah (Warung)', 'jumlah' => 2, 'kondisi' => 'baik',  'keterangan' => 'Beli',                                          'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-024', 'nama_barang' => 'Meja Bundar',                   'lokasi' => 'Lantai Bawah (Warung)', 'jumlah' => 2, 'kondisi' => 'baik',  'keterangan' => 'Beli',                                          'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-025', 'nama_barang' => 'Kipas Angin',                   'lokasi' => 'Lantai Bawah (Warung)', 'jumlah' => 2, 'kondisi' => 'baik',  'keterangan' => 'Beli',                                          'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-026', 'nama_barang' => 'Meja Café',                     'lokasi' => 'Lantai Bawah (Warung)', 'jumlah' => 1, 'kondisi' => 'baik',  'keterangan' => 'Beli',                                          'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-027', 'nama_barang' => 'Kulkas',                        'lokasi' => 'Lantai Bawah (Warung)', 'jumlah' => 1, 'kondisi' => 'baik',  'keterangan' => 'Hibah dari Pa Jito',                            'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-028', 'nama_barang' => 'Etalase',                       'lokasi' => 'Lantai Bawah (Warung)', 'jumlah' => 1, 'kondisi' => 'baik',  'keterangan' => 'Beli',                                          'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-029', 'nama_barang' => 'Jam Dinding',                   'lokasi' => 'Lantai Bawah (Warung)', 'jumlah' => 1, 'kondisi' => 'baik',  'keterangan' => 'Beli',                                          'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-030', 'nama_barang' => 'Lampu Kipas',                   'lokasi' => 'Lantai Bawah (Warung)', 'jumlah' => 4, 'kondisi' => 'baik',  'keterangan' => 'Beli',                                          'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-031', 'nama_barang' => 'Rak Buku',                      'lokasi' => 'Lantai Bawah (Warung)', 'jumlah' => 1, 'kondisi' => 'baik',  'keterangan' => 'Hibah dari Pa Sadulloh',                        'created_at' => $now, 'updated_at' => $now],
            ['kode_aset' => 'AST-032', 'nama_barang' => 'Rak Buat Sampel',               'lokasi' => 'Lantai Bawah (Warung)', 'jumlah' => 1, 'kondisi' => 'baik',  'keterangan' => 'Dari Pa Nasrul',                                'created_at' => $now, 'updated_at' => $now],
        ];

        $this->db->table('assets')->insertBatch($assets);
    }

    public function down()
    {
        $this->forge->dropTable('assets', true);
    }
}
