<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAuthorizedMembersToLembagaProfiles extends Migration
{
    public function up()
    {
        $fields = [
            'authorized_members' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'file_susunan_pengurus',
                'comment'    => 'JSON array of member IDs authorized to create proposals',
            ],
            'is_public' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'authorized_members',
                'comment'    => '1 if open to all members to create proposal, 0 if restricted',
            ],
        ];

        if (!$this->db->fieldExists('authorized_members', 'lembaga_profiles')) {
            $this->forge->addColumn('lembaga_profiles', [
                'authorized_members' => $fields['authorized_members'],
            ]);
        }

        if (!$this->db->fieldExists('is_public', 'lembaga_profiles')) {
            $this->forge->addColumn('lembaga_profiles', [
                'is_public' => $fields['is_public'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('is_public', 'lembaga_profiles')) {
            $this->forge->dropColumn('lembaga_profiles', 'is_public');
        }
        if ($this->db->fieldExists('authorized_members', 'lembaga_profiles')) {
            $this->forge->dropColumn('lembaga_profiles', 'authorized_members');
        }
    }
}
