<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDefaultValueToCustomFormFields extends Migration
{
    public function up()
    {
        $fields = [
            'default_value' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'field_options',
            ],
        ];

        if ($this->db->tableExists('custom_form_fields')) {
            if (!$this->db->fieldExists('default_value', 'custom_form_fields')) {
                $this->forge->addColumn('custom_form_fields', $fields);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('custom_form_fields')) {
            if ($this->db->fieldExists('default_value', 'custom_form_fields')) {
                $this->forge->dropColumn('custom_form_fields', 'default_value');
            }
        }
    }
}
