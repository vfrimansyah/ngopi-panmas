<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMemberIuranPaymentsTable extends Migration
{
    public function up()
    {
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
            'tahun' => [
                'type'       => 'INT',
                'constraint' => 4,
                'unsigned'   => true,
            ],
            'bulan' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'unsigned'   => true,
            ],
            'status_itb' => [
                'type'       => 'ENUM',
                'constraint' => ['belum', 'lunas'],
                'default'    => 'belum',
            ],
            'nominal_itb' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'status_iwb' => [
                'type'       => 'ENUM',
                'constraint' => ['belum', 'lunas'],
                'default'    => 'belum',
            ],
            'nominal_iwb' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'status_infaq' => [
                'type'       => 'ENUM',
                'constraint' => ['belum', 'lunas'],
                'default'    => 'belum',
            ],
            'nominal_infaq' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'tanggal_bayar' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addUniqueKey(['member_id', 'tahun', 'bulan']);
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_iuran_payments');
    }

    public function down()
    {
        $this->forge->dropTable('member_iuran_payments');
    }
}
