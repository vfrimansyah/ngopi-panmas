<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailPhoneBirthplaceToMembers extends Migration
{
    public function up()
    {
        $fields = [
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'nama_lengkap',
            ],
            'handphone' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'email',
            ],
            'tempat_lahir' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'jenis_kelamin',
            ],
        ];
        $this->forge->addColumn('members', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('members', ['email', 'handphone', 'tempat_lahir']);
    }
}
