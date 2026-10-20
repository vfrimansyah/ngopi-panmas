<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Clear existing menu tables to avoid duplicates
        $db->table('settings_menu_roles')->truncate();
        $db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $db->table('settings_menus')->truncate();
        $db->query("SET FOREIGN_KEY_CHECKS = 1;");

        $allRoles  = ['admin', 'bph', 'kaderisasi', 'seksi', 'user'];
        $staffRoles= ['admin', 'bph', 'kaderisasi', 'seksi'];
        $bphRoles  = ['admin', 'bph'];
        $adminRoles= ['admin'];

        // Seed data
        $menus = [
            // 1. Dashboard
            [
                'title'      => 'Dashboard',
                'icon'       => 'fa-solid fa-gauge',
                'url'        => 'admin/dashboard',
                'sort_order' => 1,
                'roles'      => $staffRoles,
            ],
            // 2. Anggota
            [
                'title'      => 'Anggota',
                'icon'       => 'fa-solid fa-users',
                'url'        => 'admin/members',
                'sort_order' => 2,
                'roles'      => $staffRoles,
            ],
            // 3. Produk Group (Parent)
            [
                'title'      => 'Produk',
                'icon'       => 'fa-solid fa-store',
                'url'        => null,
                'sort_order' => 3,
                'roles'      => $staffRoles,
                'children'   => [
                    [
                        'title'      => 'Daftar Produk',
                        'icon'       => 'fa-solid fa-list',
                        'url'        => 'admin/products',
                        'sort_order' => 1,
                        'roles'      => $staffRoles,
                    ],
                    [
                        'title'      => 'Kategori Produk',
                        'icon'       => 'fa-solid fa-tags',
                        'url'        => 'admin/products/categories',
                        'sort_order' => 2,
                        'roles'      => $staffRoles,
                    ],
                ],
            ],
            // 4. Berita Group (Parent)
            [
                'title'      => 'Berita',
                'icon'       => 'fa-solid fa-newspaper',
                'url'        => null,
                'sort_order' => 4,
                'roles'      => $staffRoles,
                'children'   => [
                    [
                        'title'      => 'Daftar Berita',
                        'icon'       => 'fa-solid fa-list',
                        'url'        => 'admin/news',
                        'sort_order' => 1,
                        'roles'      => $staffRoles,
                    ],
                    [
                        'title'      => 'Kategori Berita',
                        'icon'       => 'fa-solid fa-tags',
                        'url'        => 'admin/news/categories',
                        'sort_order' => 2,
                        'roles'      => $staffRoles,
                    ],
                ],
            ],
            // 5. Event / Agenda
            [
                'title'      => 'Event / Agenda',
                'icon'       => 'fa-solid fa-calendar',
                'url'        => 'admin/events',
                'sort_order' => 5,
                'roles'      => $staffRoles,
            ],
            // 6. Pemilu Group (Parent)
            [
                'title'      => 'Pemilu',
                'icon'       => 'fa-solid fa-check-to-slot',
                'url'        => null,
                'sort_order' => 6,
                'roles'      => $staffRoles,
                'children'   => [
                    [
                        'title'      => 'Partai Politik',
                        'icon'       => 'fa-solid fa-flag',
                        'url'        => 'admin/pemilu/partai',
                        'sort_order' => 1,
                        'roles'      => $staffRoles,
                    ],
                    [
                        'title'      => 'Peserta Pemilu',
                        'icon'       => 'fa-solid fa-list-ol',
                        'url'        => 'admin/pemilu/peserta',
                        'sort_order' => 2,
                        'roles'      => $staffRoles,
                    ],
                    [
                        'title'      => 'Data TPS',
                        'icon'       => 'fa-solid fa-check-to-slot',
                        'url'        => 'admin/pemilu/tps',
                        'sort_order' => 3,
                        'roles'      => $staffRoles,
                    ],
                    [
                        'title'      => 'Hasil Pemilu',
                        'icon'       => 'fa-solid fa-chart-simple',
                        'url'        => 'admin/pemilu/hasil',
                        'sort_order' => 4,
                        'roles'      => $staffRoles,
                    ],
                ],
            ],
            // 7. Keuangan Group (Parent)
            [
                'title'      => 'Keuangan',
                'icon'       => 'fa-solid fa-coins',
                'url'        => null,
                'sort_order' => 7,
                'roles'      => $bphRoles,
                'children'   => [
                    [
                        'title'      => 'Dashboard',
                        'icon'       => 'fa-solid fa-chart-pie',
                        'url'        => 'admin/keuangan',
                        'sort_order' => 1,
                        'roles'      => $bphRoles,
                    ],
                    [
                        'title'      => 'Chart of Accounts',
                        'icon'       => 'fa-solid fa-sitemap',
                        'url'        => 'admin/keuangan/accounts',
                        'sort_order' => 2,
                        'roles'      => $bphRoles,
                    ],
                    [
                        'title'      => 'Transaksi',
                        'icon'       => 'fa-solid fa-right-left',
                        'url'        => 'admin/keuangan/transaksi',
                        'sort_order' => 3,
                        'roles'      => $bphRoles,
                    ],
                    [
                        'title'      => 'Jurnal Umum',
                        'icon'       => 'fa-solid fa-book-open',
                        'url'        => 'admin/keuangan/jurnal',
                        'sort_order' => 4,
                        'roles'      => $bphRoles,
                    ],
                    [
                        'title'      => 'Rekening & Kas',
                        'icon'       => 'fa-solid fa-building-columns',
                        'url'        => 'admin/keuangan/rekening',
                        'sort_order' => 5,
                        'roles'      => $bphRoles,
                    ],
                    [
                        'title'      => 'Laporan Laba Rugi',
                        'icon'       => 'fa-solid fa-file-invoice',
                        'url'        => 'admin/keuangan/laporan',
                        'sort_order' => 6,
                        'roles'      => $bphRoles,
                    ],
                ],
            ],
            // 8. Pengaturan Group (Parent) - Admin Only
            [
                'title'      => 'Pengaturan',
                'icon'       => 'fa-solid fa-gear',
                'url'        => null,
                'sort_order' => 8,
                'roles'      => $adminRoles,
                'children'   => [
                    [
                        'title'      => 'Manajemen User',
                        'icon'       => 'fa-solid fa-user-gear',
                        'url'        => 'admin/users',
                        'sort_order' => 1,
                        'roles'      => $adminRoles,
                    ],
                    [
                        'title'      => 'Page Role',
                        'icon'       => 'fa-solid fa-lock',
                        'url'        => 'admin/roles',
                        'sort_order' => 2,
                        'roles'      => $adminRoles,
                    ],
                    [
                        'title'      => 'Manajemen Menu',
                        'icon'       => 'fa-solid fa-list-check',
                        'url'        => 'admin/menus',
                        'sort_order' => 3,
                        'roles'      => $adminRoles,
                    ],
                ],
            ],
            // 9. Lihat Website
            [
                'title'      => 'Lihat Website',
                'icon'       => 'fa-solid fa-globe',
                'url'        => '',
                'sort_order' => 9,
                'roles'      => $allRoles,
            ],
        ];

        // Insert recursively
        foreach ($menus as $m) {
            $parentId = $this->insertMenu($db, null, $m);
            if (isset($m['children'])) {
                foreach ($m['children'] as $child) {
                    $this->insertMenu($db, $parentId, $child);
                }
            }
        }

        echo "Menus successfully seeded!\n";
    }

    private function insertMenu($db, $parentId, $data)
    {
        $now = date('Y-m-d H:i:s');
        $db->table('settings_menus')->insert([
            'parent_id'  => $parentId,
            'title'      => $data['title'],
            'icon'       => $data['icon'],
            'url'        => $data['url'],
            'sort_order' => $data['sort_order'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $menuId = $db->insertID();

        // Bind roles
        if (isset($data['roles'])) {
            foreach ($data['roles'] as $role) {
                $db->table('settings_menu_roles')->insert([
                    'menu_id'    => $menuId,
                    'group_name' => $role,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        return $menuId;
    }
}
