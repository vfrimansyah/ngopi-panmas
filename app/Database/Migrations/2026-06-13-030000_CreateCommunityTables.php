<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommunityTables extends Migration
{
    public function up()
    {
        // 1. member_products (UMKM)
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'kategori_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'nama_produk' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'foto_urls' => [
                'type' => 'TEXT', // Will store JSON array of URLs
                'null' => true,
            ],
            'kontak_whatsapp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'link_eksternal' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'published', 'rejected', 'nonaktif'],
                'default'    => 'pending',
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
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kategori_id', 'product_categories', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('member_products');

        // 2. articles (CMS)
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['artikel', 'berita'],
                'default'    => 'artikel',
            ],
            'kategori_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'konten' => [
                'type' => 'LONGTEXT',
            ],
            'foto_cover' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'author_member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'pending_review', 'published', 'archived'],
                'default'    => 'draft',
            ],
            'views_count' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'published_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addUniqueKey('slug');
        $this->forge->addForeignKey('kategori_id', 'article_categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('author_member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('articles');

        // 3. events
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
            ],
            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'tanggal_mulai' => [
                'type' => 'DATETIME',
            ],
            'tanggal_selesai' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'kuota' => [
                'type' => 'INT',
                'null' => true,
            ],
            'poster_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'published', 'selesai', 'dibatalkan'],
                'default'    => 'draft',
            ],
            'created_by' => [
                'type'     => 'BIGINT',
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
        $this->forge->addForeignKey('created_by', 'members', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('events');

        // 4. event_registrations
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'event_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'status_kehadiran' => [
                'type'       => 'ENUM',
                'constraint' => ['terdaftar', 'hadir', 'tidak_hadir', 'batal'],
                'default'    => 'terdaftar',
            ],
            'registered_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('event_registrations');

        // 5. member_achievements
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'judul_prestasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
            ],
            'tanggal_prestasi' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'foto_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_featured' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'featured_period' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'created_by' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'members', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('member_achievements');

        // 6. mutabaah_logs
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('mutabaah_logs');

        // 7. mutabaah_log_items
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'mutabaah_log_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'mutabaah_item_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'nilai' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('mutabaah_log_id', 'mutabaah_logs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mutabaah_item_id', 'mutabaah_items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('mutabaah_log_items');
    }

    public function down()
    {
        $this->forge->dropTable('mutabaah_log_items');
        $this->forge->dropTable('mutabaah_logs');
        $this->forge->dropTable('member_achievements');
        $this->forge->dropTable('event_registrations');
        $this->forge->dropTable('events');
        $this->forge->dropTable('articles');
        $this->forge->dropTable('member_products');
    }
}
