<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * Resolve CRUD permission flags for the current user on a given page_key.
     *
     * Returns array with keys: can_create, can_update, can_delete
     * (can_read is assumed true since the user is already on the page).
     */
    protected function getPagePermissions(string $pageKey): array
    {
        $canCreate = false;
        $canUpdate = false;
        $canDelete = false;

        $user = auth()->user();
        if (!$user) {
            return compact('canCreate', 'canUpdate', 'canDelete');
        }

        $groups = $user->getGroups();

        // Admin always has full access
        if (in_array('admin', $groups)) {
            return ['canCreate' => true, 'canUpdate' => true, 'canDelete' => true];
        }

        $permModel = new \App\Models\RolePermissionModel();
        foreach ($groups as $grp) {
            $perms = $permModel->getByRole($grp);
            if (!empty($perms[$pageKey])) {
                if ($perms[$pageKey]['can_create']) $canCreate = true;
                if ($perms[$pageKey]['can_update']) $canUpdate = true;
                if ($perms[$pageKey]['can_delete']) $canDelete = true;
            }
        }

        return compact('canCreate', 'canUpdate', 'canDelete');
    }

    /**
     * Check if the current user can modify (edit/delete) a resource based on role & content ownership.
     *
     * - Super Admin (admin) & BPH (bph) can modify ANY resource.
     * - Other roles (seksi, custom roles, kaderisasi, user) can only modify resources THEY created,
     *   AND they must have the corresponding permission (can_update / can_delete).
     *
     * @param int|string|null $creatorUserId The user ID who created the resource
     * @param string $action 'update' or 'delete'
     * @param string $pageKey Module page key (e.g. 'news', 'booking', 'documents', 'kegiatan', 'aset')
     * @return bool
     */
    protected function canModifyResource(int|string|null $creatorUserId, string $action = 'update', string $pageKey = ''): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        $groups = $user->getGroups();

        // Super Admin & BPH always have full authority over all content
        if (in_array('admin', $groups) || in_array('bph', $groups)) {
            return true;
        }

        // Check page permission if pageKey is provided
        if (!empty($pageKey)) {
            $perms = $this->getPagePermissions($pageKey);
            if ($action === 'update' && empty($perms['canUpdate'])) {
                return false;
            }
            if ($action === 'delete' && empty($perms['canDelete'])) {
                return false;
            }
        }

        // Ownership rule: only creator can modify
        return (int)$creatorUserId === (int)$user->id;
    }
}
