<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMemberProfileHistories extends Migration
{
    public function up()
    {
        // 1. member_addresses
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'label' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'alamat_lengkap' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'rt' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'rw' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'kota_kabupaten_id' => [
                'type'       => 'INT',
                'null'       => true,
            ],
            'kecamatan_id' => [
                'type'       => 'INT',
                'null'       => true,
            ],
            'kelurahan_id' => [
                'type'       => 'INT',
                'null'       => true,
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
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kota_kabupaten_id', 'cms_region02_cities', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('kecamatan_id', 'cms_region03_districts', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('kelurahan_id', 'cms_region04_villages', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('member_addresses');

        // 2. member_educations
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'nama_institusi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'rentang_tahun' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
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
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_educations');

        // 3. member_work_experiences
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'jabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'nama_perusahaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'rentang_tahun' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
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
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_work_experiences');

        // 4. member_skills
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'nama_keahlian' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
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
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_skills');

        // 5. Add rentang_tahun to member_organizations
        $db = \Config\Database::connect();
        if (!$db->fieldExists('rentang_tahun', 'member_organizations')) {
            $this->forge->addColumn('member_organizations', [
                'rentang_tahun' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'jabatan'
                ]
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        if ($db->fieldExists('rentang_tahun', 'member_organizations')) {
            $this->forge->dropColumn('member_organizations', 'rentang_tahun');
        }
        $this->forge->dropTable('member_skills');
        $this->forge->dropTable('member_work_experiences');
        $this->forge->dropTable('member_educations');
        $this->forge->dropTable('member_addresses');
    }
}
