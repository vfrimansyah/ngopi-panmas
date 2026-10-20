<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMerkModelFotoToAssets extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $fields = [];
        if (!$db->fieldExists('merk', 'assets')) {
            $fields['merk'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'nama_barang',
            ];
        }

        if (!$db->fieldExists('model', 'assets')) {
            $fields['model'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'merk',
            ];
        }

        if (!$db->fieldExists('foto', 'assets')) {
            $fields['foto'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'keterangan',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('assets', $fields);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        foreach (['merk', 'model', 'foto'] as $col) {
            if ($db->fieldExists($col, 'assets')) {
                $this->forge->dropColumn('assets', $col);
            }
        }
    }
}
