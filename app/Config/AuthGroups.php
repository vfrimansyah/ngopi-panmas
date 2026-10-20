<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * Default Group
     * Newly registered users are added to this group.
     */
    public string $defaultGroup = 'user';

    /**
     * Groups available in the system.
     *
     * @var array<string, array<string, string>>
     */
    public array $groups = [
        'admin' => [
            'title'       => 'Super Admin',
            'description' => 'Administrator penuh — akses ke semua fitur termasuk manajemen user dan role.',
        ],
        'bph' => [
            'title'       => 'BPH',
            'description' => 'Badan Pengurus Harian — akses ke manajemen anggota, konten, dan laporan.',
        ],
        'kaderisasi' => [
            'title'       => 'Kaderisasi',
            'description' => 'Tim Kaderisasi — akses ke data anggota dan pencocokan.',
        ],
        'seksi' => [
            'title'       => 'Seksi DPC',
            'description' => 'Akses terbatas yang dikonfigurasi per halaman oleh Admin.',
        ],
        'user' => [
            'title'       => 'User',
            'description' => 'Anggota umum — hanya akses ke halaman publik.',
        ],
    ];

    /**
     * Available permissions in the system.
     */
    public array $permissions = [
        'admin.access'        => 'Dapat mengakses panel admin',
        'admin.settings'      => 'Dapat mengubah pengaturan situs',
        'users.manage'        => 'Dapat mengelola user dan role',
        'members.manage'      => 'Dapat mengelola data anggota',
        'members.view'        => 'Dapat melihat data anggota',
        'content.manage'      => 'Dapat mengelola konten (produk, berita, event)',
        'match.manage'        => 'Dapat mengelola pencocokan anggota',
        'reports.view'        => 'Dapat melihat laporan dan dashboard',
    ];

    /**
     * Permissions matrix — maps groups to permissions.
     */
    public array $matrix = [
        'admin' => [
            'admin.*',
            'users.*',
            'members.*',
            'content.*',
            'match.*',
            'reports.*',
        ],
        'bph' => [
            'admin.access',
            'members.manage',
            'members.view',
            'content.manage',
            'match.manage',
            'reports.view',
        ],
        'kaderisasi' => [
            'admin.access',
            'members.view',
            'members.manage',
            'match.manage',
        ],
        'seksi' => [
            'admin.access',
            'members.view',
        ],
        'user' => [],
    ];
}
