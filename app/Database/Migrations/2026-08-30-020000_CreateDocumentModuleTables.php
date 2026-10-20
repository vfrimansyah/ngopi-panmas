<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocumentModuleTables extends Migration
{
    public function up()
    {
        // ── 1. Table: document_categories ───────────────────────────────────
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'fa-folder',
            ],
            'warna' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => '#3b82f6',
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('document_categories', true);

        // ── 2. Table: documents ─────────────────────────────────────────────
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kategori_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'nomor_dokumen' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'file_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'file_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'file_size' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'default'    => 0,
            ],
            'tahun' => [
                'type'       => 'YEAR',
                'null'       => true,
            ],
            'tanggal_dokumen' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'sifat_dokumen' => [
                'type'       => 'ENUM',
                'constraint' => ['publik', 'internal', 'rahasia'],
                'default'    => 'internal',
            ],
            'bidang_kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'institusi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'DPC',
            ],
            'uploader_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'uploader_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'views_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'downloads_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'is_pinned' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('kategori_id');
        $this->forge->addKey('sifat_dokumen');
        $this->forge->addKey('bidang_kode');
        $this->forge->createTable('documents', true);

        // ── Seed Categories ─────────────────────────────────────────────────
        $db = \Config\Database::connect();
        $categories = [
            [
                'nama_kategori' => 'Surat Keputusan (SK)',
                'slug'          => 'surat-keputusan-sk',
                'deskripsi'     => 'SK Pengangkatan Pengurus DPC, DPRa, dan Struktur Panitia',
                'icon'          => 'fa-stamp',
                'warna'         => '#dc2626',
                'sort_order'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'Surat Masuk & Keluar',
                'slug'          => 'surat-masuk-keluar',
                'deskripsi'     => 'Arsip korespondensi, undangan resmi, surat edaran, dan permohonan',
                'icon'          => 'fa-envelope-open-text',
                'warna'         => '#2563eb',
                'sort_order'    => 2,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'Program Kerja & Perencanaan',
                'slug'          => 'program-kerja-perencanaan',
                'deskripsi'     => 'Matriks rencana program kerja, target KPI, dan RAB bidang DPC',
                'icon'          => 'fa-list-check',
                'warna'         => '#10b981',
                'sort_order'    => 3,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'Proposal & LPJ Kegiatan',
                'slug'          => 'proposal-lpj-kegiatan',
                'deskripsi'     => 'Pengajuan proposal acara, laporan pertanggungjawaban & dokumentasi',
                'icon'          => 'fa-file-invoice',
                'warna'         => '#f59e0b',
                'sort_order'    => 4,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'Materi & Modul Pelatihan',
                'slug'          => 'materi-modul-pelatihan',
                'deskripsi'     => 'Buku saku kader, modul SAKTI, materi kaderisasi, dan presentasi UPA',
                'icon'          => 'fa-book-open-reader',
                'warna'         => '#8b5cf6',
                'sort_order'    => 5,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'Template & Format Administrasi',
                'slug'          => 'template-format-administrasi',
                'deskripsi'     => 'Template resmi kop surat, formulir pendaftaran, dan format evaluasi',
                'icon'          => 'fa-file-lines',
                'warna'         => '#06b6d4',
                'sort_order'    => 6,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'Laporan Keuangan & Audit',
                'slug'          => 'laporan-keuangan-audit',
                'deskripsi'     => 'Laporan arus kas bulanan, rekapitulasi iuran, dan laporan audit tahunan',
                'icon'          => 'fa-file-invoice-dollar',
                'warna'         => '#059669',
                'sort_order'    => 7,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'Dokumen Pemilu & Saksi',
                'slug'          => 'dokumen-pemilu-saksi',
                'deskripsi'     => 'Data TPS, panduan saksi, rekapitulasi perolehan suara pemilu',
                'icon'          => 'fa-square-poll-vertical',
                'warna'         => '#ec4899',
                'sort_order'    => 8,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        $db->table('document_categories')->insertBatch($categories);
    }

    public function down()
    {
        $this->forge->dropTable('documents', true);
        $this->forge->dropTable('document_categories', true);
    }
}
