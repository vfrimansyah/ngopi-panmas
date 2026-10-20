<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Models\UserModel;

class UserManagementController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Ambil semua role yang ada (System Roles + Custom Roles dari admin_custom_roles)
     */
    protected function getRoles(): array
    {
        $roles = [
            'admin'      => 'Super Admin',
            'bph'        => 'BPH',
            'kaderisasi' => 'Kaderisasi',
            'seksi'      => 'Seksi',
            'user'       => 'User',
        ];

        $db = \Config\Database::connect();
        if ($db->tableExists('admin_custom_roles')) {
            $customRoles = $db->table('admin_custom_roles')->get()->getResultArray();
            foreach ($customRoles as $cr) {
                $roles[$cr['slug']] = $cr['title'];
            }
        }

        return $roles;
    }

    /**
     * Daftar semua user
     */
    public function index()
    {
        $db = \Config\Database::connect();

        // Query 1: Ambil semua user + email
        $users = $db->table('users u')
            ->select('u.id, u.username, u.active, u.force_password_reset, u.last_active, u.created_at, ai.secret AS email')
            ->join('auth_identities ai', "ai.user_id = u.id AND ai.type IN ('email_password', 'email')", 'left')
            ->orderBy('u.created_at', 'DESC')
            ->get()->getResultArray();

        // Query 2: Ambil semua grup per user (bisa > 1 role)
        $groupRows = $db->table('auth_groups_users')->get()->getResultArray();
        $groupMap = [];
        foreach ($groupRows as $row) {
            $groupMap[$row['user_id']][] = $row['group'];
        }

        // Merge array grup ke data user
        foreach ($users as &$user) {
            $user['groups'] = $groupMap[$user['id']] ?? ['user'];
        }
        unset($user);

        $perms = $this->getPagePermissions('users');
        return view('admin/users/index', [
            'title' => 'Manajemen User',
            'users' => $users,
            'roles' => $this->getRoles(),
            'can_create' => $perms['canCreate'],
            'can_update' => $perms['canUpdate'],
            'can_delete' => $perms['canDelete'],
        ]);
    }

    /**
     * Form tambah user baru
     */
    public function create()
    {
        return view('admin/users/create', [
            'title' => 'Tambah User Baru',
            'roles' => $this->getRoles(),
        ]);
    }

    /**
     * Simpan user baru
     */
    public function store()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[auth_identities.secret]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $rolesInput = (array) $this->request->getPost('roles');
        $availableRoles = array_keys($this->getRoles());
        $validRoles = array_intersect($rolesInput, $availableRoles);
        if (empty($validRoles)) {
            $validRoles = ['user'];
        }

        // Buat user baru via Shield UserModel
        $user = new \CodeIgniter\Shield\Entities\User([
            'username' => $this->request->getPost('username'),
            'active'   => 1,
        ]);

        // Set email & password default
        $user->setEmail($this->request->getPost('email'));
        $user->setPassword('P4nm4s');

        $this->userModel->save($user);
        $userId = $this->userModel->getInsertID();

        // Assign semua grup/role terpilih
        $newUser = $this->userModel->findById($userId);
        foreach ($validRoles as $r) {
            $newUser->addGroup($r);
        }

        // Tandai harus ganti password
        $db = \Config\Database::connect();
        $db->table('users')->where('id', $userId)->update(['force_password_reset' => 1]);

        return redirect()->to(base_url('admin/users'))->with('success', 'User berhasil ditambahkan. Password default: P4nm4s');
    }

    /**
     * Form edit user
     */
    public function edit($id)
    {
        $db = \Config\Database::connect();

        // Ambil data user + email
        $user = $db->table('users u')
            ->select('u.id, u.username, u.active, u.force_password_reset, ai.secret AS email')
            ->join('auth_identities ai', "ai.user_id = u.id AND ai.type IN ('email_password', 'email')", 'left')
            ->where('u.id', $id)
            ->get()->getRowArray();

        if (! $user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        // Ambil semua grup user (bisa multiple)
        $groupRows = $db->table('auth_groups_users')->where('user_id', $id)->get()->getResultArray();
        $user['groups'] = !empty($groupRows) ? array_column($groupRows, 'group') : ['user'];

        return view('admin/users/edit', [
            'title'  => 'Edit User: ' . $user['username'],
            'user'   => $user,
            'roles'  => $this->getRoles(),
        ]);
    }

    /**
     * Update user
     */
    public function update($id)
    {
        $db = \Config\Database::connect();

        $existing = $this->userModel->findById($id);
        if (! $existing) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        // Cek email unik (kecuali milik sendiri)
        $newEmail = $this->request->getPost('email');
        $emailCheck = $db->query("
            SELECT ai.user_id FROM auth_identities ai
            WHERE ai.secret = ? AND ai.user_id != ? AND ai.type = 'email_password'
        ", [$newEmail, $id])->getRowArray();

        if ($emailCheck) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan oleh user lain.');
        }

        // Update username
        $newUsername = $this->request->getPost('username');
        $usernameCheck = $db->query("SELECT id FROM users WHERE username = ? AND id != ?", [$newUsername, $id])->getRowArray();
        if ($usernameCheck) {
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan.');
        }

        $db->table('users')->where('id', $id)->update([
            'username' => $newUsername,
            'active'   => $this->request->getPost('active') ? 1 : 0,
        ]);

        // Update email di auth_identities
        $db->table('auth_identities')
            ->where('user_id', $id)
            ->where('type', 'email_password')
            ->update(['secret' => $newEmail]);

        // Ganti password jika diisi
        $newPass = $this->request->getPost('password');
        if (!empty($newPass)) {
            $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
            $db->table('auth_identities')
                ->where('user_id', $id)
                ->where('type', 'email_password')
                ->update(['secret2' => $hashedPass]);
            // tandai harus reset jika admin memberikan password baru
            $db->table('users')->where('id', $id)->update(['force_password_reset' => 1]);
        }

        // Update role (bisa multiple)
        $rolesInput = (array) $this->request->getPost('roles');
        $availableRoles = array_keys($this->getRoles());
        $validRoles = array_intersect($rolesInput, $availableRoles);
        if (empty($validRoles)) {
            $validRoles = ['user'];
        }

        // Hapus grup lama
        $db->table('auth_groups_users')->where('user_id', $id)->delete();
        // Insert semua grup baru
        foreach ($validRoles as $role) {
            $db->table('auth_groups_users')->insert([
                'user_id'    => $id,
                'group'      => $role,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->to(base_url('admin/users'))->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Hapus user
     */
    public function delete($id)
    {
        // Jangan hapus diri sendiri
        if (auth()->id() == $id) {
            return redirect()->to(base_url('admin/users'))->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $db = \Config\Database::connect();
        $db->table('auth_groups_users')->where('user_id', $id)->delete();
        $db->table('auth_identities')->where('user_id', $id)->delete();
        $db->table('users')->where('id', $id)->delete();

        return redirect()->to(base_url('admin/users'))->with('success', 'User berhasil dihapus.');
    }

    /**
     * Reset password ke default "P4nm4s"
     */
    public function resetPassword($id)
    {
        $db = \Config\Database::connect();

        $hashedPass = password_hash('P4nm4s', PASSWORD_DEFAULT);
        $db->table('auth_identities')
            ->where('user_id', $id)
            ->where('type', 'email_password')
            ->update(['secret2' => $hashedPass]);

        $db->table('users')->where('id', $id)->update(['force_password_reset' => 1]);

        return redirect()->to(base_url('admin/users'))->with('success', 'Password berhasil direset ke default. User harus ganti password saat login berikutnya.');
    }
}
