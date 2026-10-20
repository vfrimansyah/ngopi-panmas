<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyProfileHistoriesAddFields extends Migration
{
    public function up()
    {
        // Add jenjang to member_educations
        $this->forge->addColumn('member_educations', [
            'jenjang' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'member_id'
            ]
        ]);

        // Add bidang_pekerjaan, pendapatan_range, alamat_kerja to member_work_experiences
        $this->forge->addColumn('member_work_experiences', [
            'bidang_pekerjaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'nama_perusahaan'
            ],
            'pendapatan_range' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'bidang_pekerjaan'
            ],
            'alamat_kerja' => [
                'type' => 'TEXT',
                'null' => true,
                'after'      => 'pendapatan_range'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('member_work_experiences', 'alamat_kerja');
        $this->forge->dropColumn('member_work_experiences', 'pendapatan_range');
        $this->forge->dropColumn('member_work_experiences', 'bidang_pekerjaan');
        $this->forge->dropColumn('member_educations', 'jenjang');
    }
}
