<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDiskusiAndIdeTables extends Migration
{
    public function up()
    {
        // 1. Modify articles table tipe column to VARCHAR(50) to support 'artikel', 'berita', 'ide'
        $this->forge->modifyColumn('articles', [
            'tipe' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'artikel',
            ],
        ]);

        // 2. Create discussions table
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
            'kategori_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'konten' => [
                'type' => 'LONGTEXT',
            ],
            'author_member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'views_count' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'upvotes_count' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'replies_count' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['published', 'closed', 'archived'],
                'default'    => 'published',
            ],
            'is_pinned' => [
                'type'    => 'TINYINT',
                'default' => 0,
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
        $this->forge->createTable('discussions', true);

        // 3. Create discussion_replies table
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'discussion_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'parent_reply_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'konten' => [
                'type' => 'TEXT',
            ],
            'upvotes_count' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'is_best_answer' => [
                'type'    => 'TINYINT',
                'default' => 0,
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
        $this->forge->addForeignKey('discussion_id', 'discussions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('discussion_replies', true);
    }

    public function down()
    {
        $this->forge->dropTable('discussion_replies', true);
        $this->forge->dropTable('discussions', true);
    }
}
