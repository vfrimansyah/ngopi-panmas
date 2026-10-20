<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddClosedAtToCustomForms extends Migration
{
    public function up()
    {
        $fields = [
            'closed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'is_active',
            ],
        ];

        if ($this->db->tableExists('custom_forms')) {
            if (!$this->db->fieldExists('closed_at', 'custom_forms')) {
                $this->forge->addColumn('custom_forms', $fields);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('custom_forms')) {
            if ($this->db->fieldExists('closed_at', 'custom_forms')) {
                $this->forge->dropColumn('custom_forms', 'closed_at');
            }
        }
    }
}
