<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMejaSayaMenus extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Get all active roles from settings_menu_roles and admin_custom_roles
        $existingRoles = ['admin', 'bph', 'kaderisasi', 'seksi', 'user'];
        if ($db->tableExists('admin_custom_roles')) {
            $customRoles = $db->table('admin_custom_roles')->get()->getResultArray();
            foreach ($customRoles as $cr) {
                if (!in_array($cr['slug'], $existingRoles)) {
                    $existingRoles[] = $cr['slug'];
                }
            }
        }
        // Also fetch any other distinct group_name already used in settings_menu_roles
        $rolesInDb = $db->table('settings_menu_roles')->select('DISTINCT(group_name) as role')->get()->getResultArray();
        foreach ($rolesInDb as $r) {
            if (!in_array($r['role'], $existingRoles)) {
                $existingRoles[] = $r['role'];
            }
        }

        // 2. Check if parent menu "Meja Saya" already exists
        $existingParent = $db->table('settings_menus')->where('title', 'Meja Saya')->get()->getRowArray();
        $parentId = null;

        if ($existingParent) {
            $parentId = $existingParent['id'];
        } else {
            $db->table('settings_menus')->insert([
                'parent_id'  => null,
                'title'      => 'Meja Saya',
                'icon'       => 'fa-solid fa-laptop-user',
                'url'        => null,
                'sort_order' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $parentId = $db->insertID();

            // Link parent menu to all roles
            foreach ($existingRoles as $role) {
                $db->table('settings_menu_roles')->insert([
                    'menu_id'    => $parentId,
                    'group_name' => $role,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // 3. Define the submenus under "Meja Saya"
        $submenus = [
            [
                'title'      => 'Profile',
                'icon'       => 'fa-solid fa-id-card',
                'url'        => 'admin/meja/profile',
                'sort_order' => 1,
            ],
            [
                'title'      => 'Mutabaah',
                'icon'       => 'fa-solid fa-book-quran',
                'url'        => 'admin/meja/mutabaah',
                'sort_order' => 2,
            ],
            [
                'title'      => 'Iuran',
                'icon'       => 'fa-solid fa-hand-holding-dollar',
                'url'        => 'admin/meja/iuran',
                'sort_order' => 3,
            ],
        ];

        foreach ($submenus as $sub) {
            $existingSub = $db->table('settings_menus')
                ->where('parent_id', $parentId)
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
                    'parent_id'  => $parentId,
                    'title'      => $sub['title'],
                    'icon'       => $sub['icon'],
                    'url'        => $sub['url'],
                    'sort_order' => $sub['sort_order'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $subId = $db->insertID();
            }

            // Assign roles
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

    public function down()
    {
        $db = \Config\Database::connect();
        $parent = $db->table('settings_menus')->where('title', 'Meja Saya')->get()->getRowArray();
        if ($parent) {
            // Delete children
            $children = $db->table('settings_menus')->where('parent_id', $parent['id'])->get()->getResultArray();
            foreach ($children as $c) {
                $db->table('settings_menu_roles')->where('menu_id', $c['id'])->delete();
                $db->table('settings_menus')->where('id', $c['id'])->delete();
            }
            // Delete parent
            $db->table('settings_menu_roles')->where('menu_id', $parent['id'])->delete();
            $db->table('settings_menus')->where('id', $parent['id'])->delete();
        }
    }
}
