<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLembagaAndProposalMenus extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Get all roles for access
        $existingRoles = ['admin', 'bph', 'kaderisasi', 'seksi', 'user'];
        if ($db->tableExists('admin_custom_roles')) {
            $customRoles = $db->table('admin_custom_roles')->get()->getResultArray();
            foreach ($customRoles as $cr) {
                if (!in_array($cr['slug'], $existingRoles)) {
                    $existingRoles[] = $cr['slug'];
                }
            }
        }
        $rolesInDb = $db->table('settings_menu_roles')->select('DISTINCT(group_name) as role')->get()->getResultArray();
        foreach ($rolesInDb as $r) {
            if (!in_array($r['role'], $existingRoles)) {
                $existingRoles[] = $r['role'];
            }
        }

        // 2. Find parent menu "Meja Saya"
        $parentMeja = $db->table('settings_menus')->where('title', 'Meja Saya')->get()->getRowArray();
        $parentMejaId = $parentMeja ? $parentMeja['id'] : null;

        if ($parentMejaId) {
            $mejaSubmenus = [
                [
                    'title'      => 'Yayasan & Legalitas',
                    'icon'       => 'fa-solid fa-landmark',
                    'url'        => 'admin/meja/lembaga',
                    'sort_order' => 4,
                ],
                [
                    'title'      => 'Proposal Saya',
                    'icon'       => 'fa-solid fa-file-signature',
                    'url'        => 'admin/meja/proposals',
                    'sort_order' => 5,
                ],
            ];

            foreach ($mejaSubmenus as $sub) {
                $existingSub = $db->table('settings_menus')
                    ->where('parent_id', $parentMejaId)
                    ->where('title', $sub['title'])
                    ->get()
                    ->getRowArray();

                $subId = null;
                if ($existingSub) {
                    $subId = $existingSub['id'];
                    $db->table('settings_menus')->where('id', $subId)->update([
                        'icon'       => $sub['icon'],
                        'url'        => $sub['url'],
                        'sort_order' => $sub['sort_order'],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $db->table('settings_menus')->insert([
                        'parent_id'  => $parentMejaId,
                        'title'      => $sub['title'],
                        'icon'       => $sub['icon'],
                        'url'        => $sub['url'],
                        'sort_order' => $sub['sort_order'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $subId = $db->insertID();
                }

                // Give access to all member roles for Meja Saya submenus
                foreach ($existingRoles as $role) {
                    $hasRole = $db->table('settings_menu_roles')
                        ->where('menu_id', $subId)
                        ->where('group_name', $role)
                        ->countAllResults();

                    if ($hasRole === 0) {
                        $db->table('settings_menu_roles')->insert([
                            'menu_id'    => $subId,
                            'group_name' => $role,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
        }

        // 3. Admin / BPH Monitoring Menu: "Proposal & Pokir"
        $adminMenu = $db->table('settings_menus')->where('title', 'Proposal & Pokir')->get()->getRowArray();
        $adminMenuId = null;

        if ($adminMenu) {
            $adminMenuId = $adminMenu['id'];
            $db->table('settings_menus')->where('id', $adminMenuId)->update([
                'icon'       => 'fa-solid fa-file-invoice-dollar',
                'url'        => 'admin/proposals',
                'sort_order' => 9,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $db->table('settings_menus')->insert([
                'parent_id'  => null,
                'title'      => 'Proposal & Pokir',
                'icon'       => 'fa-solid fa-file-invoice-dollar',
                'url'        => 'admin/proposals',
                'sort_order' => 9,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $adminMenuId = $db->insertID();
        }

        // Give access to admin, bph, superadmin
        $adminRoles = ['admin', 'bph', 'superadmin'];
        foreach ($adminRoles as $role) {
            $hasRole = $db->table('settings_menu_roles')
                ->where('menu_id', $adminMenuId)
                ->where('group_name', $role)
                ->countAllResults();

            if ($hasRole === 0) {
                $db->table('settings_menu_roles')->insert([
                    'menu_id'    => $adminMenuId,
                    'group_name' => $role,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->table('settings_menus')->whereIn('url', [
            'admin/meja/lembaga',
            'admin/meja/proposals',
            'admin/proposals'
        ])->delete();
    }
}
