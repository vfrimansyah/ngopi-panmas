<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGalleryModuleTables extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Table gallery_albums
        if (!$db->tableExists('gallery_albums')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'nama_album' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'slug' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 160,
                    'unique'     => true,
                ],
                'deskripsi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'cover_image' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'tanggal_kegiatan' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'lokasi' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                ],
                'sort_order' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 1,
                ],
                'created_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
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
            $this->forge->createTable('gallery_albums');

            // Seed initial albums
            $db->table('gallery_albums')->insertBatch([
                [
                    'nama_album'       => 'Dokumentasi Kegiatan DPC PKS Panmas',
                    'slug'             => 'dokumentasi-kegiatan-dpc-pks-panmas',
                    'deskripsi'        => 'Kumpulan foto dan video rapat, konsolidasi, dan kegiatan resmi pengurus DPC.',
                    'tanggal_kegiatan' => date('Y-m-d'),
                    'lokasi'           => 'Sekretariat DPC Panmas',
                    'sort_order'       => 1,
                    'created_by'       => 1,
                    'created_at'       => date('Y-m-d H:i:s'),
                    'updated_at'       => date('Y-m-d H:i:s'),
                ],
                [
                    'nama_album'       => 'Bakti Sosial & Pelayanan Masyarakat',
                    'slug'             => 'bakti-sosial-pelayanan-masyarakat',
                    'deskripsi'        => 'Dokumentasi kegiatan bakti sosial, santunan yatim, posko kesehatan, dan layanan warga.',
                    'tanggal_kegiatan' => date('Y-m-d'),
                    'lokasi'           => 'Kecamatan Pancoran Mas',
                    'sort_order'       => 2,
                    'created_by'       => 1,
                    'created_at'       => date('Y-m-d H:i:s'),
                    'updated_at'       => date('Y-m-d H:i:s'),
                ],
                [
                    'nama_album'       => 'Pendidikan Politik & Kaderisasi',
                    'slug'             => 'pendidikan-politik-kaderisasi',
                    'deskripsi'        => 'Pelatihan kader, seminar kebangsaan, dan upgrading pengurus se-Pancoran Mas.',
                    'tanggal_kegiatan' => date('Y-m-d'),
                    'lokasi'           => 'Aula DPC Panmas',
                    'sort_order'       => 3,
                    'created_by'       => 1,
                    'created_at'       => date('Y-m-d H:i:s'),
                    'updated_at'       => date('Y-m-d H:i:s'),
                ],
                [
                    'nama_album'       => 'Kegiatan DPRa & Ranting',
                    'slug'             => 'kegiatan-dpra-ranting',
                    'deskripsi'        => 'Dokumentasi silaturahim dan kegiatan tingkat kelurahan/DPRa di 6 kelurahan.',
                    'tanggal_kegiatan' => date('Y-m-d'),
                    'lokasi'           => 'Wilayah DPRa Panmas',
                    'sort_order'       => 4,
                    'created_by'       => 1,
                    'created_at'       => date('Y-m-d H:i:s'),
                    'updated_at'       => date('Y-m-d H:i:s'),
                ],
            ]);
        }

        // 2. Table gallery_items
        if (!$db->tableExists('gallery_items')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'album_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'tipe' => [
                    'type'       => 'ENUM',
                    'constraint' => ['image', 'video'],
                    'default'    => 'image',
                ],
                'judul' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'slug' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'deskripsi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'file_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'file_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'file_size' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'null'       => true,
                ],
                'video_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['youtube', 'upload', 'link'],
                    'null'       => true,
                ],
                'video_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                ],
                'video_id' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'thumbnail_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'tanggal_media' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'lokasi' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                ],
                'kredit' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                ],
                'is_featured' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'views_count' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'created_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
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
            $this->forge->addKey('album_id');
            $this->forge->addKey('tipe');
            $this->forge->createTable('gallery_items');

            // Seed sample YouTube video & sample items
            $db->table('gallery_items')->insertBatch([
                [
                    'album_id'       => 1,
                    'tipe'           => 'video',
                    'judul'          => 'Kompilasi Semangat Kebangsaan DPC PKS Pancoran Mas',
                    'slug'           => 'kompilasi-semangat-kebangsaan-dpc-pks-pancoran-mas',
                    'deskripsi'      => 'Video rangkuman kegiatan dan konsolidasi kader PKS se-Kecamatan Pancoran Mas.',
                    'video_type'     => 'youtube',
                    'video_url'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'video_id'       => 'dQw4w9WgXcQ',
                    'thumbnail_path' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
                    'tanggal_media'  => date('Y-m-d'),
                    'lokasi'         => 'Pancoran Mas, Depok',
                    'kredit'         => 'Tim Media Komdigi',
                    'is_featured'    => 1,
                    'views_count'    => 120,
                    'created_by'     => 1,
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('gallery_items', true);
        $this->forge->dropTable('gallery_albums', true);
    }
}
