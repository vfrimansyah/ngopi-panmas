<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterTables extends Migration
{
    public function up()
    {
        // 1. regions_upa
        $this->forge->addField([
            'kode_upa' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'kelurahan_id' => [
                'type'       => 'INT',
                'null'       => true,
            ],
            'kecamatan_id' => [
                'type'       => 'INT',
                'null'       => true,
            ],
            'kota_kabupaten_id' => [
                'type'       => 'INT',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('kode_upa', true);
        $this->forge->addForeignKey('kelurahan_id', 'cms_region04_villages', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('kecamatan_id', 'cms_region03_districts', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('kota_kabupaten_id', 'cms_region02_cities', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('regions_upa');

        // 2. programs
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_program' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('programs');

        // 3. product_categories
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('product_categories');

        // 4. article_categories
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['artikel', 'berita'],
                'default'    => 'artikel',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('article_categories');

        // 5. mutabaah_items
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_item' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'tipe_input' => [
                'type'       => 'ENUM',
                'constraint' => ['boolean', 'angka', 'skala'],
                'default'    => 'boolean',
            ],
            'urutan' => [
                'type'       => 'INT',
                'default'    => 0,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('mutabaah_items');
    }

    public function down()
    {
        $this->forge->dropTable('mutabaah_items');
        $this->forge->dropTable('article_categories');
        $this->forge->dropTable('product_categories');
        $this->forge->dropTable('programs');
        $this->forge->dropTable('regions_upa');
    }
}
