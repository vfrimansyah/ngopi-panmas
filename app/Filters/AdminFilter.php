<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AdminFilter
 * Memastikan user sudah login DAN memiliki akses ke panel admin
 * (role: admin, bph, atau kaderisasi).
 */
class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek apakah user sudah login
        if (! auth()->loggedIn()) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = auth()->user();
        $userGroups = $user->getGroups();

        // Admin access is granted if user has any role other than purely 'user'
        $hasAdminRole = false;
        foreach ($userGroups as $grp) {
            if ($grp !== 'user') {
                $hasAdminRole = true;
                break;
            }
        }

        // Cek apakah user memiliki akses ke panel admin
        if (! $hasAdminRole && ! $user->can('admin.access')) {
            return redirect()->to(base_url('/'))->with('error', 'Anda tidak memiliki akses ke panel admin.');
        }

        // Cek apakah user perlu ganti password (force reset) — abaikan jika sudah di halaman change-password
        $currentUri = $request->getUri()->getPath();
        if ($user->force_password_reset && ! str_contains($currentUri, 'change-password')) {
            return redirect()->to(base_url('admin/change-password'))->with('warning', 'Anda harus mengganti password default sebelum melanjutkan.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // tidak perlu action after
    }
}
