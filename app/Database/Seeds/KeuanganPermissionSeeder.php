<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KeuanganPermissionSeeder extends Seeder
{
    public function run()
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // Ambil semua role_slug unik yang ada di admin_role_permissions atau custom roles
        $roles = $db->query("SELECT DISTINCT role_slug FROM admin_role_permissions")->getResultArray();

        // Tambah role default jika belum ada di tabel
        $allRoleSlugs = array_column($roles, 'role_slug');
        foreach (['admin', 'bph', 'kaderisasi', 'seksi', 'user'] as $sysRole) {
            if (!in_array($sysRole, $allRoleSlugs)) {
                $allRoleSlugs[] = $sysRole;
            }
        }

        foreach ($allRoleSlugs as $slug) {
            $exists = $db->table('admin_role_permissions')
                ->where('role_slug', $slug)
                ->where('page_key', 'keuangan')
                ->countAllResults();

            if (!$exists) {
                $canCRUD = in_array($slug, ['admin', 'bph']) ? 1 : 0;
                $db->table('admin_role_permissions')->insert([
                    'role_slug'  => $slug,
                    'page_key'   => 'keuangan',
                    'can_create' => $canCRUD,
                    'can_read'   => $canCRUD,
                    'can_update' => $canCRUD,
                    'can_delete' => $canCRUD,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        echo "Keuangan permissions seeded successfully!\n";
    }
}
