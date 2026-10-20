<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKegiatanAbsensiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kegiatan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'member_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['hadir', 'izin', 'sakit', 'alfa'],
                'default'    => 'hadir',
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'recorded_by' => [
                'type' => 'INT',
                'null' => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['kegiatan_id', 'member_id'], 'uk_kegiatan_member');
        $this->forge->addKey('member_id', false, false, 'idx_member');
        $this->forge->addKey('status', false, false, 'idx_status');
        $this->forge->createTable('kegiatan_absensi', true);
    }

    public function down()
    {
        $this->forge->dropTable('kegiatan_absensi', true);
    }
}
