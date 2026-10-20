<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomFormTables extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. custom_forms
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'header_image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_public' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'allow_multiple' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'auto_link_member' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'success_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
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
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('custom_forms', true);

        // 2. custom_form_fields
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'form_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'field_label' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'field_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'field_type' => [
                'type'       => 'ENUM',
                'constraint' => ['text', 'number', 'textarea', 'date', 'radio', 'checkbox', 'select'],
                'default'    => 'text',
            ],
            'field_options' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'help_text' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_required' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'map_to_member_field' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'sort_order' => [
                'type'    => 'INT',
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
        $this->forge->addForeignKey('form_id', 'custom_forms', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('custom_form_fields', true);

        // 3. custom_form_responses
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'form_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'user_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'respondent_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'respondent_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'respondent_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'link_status' => [
                'type'       => 'ENUM',
                'constraint' => ['auto_linked', 'manual_linked', 'unlinked'],
                'default'    => 'unlinked',
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'user_agent' => [
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
        $this->forge->addForeignKey('form_id', 'custom_forms', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('member_id', 'members', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('custom_form_responses', true);

        // 4. custom_form_answers
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'response_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'field_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'answer_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('response_id', 'custom_form_responses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('field_id', 'custom_form_fields', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('custom_form_answers', true);

        // 5. Register Menu "Form & Survei" in settings_menus and settings_menu_roles
        $existingMenu = $db->table('settings_menus')->where('title', 'Form & Survei')->get()->getRowArray();
        if (!$existingMenu) {
            $db->table('settings_menus')->insert([
                'parent_id'  => null,
                'title'      => 'Form & Survei',
                'icon'       => 'fa-solid fa-clipboard-question',
                'url'        => null,
                'sort_order' => 8,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $menuId = $db->insertID();

            // Submenu: Daftar Form
            $db->table('settings_menus')->insert([
                'parent_id'  => $menuId,
                'title'      => 'Kelola Form',
                'icon'       => 'fa-solid fa-list-check',
                'url'        => 'admin/forms',
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $sub1Id = $db->insertID();

            // Submenu: Buat Form Baru
            $db->table('settings_menus')->insert([
                'parent_id'  => $menuId,
                'title'      => 'Buat Form Baru',
                'icon'       => 'fa-solid fa-plus',
                'url'        => 'admin/forms/create',
                'sort_order' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $sub2Id = $db->insertID();

            // Assign roles (admin, bph, kaderisasi, seksi, user, dll)
            $roles = $db->table('settings_menu_roles')->select('DISTINCT(group_name) as g')->get()->getResultArray();
            $roleNames = array_column($roles, 'g');
            if (empty($roleNames)) {
                $roleNames = ['admin', 'bph', 'kaderisasi', 'seksi', 'user'];
            }

            foreach ([$menuId, $sub1Id, $sub2Id] as $mId) {
                foreach ($roleNames as $r) {
                    $db->table('settings_menu_roles')->insert([
                        'menu_id'    => $mId,
                        'group_name' => $r,
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

        $menu = $db->table('settings_menus')->where('title', 'Form & Survei')->get()->getRowArray();
        if ($menu) {
            $children = $db->table('settings_menus')->where('parent_id', $menu['id'])->get()->getResultArray();
            foreach ($children as $c) {
                $db->table('settings_menu_roles')->where('menu_id', $c['id'])->delete();
                $db->table('settings_menus')->where('id', $c['id'])->delete();
            }
            $db->table('settings_menu_roles')->where('menu_id', $menu['id'])->delete();
            $db->table('settings_menus')->where('id', $menu['id'])->delete();
        }

        $this->forge->dropTable('custom_form_answers', true);
        $this->forge->dropTable('custom_form_responses', true);
        $this->forge->dropTable('custom_form_fields', true);
        $this->forge->dropTable('custom_forms', true);
    }
}
