<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table      = 'settings_menus';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['parent_id', 'title', 'icon', 'url', 'sort_order'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get menus hierarchical list for a user based on their roles/groups
     */
    public function getMenusForUser($user)
    {
        $db = \Config\Database::connect();
        
        if (!$user) {
            return [];
        }

        $groups = $user->getGroups();
        if (empty($groups)) {
            return [];
        }

        $allowedMenuIds = $db->table('settings_menu_roles mr')
            ->select('DISTINCT(mr.menu_id) as menu_id')
            ->whereIn('mr.group_name', $groups)
            ->get()
            ->getResultArray();
            
        $menuIds = array_column($allowedMenuIds, 'menu_id');
        if (empty($menuIds)) {
            return [];
        }

        $allMenus = $this->whereIn('id', $menuIds)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('title', 'ASC')
            ->findAll();

        // Index by id
        $byId = [];
        foreach ($allMenus as $menu) {
            $byId[$menu['id']] = $menu;
        }

        // Jika child diizinkan tapi parent/leluhurnya tidak eksplisit diizinkan, ambil info leluhurnya
        // (berlaku untuk semua level, bukan hanya 1 level di atas)
        $guard = 0;
        do {
            $missing = [];
            foreach ($byId as $menu) {
                $pId = $menu['parent_id'];
                if (!empty($pId) && $pId != $menu['id'] && !isset($byId[$pId])) {
                    $missing[$pId] = $pId;
                }
            }
            if (!empty($missing)) {
                foreach ($this->whereIn('id', array_values($missing))->findAll() as $p) {
                    $byId[$p['id']] = $p;
                }
            }
            $guard++;
        } while (!empty($missing) && $guard < 10);

        // Bangun tree secara rekursif (mendukung menu bertingkat: Parent > Sub > Sub-sub)
        $childrenOf = [];
        $roots = [];
        foreach ($byId as $id => $menu) {
            $pId = $menu['parent_id'];
            if (empty($pId) || $pId == $id || !isset($byId[$pId])) {
                $roots[] = $id;
            } else {
                $childrenOf[$pId][] = $id;
            }
        }

        $build = function (array $ids, int $depth) use (&$build, $byId, $childrenOf) {
            $items = [];
            foreach ($ids as $id) {
                $item = $byId[$id];
                $item['children'] = ($depth < 5 && !empty($childrenOf[$id]))
                    ? $build($childrenOf[$id], $depth + 1)
                    : [];
                $items[] = $item;
            }
            usort($items, function ($a, $b) {
                $cmp = (int)$a['sort_order'] - (int)$b['sort_order'];
                return $cmp !== 0 ? $cmp : strcmp((string)$a['title'], (string)$b['title']);
            });
            return $items;
        };

        return $build($roots, 0);
    }
}
