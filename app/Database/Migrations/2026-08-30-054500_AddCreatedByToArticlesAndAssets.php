<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCreatedByToArticlesAndAssets extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Add created_by to articles
        if (!$db->fieldExists('created_by', 'articles')) {
            $this->forge->addColumn('articles', [
                'created_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'after'      => 'author_member_id',
                ],
            ]);
            $db->query("UPDATE articles SET created_by = 1 WHERE created_by IS NULL");
        }

        // 2. Add created_by to assets
        if (!$db->fieldExists('created_by', 'assets')) {
            $this->forge->addColumn('assets', [
                'created_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'after'      => 'penanggung_jawab',
                ],
            ]);
            $db->query("UPDATE assets SET created_by = 1 WHERE created_by IS NULL");
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if ($db->fieldExists('created_by', 'articles')) {
            $this->forge->dropColumn('articles', 'created_by');
        }

        if ($db->fieldExists('created_by', 'assets')) {
            $this->forge->dropColumn('assets', 'created_by');
        }
    }
}
