<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Buat tabel admin_role_permissions
 * Menyimpan konfigurasi permissions CRUD per role per halaman admin.
 * Role sistem: admin (Super Admin), bph, kaderisasi, seksi, user
 */
class CreateRolePermissionsTable extends Migration
{
    public function up()
    {
        // Buat tabel admin_role_permissions
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'role_slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'page_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'can_create' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'can_read' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'can_update' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'can_delete' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
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
        $this->forge->addKey(['role_slug', 'page_key']);
        $this->forge->createTable('admin_role_permissions');

        // Seed default permissions
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // Daftar halaman admin
        $pages = [
            'dashboard', 'members', 'match', 'products',
            'news', 'events', 'users', 'roles', 'menus',
            'settings', 'pemilu',
        ];

        // Konfigurasi default per role
        $defaults = [
            // Super Admin — akses penuh ke semua halaman
            'admin' => array_fill_keys($pages, ['create' => 1, 'read' => 1, 'update' => 1, 'delete' => 1]),

            // BPH — kelola konten & anggota, tidak bisa kelola user/role/settings
            'bph' => [
                'dashboard' => ['create' => 0, 'read' => 1, 'update' => 0, 'delete' => 0],
                'members'   => ['create' => 1, 'read' => 1, 'update' => 1, 'delete' => 1],
                'match'     => ['create' => 1, 'read' => 1, 'update' => 1, 'delete' => 1],
                'products'  => ['create' => 1, 'read' => 1, 'update' => 1, 'delete' => 1],
                'news'      => ['create' => 1, 'read' => 1, 'update' => 1, 'delete' => 1],
                'events'    => ['create' => 1, 'read' => 1, 'update' => 1, 'delete' => 1],
                'pemilu'    => ['create' => 0, 'read' => 1, 'update' => 0, 'delete' => 0],
                'users'     => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                'roles'     => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                'menus'     => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                'settings'  => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
            ],

            // Kaderisasi — kelola anggota & pencocokan
            'kaderisasi' => [
                'dashboard' => ['create' => 0, 'read' => 1, 'update' => 0, 'delete' => 0],
                'members'   => ['create' => 1, 'read' => 1, 'update' => 1, 'delete' => 0],
                'match'     => ['create' => 1, 'read' => 1, 'update' => 1, 'delete' => 0],
                'products'  => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                'news'      => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                'events'    => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                'pemilu'    => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                'users'     => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                'roles'     => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                'menus'     => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                'settings'  => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
            ],

            // Seksi — akses baca saja pada beberapa halaman
            'seksi' => [
                'dashboard' => ['create' => 0, 'read' => 1, 'update' => 0, 'delete' => 0],
                'members'   => ['create' => 0, 'read' => 1, 'update' => 0, 'delete' => 0],
                'match'     => ['create' => 0, 'read' => 1, 'update' => 0, 'delete' => 0],
                'products'  => ['create' => 0, 'read' => 1, 'update' => 0, 'delete' => 0],
                'news'      => ['create' => 0, 'read' => 1, 'update' => 0, 'delete' => 0],
                'events'    => ['create' => 0, 'read' => 1, 'update' => 0, 'delete' => 0],
                'pemilu'    => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                'users'     => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                'roles'     => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                'menus'     => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                'settings'  => ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
            ],

            // User — tidak ada akses admin
            'user' => array_fill_keys($pages, ['create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0]),
        ];

        $rows = [];
        foreach ($defaults as $roleSlug => $pagePerms) {
            foreach ($pagePerms as $pageKey => $perms) {
                $rows[] = [
                    'role_slug'  => $roleSlug,
                    'page_key'   => $pageKey,
                    'can_create' => $perms['create'],
                    'can_read'   => $perms['read'],
                    'can_update' => $perms['update'],
                    'can_delete' => $perms['delete'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        $db->table('admin_role_permissions')->insertBatch($rows);
    }

    public function down()
    {
        $this->forge->dropTable('admin_role_permissions', true);
    }
}
