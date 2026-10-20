<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPembimbingToRegionsUpa extends Migration
{
    public function up()
    {
        $fields = [
            'pembimbing_id' => [
                'type'       => 'BIGINT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'kode_upa', // Place it right after kode_upa
            ],
        ];
        
        $this->forge->addColumn('regions_upa', $fields);
        
        // Add foreign key constraint
        $this->db->query("ALTER TABLE regions_upa ADD CONSTRAINT fk_regions_upa_pembimbing FOREIGN KEY (pembimbing_id) REFERENCES members(id) ON DELETE SET NULL ON UPDATE CASCADE");
    }

    public function down()
    {
        // Drop foreign key first
        $this->db->query("ALTER TABLE regions_upa DROP FOREIGN KEY fk_regions_upa_pembimbing");
        $this->forge->dropColumn('regions_upa', 'pembimbing_id');
    }
}
