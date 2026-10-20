<?php

namespace App\Models;

use CodeIgniter\Model;

class RolePermissionModel extends Model
{
    protected $table         = 'admin_role_permissions';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'role_slug', 'page_key',
        'can_create', 'can_read', 'can_update', 'can_delete',
    ];
    protected $useTimestamps = true;

    /**
     * Ambil semua permissions untuk satu role, di-index by page_key
     */
    public function getByRole(string $roleSlug): array
    {
        $rows = $this->where('role_slug', $roleSlug)->findAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['page_key']] = $row;
        }
        return $result;
    }

    /**
     * Ambil semua permissions semua role, di-index [role_slug][page_key]
     */
    public function getAllGrouped(): array
    {
        $rows = $this->findAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['role_slug']][$row['page_key']] = $row;
        }
        return $result;
    }

    /**
     * Simpan/update permissions untuk satu role sekaligus
     * $permissions = ['dashboard' => ['create'=>1,'read'=>1,'update'=>0,'delete'=>0], ...]
     */
    public function saveForRole(string $roleSlug, array $permissions): void
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // Hapus permissions lama
        $db->table($this->table)->where('role_slug', $roleSlug)->delete();

        // Insert baru
        $rows = [];
        foreach ($permissions as $pageKey => $perms) {
            $rows[] = [
                'role_slug'  => $roleSlug,
                'page_key'   => $pageKey,
                'can_create' => (int) ($perms['create'] ?? 0),
                'can_read'   => (int) ($perms['read']   ?? 0),
                'can_update' => (int) ($perms['update'] ?? 0),
                'can_delete' => (int) ($perms['delete'] ?? 0),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rows)) {
            $db->table($this->table)->insertBatch($rows);
        }
    }

    /**
     * Copy permissions dari satu role ke role baru
     */
    public function copyFromRole(string $sourceSlug, string $targetSlug): void
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $sourceRows = $this->where('role_slug', $sourceSlug)->findAll();
        $newRows = [];
        foreach ($sourceRows as $row) {
            $newRows[] = [
                'role_slug'  => $targetSlug,
                'page_key'   => $row['page_key'],
                'can_create' => $row['can_create'],
                'can_read'   => $row['can_read'],
                'can_update' => $row['can_update'],
                'can_delete' => $row['can_delete'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($newRows)) {
            $db->table($this->table)->insertBatch($newRows);
        }
    }

    /**
     * Hapus semua permissions untuk satu role
     */
    public function deleteByRole(string $roleSlug): void
    {
        $this->where('role_slug', $roleSlug)->delete();
    }
}
