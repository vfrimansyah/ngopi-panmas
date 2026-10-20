<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobFieldsTable extends Migration
{
    public function up()
    {
        // 1. Create job_fields table
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->createTable('job_fields');

        // Seed initial job fields
        $db = \Config\Database::connect();
        $builder = $db->table('job_fields');
        $builder->insertBatch([
            ['name' => 'IT / Teknologi Informasi'],
            ['name' => 'Kesehatan / Medis'],
            ['name' => 'Pendidikan / Akademis'],
            ['name' => 'Keuangan / Perbankan'],
            ['name' => 'Perdagangan / Retail / E-commerce'],
            ['name' => 'Pertanian / Perkebunan / Peternakan'],
            ['name' => 'Konstruksi / Real Estate'],
            ['name' => 'Transportasi / Logistik'],
            ['name' => 'Seni / Kreatif / Media / Hiburan'],
            ['name' => 'Pemerintahan / Pelayanan Publik / Militer'],
            ['name' => 'Hukum / Legal'],
            ['name' => 'Manufaktur / Pabrik'],
            ['name' => 'Lainnya']
        ]);

        // 2. Modify member_work_experiences table
        // Add bidang_pekerjaan_id column
        $this->forge->addColumn('member_work_experiences', [
            'bidang_pekerjaan_id' => [
                'type'       => 'BIGINT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'nama_perusahaan'
            ]
        ]);

        // Add foreign key constraint
        $db->query("ALTER TABLE member_work_experiences ADD CONSTRAINT fk_work_experiences_job_field FOREIGN KEY (bidang_pekerjaan_id) REFERENCES job_fields(id) ON DELETE SET NULL");

        // Drop the old varchar bidang_pekerjaan column
        if ($db->fieldExists('bidang_pekerjaan', 'member_work_experiences')) {
            $this->forge->dropColumn('member_work_experiences', 'bidang_pekerjaan');
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        // Drop foreign key constraint
        $db->query("ALTER TABLE member_work_experiences DROP FOREIGN KEY fk_work_experiences_job_field");

        // Add back bidang_pekerjaan column
        $this->forge->addColumn('member_work_experiences', [
            'bidang_pekerjaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'nama_perusahaan'
            ]
        ]);

        // Drop bidang_pekerjaan_id column
        $this->forge->dropColumn('member_work_experiences', 'bidang_pekerjaan_id');

        // Drop job_fields table
        $this->forge->dropTable('job_fields');
    }
}
