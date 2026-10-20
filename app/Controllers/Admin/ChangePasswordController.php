<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Handles forced password change on first login
 */
class ChangePasswordController extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        // Jika tidak perlu reset, redirect ke dashboard
        if (!$user->force_password_reset) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        return view('admin/change_password', [
            'title' => 'Ganti Password',
        ]);
    }

    public function update()
    {
        $user = auth()->user();

        $rules = [
            'password'     => 'required|min_length[6]',
            'pass_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $newPassword = $this->request->getPost('password');

        // Cek tidak sama dengan password default
        if ($newPassword === 'P4nm4s') {
            return redirect()->back()->with('error', 'Password baru tidak boleh sama dengan password default.');
        }

        // Update password via Shield
        $db = \Config\Database::connect();
        $hashedPass = password_hash($newPassword, PASSWORD_DEFAULT);
        $db->table('auth_identities')
            ->where('user_id', $user->id)
            ->where('type', 'email_password')
            ->update(['secret2' => $hashedPass]);

        // Hapus flag force_password_reset
        $db->table('users')->where('id', $user->id)->update(['force_password_reset' => 0]);

        return redirect()->to(base_url('admin/dashboard'))->with('success', 'Password berhasil diperbarui. Selamat datang!');
    }
}
