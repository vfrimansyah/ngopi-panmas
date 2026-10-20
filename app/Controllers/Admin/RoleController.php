<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RolePermissionModel;

class RoleController extends BaseController
{
    protected $permModel;

    // Halaman-halaman admin yang dikenai permissions
    protected $pages = [
        'dashboard' => ['label' => 'Dashboard',                   'icon' => 'fa-gauge',      'route' => '/admin/dashboard'],
        'members'   => ['label' => 'Anggota',                     'icon' => 'fa-users',      'route' => '/admin/members'],
        'match'     => ['label' => 'Pencocokan (Pasangan & Anak)','icon' => 'fa-code-merge', 'route' => '/admin/match'],
        'products'  => ['label' => 'Produk & Kategori',           'icon' => 'fa-store',      'route' => '/admin/products'],
        'news'      => ['label' => 'Berita & Kategori',           'icon' => 'fa-newspaper',  'route' => '/admin/news'],
        'kegiatan'  => ['label' => 'Modul Kegiatan',              'icon' => 'fa-calendar-days','route' => '/admin/kegiatan/dashboard'],
        'booking'   => ['label' => 'Booking Ruangan',             'icon' => 'fa-door-open',  'route' => '/admin/booking/list'],
        'dpc'       => ['label' => 'DPC PKS (Struktur, Proker & Monev)', 'icon' => 'fa-sitemap', 'route' => '/admin/dpc/dashboard'],
        'documents' => ['label' => 'Manajemen Dokumen (DMS)',      'icon' => 'fa-folder-open', 'route' => '/admin/documents'],
        'gallery'   => ['label' => 'Galeri & Media (Foto & Video)', 'icon' => 'fa-photo-film',  'route' => '/admin/gallery'],
        'aset'      => ['label' => 'Aset & Inventaris',           'icon' => 'fa-warehouse',  'route' => '/admin/aset/dashboard'],
        'pemilu'    => ['label' => 'Pemilu',                      'icon' => 'fa-ballot-check','route' => '/admin/pemilu'],
        'keuangan'  => ['label' => 'Keuangan',                    'icon' => 'fa-coins',      'route' => '/admin/keuangan'],
        'users'     => ['label' => 'Manajemen User',              'icon' => 'fa-user-gear',  'route' => '/admin/users'],
        'roles'     => ['label' => 'Page Role',                   'icon' => 'fa-lock',       'route' => '/admin/roles'],
        'menus'     => ['label' => 'Manajemen Menu',              'icon' => 'fa-bars',       'route' => '/admin/menus'],
        'settings'  => ['label' => 'Pengaturan Web',              'icon' => 'fa-gear',       'route' => '/admin/settings'],
    ];

    // Role sistem bawaan yang tidak bisa dihapus
    protected $systemRoles = ['admin', 'bph', 'kaderisasi', 'seksi', 'user'];

    // Konfigurasi tampilan role
    protected $roleConfig = [
        'admin'      => ['title' => 'Super Admin',             'description' => 'Administrator penuh — akses ke semua fitur termasuk manajemen user dan role.', 'color' => 'danger',    'hex' => '#ef4444', 'icon' => 'fa-shield-halved'],
        'bph'        => ['title' => 'BPH (Badan Pengurus Harian)', 'description' => 'Akses ke manajemen anggota, konten, pencocokan, dan laporan.',          'color' => 'warning',   'hex' => '#f59e0b', 'icon' => 'fa-star'],
        'kaderisasi' => ['title' => 'Kaderisasi',              'description' => 'Akses ke data anggota dan pencocokan keluarga.',                               'color' => 'info',      'hex' => '#3b82f6', 'icon' => 'fa-people-group'],
        'seksi'      => ['title' => 'Seksi',                   'description' => 'Akses terbatas yang dikonfigurasi per halaman oleh Admin.',                   'color' => 'success',   'hex' => '#10b981', 'icon' => 'fa-sitemap'],
        'user'       => ['title' => 'User',                    'description' => 'Anggota umum — hanya akses ke halaman publik situs.',                         'color' => 'secondary', 'hex' => '#6b7280', 'icon' => 'fa-user'],
    ];

    public function __construct()
    {
        $this->permModel = new RolePermissionModel();
    }

    /**
     * Daftar semua role + peta akses CRUD
     */
    public function index()
    {
        $db = \Config\Database::connect();

        // Ambil jumlah user per role
        $userCounts = [];
        $rows = $db->query("SELECT `group`, COUNT(*) as cnt FROM auth_groups_users GROUP BY `group`")->getResultArray();
        foreach ($rows as $row) {
            $userCounts[$row['group']] = $row['cnt'];
        }

        // Ambil semua custom roles dari auth_groups (di luar system roles yang tidak ada di config)
        $allGroups = \Config\AuthGroups::class;
        $configGroups = (new \Config\AuthGroups())->groups;

        // Ambil custom roles dari DB
        $db = \Config\Database::connect();
        $customRoles = [];
        if ($db->tableExists('admin_custom_roles')) {
            $rows = $db->table('admin_custom_roles')->get()->getResultArray();
            foreach ($rows as $cr) {
                $customRoles[$cr['slug']] = $cr;
            }
        }

        // Ambil permissions semua role dari DB
        $allPerms = $this->permModel->getAllGrouped();

        // Bangun daftar role (dari config + tambahan custom dari DB)
        $roles = [];

        // 1. System roles dari AuthGroups
        foreach ($configGroups as $slug => $group) {
            $cfg = $this->roleConfig[$slug] ?? [
                'title'       => $group['title'],
                'description' => $group['description'] ?? '',
                'color'       => 'secondary',
                'hex'         => '#6b7280',
                'icon'        => 'fa-circle-user',
            ];
            $roles[$slug] = array_merge($cfg, [
                'slug'        => $slug,
                'is_system'   => in_array($slug, $this->systemRoles),
                'user_count'  => $userCounts[$slug] ?? 0,
                'permissions' => $allPerms[$slug] ?? [],
            ]);
        }

        // 2. Custom roles dari DB (yang belum ada di config)
        foreach ($customRoles as $slug => $cr) {
            if (isset($roles[$slug])) continue; // sudah ada dari config
            $roles[$slug] = [
                'slug'        => $slug,
                'title'       => $cr['title'],
                'description' => $cr['description'] ?? '',
                'color'       => $cr['color'] ?? 'secondary',
                'hex'         => $this->hexFromColor($cr['color'] ?? 'secondary'),
                'icon'        => $cr['icon'] ?? 'fa-circle-user',
                'is_system'   => false,
                'user_count'  => $userCounts[$slug] ?? 0,
                'permissions' => $allPerms[$slug] ?? [],
            ];
        }

        $perms = $this->getPagePermissions('roles');
        return view('admin/roles/index', [
            'title'      => 'Page Role & Permissions',
            'roles'      => $roles,
            'pages'      => $this->pages,
            'allPerms'   => $allPerms,
            'can_create' => $perms['canCreate'],
            'can_update' => $perms['canUpdate'],
            'can_delete' => $perms['canDelete'],
        ]);
    }

    /**
     * Form tambah role baru
     */
    public function create()
    {
        // Kirim data preset permissions untuk fitur "Salin dari role"
        $presets = $this->permModel->getAllGrouped();

        return view('admin/roles/create', [
            'title'        => 'Tambah Role Baru',
            'pages'        => $this->pages,
            'colorOptions' => $this->colorOptions(),
            'iconOptions'  => $this->iconOptions(),
            'presets'      => $presets,
        ]);
    }

    /**
     * Simpan role baru
     */
    public function store()
    {
        $slug  = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $this->request->getPost('slug')));
        $title = trim($this->request->getPost('title'));
        $desc  = trim($this->request->getPost('description'));
        $color = $this->request->getPost('color');
        $icon  = $this->request->getPost('icon');

        if (empty($slug) || empty($title)) {
            return redirect()->back()->withInput()->with('error', 'Slug dan nama role wajib diisi.');
        }

        // Cek duplikasi slug
        $authGroups = new \Config\AuthGroups();
        if (isset($authGroups->groups[$slug])) {
            return redirect()->back()->withInput()->with('error', "Role dengan slug '{$slug}' sudah ada.");
        }

        // Simpan permissions dari form
        $permissions = $this->buildPermissionsFromPost();

        // Simpan ke DB permissions
        $this->permModel->saveForRole($slug, $permissions);

        // Tambahkan ke AuthGroups di DB (kita simpan di tabel custom_roles)
        $db = \Config\Database::connect();

        // Cek apakah tabel custom_roles sudah ada
        if (!$db->tableExists('admin_custom_roles')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'slug'        => ['type' => 'VARCHAR', 'constraint' => 50],
                'title'       => ['type' => 'VARCHAR', 'constraint' => 150],
                'description' => ['type' => 'TEXT', 'null' => true],
                'color'       => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'secondary'],
                'icon'        => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'fa-circle-user'],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('slug', true);
            $forge->createTable('admin_custom_roles');
        }

        $db->table('admin_custom_roles')->insert([
            'slug'        => $slug,
            'title'       => $title,
            'description' => $desc,
            'color'       => $color ?? 'secondary',
            'icon'        => $icon  ?? 'fa-circle-user',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        // Sinkronkan akses menu otomatis
        $this->syncMenuRolesForRole($slug);

        return redirect()->to(base_url('admin/roles'))->with('success', "Role '{$title}' berhasil ditambahkan.");
    }

    /**
     * Form edit role
     */
    public function edit(string $slug)
    {
        $authGroups = new \Config\AuthGroups();
        $allGroups  = $authGroups->groups;

        // Cek custom roles juga
        $db = \Config\Database::connect();
        $customRole = null;
        if ($db->tableExists('admin_custom_roles')) {
            $customRole = $db->table('admin_custom_roles')->where('slug', $slug)->get()->getRowArray();
        }

        if (!isset($allGroups[$slug]) && !$customRole) {
            return redirect()->to(base_url('admin/roles'))->with('error', 'Role tidak ditemukan.');
        }

        $cfg = $this->roleConfig[$slug] ?? null;
        if ($customRole) {
            $cfg = [
                'title'       => $customRole['title'],
                'description' => $customRole['description'],
                'color'       => $customRole['color'],
                'hex'         => $this->hexFromColor($customRole['color']),
                'icon'        => $customRole['icon'],
            ];
        }

        $rolePerms = $this->permModel->getByRole($slug);

        return view('admin/roles/edit', [
            'title'        => 'Edit Role: ' . ($cfg['title'] ?? $slug),
            'slug'         => $slug,
            'role'         => $cfg,
            'is_system'    => in_array($slug, $this->systemRoles),
            'pages'        => $this->pages,
            'rolePerms'    => $rolePerms,
            'colorOptions' => $this->colorOptions(),
            'iconOptions'  => $this->iconOptions(),
        ]);
    }

    /**
     * Update role
     */
    public function update(string $slug)
    {
        $db = \Config\Database::connect();

        // Simpan permissions
        $permissions = $this->buildPermissionsFromPost();
        $this->permModel->saveForRole($slug, $permissions);

        // Update custom role jika bukan system role
        if (!in_array($slug, $this->systemRoles)) {
            $title = trim($this->request->getPost('title'));
            $desc  = trim($this->request->getPost('description'));
            $color = $this->request->getPost('color');
            $icon  = $this->request->getPost('icon');

            if ($db->tableExists('admin_custom_roles')) {
                $db->table('admin_custom_roles')->where('slug', $slug)->update([
                    'title'       => $title,
                    'description' => $desc,
                    'color'       => $color,
                    'icon'        => $icon,
                    'updated_at'  => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Sinkronkan akses menu otomatis
        $this->syncMenuRolesForRole($slug);

        return redirect()->to(base_url('admin/roles'))->with('success', 'Permissions role berhasil diperbarui.');
    }

    /**
     * Copy/duplikasi role
     */
    public function copy(string $slug)
    {
        $authGroups = new \Config\AuthGroups();
        $allGroups  = $authGroups->groups;
        $db = \Config\Database::connect();

        $customRole = null;
        if ($db->tableExists('admin_custom_roles')) {
            $customRole = $db->table('admin_custom_roles')->where('slug', $slug)->get()->getRowArray();
        }

        if (!isset($allGroups[$slug]) && !$customRole) {
            return redirect()->to(base_url('admin/roles'))->with('error', 'Role tidak ditemukan.');
        }

        // Buat slug baru
        $baseTitle = $customRole ? $customRole['title'] : ($this->roleConfig[$slug]['title'] ?? $slug);
        $newSlug   = $slug . '_copy_' . time();
        $newTitle  = $baseTitle . ' (Salinan)';

        $srcColor = $customRole['color'] ?? ($this->roleConfig[$slug]['color'] ?? 'secondary');
        $srcIcon  = $customRole['icon']  ?? ($this->roleConfig[$slug]['icon']  ?? 'fa-circle-user');
        $srcDesc  = $customRole['description'] ?? ($this->roleConfig[$slug]['description'] ?? '');

        // Pastikan tabel custom_roles ada
        if (!$db->tableExists('admin_custom_roles')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'slug'        => ['type' => 'VARCHAR', 'constraint' => 50],
                'title'       => ['type' => 'VARCHAR', 'constraint' => 150],
                'description' => ['type' => 'TEXT', 'null' => true],
                'color'       => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'secondary'],
                'icon'        => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'fa-circle-user'],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('slug', true);
            $forge->createTable('admin_custom_roles');
        }

        $db->table('admin_custom_roles')->insert([
            'slug'        => $newSlug,
            'title'       => $newTitle,
            'description' => $srcDesc,
            'color'       => $srcColor,
            'icon'        => $srcIcon,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        // Copy permissions
        $this->permModel->copyFromRole($slug, $newSlug);

        // Sinkronkan akses menu untuk role baru
        $this->syncMenuRolesForRole($newSlug);

        return redirect()->to(base_url('admin/roles'))->with('success', "Role '{$newTitle}' berhasil dibuat sebagai salinan.");
    }

    /**
     * Hapus role (hanya role custom, bukan system role)
     */
    public function delete(string $slug)
    {
        if (in_array($slug, $this->systemRoles)) {
            return redirect()->to(base_url('admin/roles'))->with('error', 'Role sistem tidak dapat dihapus.');
        }

        $db = \Config\Database::connect();

        // Hapus dari custom roles
        if ($db->tableExists('admin_custom_roles')) {
            $db->table('admin_custom_roles')->where('slug', $slug)->delete();
        }

        // Hapus permissions
        $this->permModel->deleteByRole($slug);

        // Pindahkan user yang punya role ini ke 'user'
        $db->table('auth_groups_users')->where('group', $slug)->update(['group' => 'user']);

        return redirect()->to(base_url('admin/roles'))->with('success', 'Role berhasil dihapus. User terkait dipindahkan ke role User.');
    }

    // ─────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────

    /**
     * Bangun array permissions dari POST data
     */
    private function buildPermissionsFromPost(): array
    {
        $permissions = [];
        foreach (array_keys($this->pages) as $pageKey) {
            $permissions[$pageKey] = [
                'create' => (int) ($this->request->getPost("perm_{$pageKey}_create") ?? 0),
                'read'   => (int) ($this->request->getPost("perm_{$pageKey}_read")   ?? 0),
                'update' => (int) ($this->request->getPost("perm_{$pageKey}_update") ?? 0),
                'delete' => (int) ($this->request->getPost("perm_{$pageKey}_delete") ?? 0),
            ];
        }
        return $permissions;
    }

    private function hexFromColor(string $color): string
    {
        $map = [
            'danger'    => '#ef4444',
            'warning'   => '#f59e0b',
            'info'      => '#3b82f6',
            'success'   => '#10b981',
            'secondary' => '#6b7280',
            'primary'   => '#6366f1',
            'dark'      => '#1f2937',
        ];
        return $map[$color] ?? '#6b7280';
    }

    private function colorOptions(): array
    {
        return [
            'danger'    => ['label' => 'Merah',   'hex' => '#ef4444'],
            'warning'   => ['label' => 'Kuning',  'hex' => '#f59e0b'],
            'info'      => ['label' => 'Biru',    'hex' => '#3b82f6'],
            'success'   => ['label' => 'Hijau',   'hex' => '#10b981'],
            'primary'   => ['label' => 'Ungu',    'hex' => '#6366f1'],
            'secondary' => ['label' => 'Abu-abu', 'hex' => '#6b7280'],
            'dark'      => ['label' => 'Gelap',   'hex' => '#1f2937'],
        ];
    }

    private function iconOptions(): array
    {
        return [
            'fa-shield-halved', 'fa-star', 'fa-people-group', 'fa-sitemap',
            'fa-user', 'fa-user-gear', 'fa-circle-user', 'fa-crown',
            'fa-briefcase', 'fa-building', 'fa-flag', 'fa-key',
            'fa-lock', 'fa-id-badge', 'fa-users', 'fa-user-tie',
        ];
    }

    /**
     * Sinkronkan tabel settings_menu_roles berdasarkan permissions page_key role ini
     */
    private function syncMenuRolesForRole(string $roleSlug): void
    {
        $db = \Config\Database::connect();

        $pageMenuMap = [
            'dashboard' => ['admin/dashboard'],
            'members'   => ['admin/members', 'admin/match'],
            'match'     => ['admin/match'],
            'products'  => ['admin/products', 'admin/products/categories'],
            'news'      => ['admin/news', 'admin/news/categories'],
            'kegiatan'  => ['admin/kegiatan/dashboard', 'admin/kegiatan/master', 'admin/kegiatan/list', 'admin/kegiatan/rekap-dpra', 'admin/kegiatan/rekap-rw'],
            'booking'   => ['admin/booking/master', 'admin/booking/form', 'admin/booking/list'],
            'dpc'       => ['admin/dpc/dashboard', 'admin/dpc/struktur', 'admin/dpc/proker', 'admin/dpc/rab', 'admin/dpc/monev'],
            'documents' => ['admin/documents', 'admin/documents/create', 'admin/documents/edit', 'admin/documents/view', 'admin/documents/categories'],
            'gallery'   => ['admin/gallery', 'admin/gallery/create-image', 'admin/gallery/create-video', 'admin/gallery/edit', 'admin/gallery/albums'],
            'aset'      => ['admin/aset/dashboard', 'admin/aset/list', 'admin/aset/create', 'admin/aset/edit', 'admin/aset/export-excel'],
            'pemilu'    => ['admin/pemilu/partai', 'admin/pemilu/peserta', 'admin/dpra', 'admin/pemilu/tps', 'admin/pemilu/hasil', 'admin/pemilu/sisir-rw'],
            'keuangan'  => ['admin/keuangan', 'admin/keuangan/accounts', 'admin/keuangan/transaksi', 'admin/keuangan/jurnal', 'admin/keuangan/rekening', 'admin/keuangan/laporan', 'admin/keuangan/iuran'],
            'users'     => ['admin/users'],
            'roles'     => ['admin/roles'],
            'menus'     => ['admin/menus'],
            'settings'  => ['admin/settings'],
        ];

        // Fetch permissions for this role where can_read = 1
        $rows = $db->table('admin_role_permissions')->where('role_slug', $roleSlug)->where('can_read', 1)->get()->getResultArray();
        $allowedPages = array_column($rows, 'page_key');

        $allowedUrls = ['']; // always allow Lihat Website
        foreach ($allowedPages as $pageKey) {
            if (isset($pageMenuMap[$pageKey])) {
                $allowedUrls = array_merge($allowedUrls, $pageMenuMap[$pageKey]);
            }
        }

        if (!empty($allowedUrls)) {
            $menus = $db->table('settings_menus')->whereIn('url', $allowedUrls)->get()->getResultArray();
            $menuIds = [];
            foreach ($menus as $m) {
                $menuIds[] = $m['id'];
                if ($m['parent_id']) {
                    $menuIds[] = $m['parent_id'];
                }
            }
            $menuIds = array_unique($menuIds);

            // Clear old menu roles for this group_name
            $db->table('settings_menu_roles')->where('group_name', $roleSlug)->delete();

            // Insert new menu roles
            $insertRows = [];
            $now = date('Y-m-d H:i:s');
            foreach ($menuIds as $mId) {
                $insertRows[] = [
                    'menu_id'    => $mId,
                    'group_name' => $roleSlug,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if (!empty($insertRows)) {
                $db->table('settings_menu_roles')->insertBatch($insertRows);
            }
        }
    }
}
