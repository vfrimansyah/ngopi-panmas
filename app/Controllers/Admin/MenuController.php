<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MenuModel;

class MenuController extends BaseController
{
    protected $menuModel;

    public function __construct()
    {
        $this->menuModel = new MenuModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        // Fetch menus and join their parents & roles
        $rawMenus = $this->menuModel
            ->orderBy('sort_order', 'ASC')
            ->orderBy('title', 'ASC')
            ->findAll();

        $menus = [];
        foreach ($rawMenus as $menu) {
            // Get parent title if any
            $parentTitle = '-';
            if ($menu['parent_id']) {
                $p = $this->menuModel->find($menu['parent_id']);
                if ($p) {
                    $parentTitle = $p['title'];
                }
            }

            // Get mapped roles
            $rolesRaw = $db->table('settings_menu_roles')
                ->where('menu_id', $menu['id'])
                ->get()
                ->getResultArray();
            $roles = array_column($rolesRaw, 'group_name');

            $menu['parent_title'] = $parentTitle;
            $menu['roles'] = $roles;
            $menus[] = $menu;
        }

        $data = [
            'title' => 'Manajemen Menu',
            'menus' => $menus
        ];

        $perms = $this->getPagePermissions('menus');
        $data['can_create'] = $perms['canCreate'];
        $data['can_update'] = $perms['canUpdate'];
        $data['can_delete'] = $perms['canDelete'];

        return view('admin/menus/index', $data);
    }

    protected function getAllGroups(): array
    {
        $db = \Config\Database::connect();
        $groups = ['admin', 'bph', 'kaderisasi', 'seksi', 'user'];
        if ($db->tableExists('admin_custom_roles')) {
            $customRoles = $db->table('admin_custom_roles')->get()->getResultArray();
            foreach ($customRoles as $cr) {
                if (!in_array($cr['slug'], $groups)) {
                    $groups[] = $cr['slug'];
                }
            }
        }
        return $groups;
    }

    protected function getPossibleParents(?int $excludeId = null): array
    {
        $all = $this->menuModel
            ->orderBy('sort_order', 'ASC')
            ->orderBy('title', 'ASC')
            ->findAll();

        $excludeIds = [];
        if ($excludeId !== null) {
            $excludeIds[$excludeId] = true;
            $findDescendants = function ($parentId) use (&$findDescendants, &$excludeIds, $all) {
                foreach ($all as $m) {
                    if ($m['parent_id'] == $parentId && !isset($excludeIds[$m['id']])) {
                        $excludeIds[$m['id']] = true;
                        $findDescendants($m['id']);
                    }
                }
            };
            $findDescendants($excludeId);
        }

        // Susun path hierarki judul: misal "DPC PKS" atau "DPC PKS > Pemilu"
        $titlePath = function ($m) use (&$titlePath, $all) {
            $parts = [$m['title']];
            $curr = $m;
            $depth = 0;
            while (!empty($curr['parent_id']) && $depth < 5) {
                $p = null;
                foreach ($all as $item) {
                    if ($item['id'] == $curr['parent_id']) {
                        $p = $item;
                        break;
                    }
                }
                if (!$p) break;
                array_unshift($parts, $p['title']);
                $curr = $p;
                $depth++;
            }
            return implode(' > ', $parts);
        };

        $result = [];
        foreach ($all as $m) {
            if (isset($excludeIds[$m['id']])) {
                continue;
            }
            $result[] = [
                'id'    => $m['id'],
                'title' => $titlePath($m)
            ];
        }

        usort($result, function($a, $b) {
            return strcmp($a['title'], $b['title']);
        });

        return $result;
    }

    public function create()
    {
        $parents = $this->getPossibleParents();

        $groups = $this->getAllGroups();

        $data = [
            'title'   => 'Tambah Menu Baru',
            'parents' => $parents,
            'groups'  => $groups
        ];

        return view('admin/menus/form', $data);
    }

    public function store()
    {
        $db = \Config\Database::connect();

        // Validation
        $rules = [
            'title'      => 'required|min_length[3]|max_length[255]',
            'sort_order' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $parentId = $this->request->getPost('parent_id') ?: null;
        $title = $this->request->getPost('title');
        $icon = $this->request->getPost('icon') ?: null;
        $url = $this->request->getPost('url') ?: null;
        $sortOrder = intval($this->request->getPost('sort_order'));
        $roles = $this->request->getPost('roles') ?: [];

        $db->transStart();

        $this->menuModel->insert([
            'parent_id'  => $parentId,
            'title'      => $title,
            'icon'       => $icon,
            'url'        => $url,
            'sort_order' => $sortOrder
        ]);

        $menuId = $this->menuModel->insertID();

        // Insert roles
        foreach ($roles as $role) {
            $db->table('settings_menu_roles')->insert([
                'menu_id'    => $menuId,
                'group_name' => $role,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan menu baru.');
        }

        return redirect()->to('admin/menus')->with('message', 'Menu baru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $menu = $this->menuModel->find($id);
        if (!$menu) {
            return redirect()->to('admin/menus')->with('error', 'Menu tidak ditemukan.');
        }

        // Get parents (excluding itself and descendants to avoid circular parenting!)
        $parents = $this->getPossibleParents((int)$id);

        // Get existing roles
        $db = \Config\Database::connect();
        $rolesRaw = $db->table('settings_menu_roles')
            ->where('menu_id', $id)
            ->get()
            ->getResultArray();
        $existingRoles = array_column($rolesRaw, 'group_name');

        $groups = $this->getAllGroups();

        $data = [
            'title'         => 'Edit Menu: ' . $menu['title'],
            'menu'          => $menu,
            'parents'       => $parents,
            'groups'        => $groups,
            'existingRoles' => $existingRoles
        ];

        return view('admin/menus/form', $data);
    }

    public function update($id)
    {
        $db = \Config\Database::connect();
        $menu = $this->menuModel->find($id);
        if (!$menu) {
            return redirect()->to('admin/menus')->with('error', 'Menu tidak ditemukan.');
        }

        $rules = [
            'title'      => 'required|min_length[3]|max_length[255]',
            'sort_order' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $parentId = $this->request->getPost('parent_id') ?: null;
        if ($parentId && $parentId == $id) {
            return redirect()->back()->withInput()->with('error', 'Menu tidak dapat menjadi parent untuk dirinya sendiri.');
        }
        $title = $this->request->getPost('title');
        $icon = $this->request->getPost('icon') ?: null;
        $url = $this->request->getPost('url') ?: null;
        $sortOrder = intval($this->request->getPost('sort_order'));
        $roles = $this->request->getPost('roles') ?: [];

        $db->transStart();

        $this->menuModel->update($id, [
            'parent_id'  => $parentId,
            'title'      => $title,
            'icon'       => $icon,
            'url'        => $url,
            'sort_order' => $sortOrder
        ]);

        // Re-sync roles: delete then insert
        $db->table('settings_menu_roles')->where('menu_id', $id)->delete();
        foreach ($roles as $role) {
            $db->table('settings_menu_roles')->insert([
                'menu_id'    => $id,
                'group_name' => $role,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui menu.');
        }

        return redirect()->to('admin/menus')->with('message', 'Menu berhasil diperbarui.');
    }

    public function delete($id)
    {
        $menu = $this->menuModel->find($id);
        if (!$menu) {
            return redirect()->to('admin/menus')->with('error', 'Menu tidak ditemukan.');
        }

        // Deleting menu (roles will cascade delete automatically due to FK constraints)
        if ($this->menuModel->delete($id)) {
            return redirect()->to('admin/menus')->with('message', 'Menu berhasil dihapus.');
        }

        return redirect()->to('admin/menus')->with('error', 'Gagal menghapus menu.');
    }
}
