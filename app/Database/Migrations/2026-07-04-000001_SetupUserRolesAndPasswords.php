<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Setup User Roles & Default Passwords
 *
 * 1. Menambahkan kolom 'must_reset_password' ke tabel users (Shield)
 * 2. Memberikan role 'user' ke semua user existing yang belum punya grup
 * 3. Membuat user account untuk semua anggota yang belum punya akun (account_status = 'no_account')
 *    dengan password default "P4nm4s" dan force_password_reset = true
 */
class SetupUserRolesAndPasswords extends Migration
{
    public function up()
    {
        // 1. Tambah kolom force_password_reset ke tabel users Shield (jika belum ada)
        $db = \Config\Database::connect();
        
        // Cek apakah kolom sudah ada di tabel users
        $fields = $db->getFieldNames('users');
        if (!in_array('force_password_reset', $fields)) {
            $this->forge->addColumn('users', [
                'force_password_reset' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'after'      => 'last_active',
                ],
            ]);
        }

        // 2. Assign role 'user' ke semua user yang belum punya grup
        $usersWithoutGroup = $db->query("
            SELECT u.id FROM users u
            LEFT JOIN auth_groups_users agu ON agu.user_id = u.id
            WHERE agu.user_id IS NULL
        ")->getResultArray();

        foreach ($usersWithoutGroup as $user) {
            $db->table('auth_groups_users')->insert([
                'user_id'    => $user['id'],
                'group'      => 'user',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('users');
        if (in_array('force_password_reset', $fields)) {
            $this->forge->dropColumn('users', 'force_password_reset');
        }
    }
}
