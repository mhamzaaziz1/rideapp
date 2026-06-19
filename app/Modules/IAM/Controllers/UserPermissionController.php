<?php

namespace App\Modules\IAM\Controllers;

use App\Controllers\BaseController;
use App\Modules\IAM\Models\UserModel;
use App\Modules\IAM\Models\PermissionModel;
use App\Modules\IAM\Services\PermissionService;

/**
 * UserPermissionController - Manage direct user-level permission overrides
 * 
 * This handles the advanced per-user permission assignment that overrides
 * role-level permissions (e.g., deny a specific permission for one user
 * even though their role grants it, or grant an extra permission).
 */
class UserPermissionController extends BaseController
{
    protected $userModel;
    protected $permModel;
    protected $permService;

    public function __construct()
    {
        helper('Modules\IAM\Helpers\permission');
        $this->userModel   = new UserModel();
        $this->permModel   = new PermissionModel();
        $this->permService = new PermissionService();
    }

    /**
     * Show user permission management page
     */
    public function manage($userId)
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to('/staff')->with('error', 'User not found.');
        }

        $db = \Config\Database::connect();

        // Get user's role
        $userRole = $db->table('users_roles')
                       ->join('roles', 'roles.id = users_roles.role_id')
                       ->where('user_id', $userId)
                       ->get()
                       ->getRow();

        // Get role permissions
        $rolePermIds = [];
        if ($userRole) {
            $rolePermIds = $this->permModel->getIdsForRole($userRole->role_id);
        }

        // Get direct user overrides
        $directOverrides = $this->permModel->getDirectForUser($userId);

        $data = [
            'user'            => $user,
            'userRole'        => $userRole,
            'groupedPerms'    => $this->permModel->getGrouped(),
            'rolePermIds'     => $rolePermIds,
            'directOverrides' => $directOverrides,
            'effectivePerms'  => $this->permService->getEffectivePermissions($userId),
            'title'           => "Permissions: {$user->first_name} {$user->last_name}",
        ];

        return view('Modules\IAM\Views\permissions\user_manage', $data);
    }

    /**
     * Save user-level permission overrides
     */
    public function save($userId)
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to('/staff')->with('error', 'User not found.');
        }

        $db = \Config\Database::connect();
        $actorId = session('user_id') ?? 0;

        // Clear existing direct overrides
        $db->table('user_permissions')
           ->where('user_id', $userId)
           ->delete();

        // Process submitted overrides
        $overrides = $this->request->getPost('overrides') ?? [];
        // Format: overrides[permission_id] = 'grant' | 'deny' | 'inherit'

        foreach ($overrides as $permId => $action) {
            if ($action === 'grant') {
                $this->permService->grantUserPermission($userId, (int)$permId, $actorId);
            } elseif ($action === 'deny') {
                $this->permService->denyUserPermission($userId, (int)$permId, $actorId);
            }
            // 'inherit' = no override, falls back to role
        }

        $this->permService->clearCache();

        return redirect()->to("/staff/permissions/$userId")
            ->with('success', 'User permissions updated successfully.');
    }

    /**
     * AJAX: Toggle a single user permission
     */
    public function toggle()
    {
        $userId = (int) $this->request->getPost('user_id');
        $permId = (int) $this->request->getPost('permission_id');
        $action = $this->request->getPost('action'); // 'grant', 'deny', 'inherit'
        $actorId = session('user_id') ?? 0;

        if (!$userId || !$permId) {
            return $this->response->setJSON(['error' => 'Invalid parameters'])->setStatusCode(400);
        }

        $db = \Config\Database::connect();

        switch ($action) {
            case 'grant':
                $this->permService->grantUserPermission($userId, $permId, $actorId);
                break;
            case 'deny':
                $this->permService->denyUserPermission($userId, $permId, $actorId);
                break;
            case 'inherit':
                $this->permService->removeUserPermissionOverride($userId, $permId, $actorId);
                break;
            default:
                return $this->response->setJSON(['error' => 'Invalid action'])->setStatusCode(400);
        }

        // Return effective state
        $effective = $this->permService->userCan($userId, '');
        return $this->response->setJSON([
            'status' => 'success',
            'action' => $action,
        ]);
    }

    /**
     * View audit log
     */
    public function auditLog()
    {
        $db = \Config\Database::connect();

        $logs = $db->table('permission_audit_log')
                   ->join('users', 'users.id = permission_audit_log.actor_id', 'left')
                   ->select('permission_audit_log.*, users.first_name as actor_first, users.last_name as actor_last')
                   ->orderBy('permission_audit_log.created_at', 'DESC')
                   ->limit(100)
                   ->get()
                   ->getResult();

        $data = [
            'logs'  => $logs,
            'title' => 'Permission Audit Log',
        ];

        return view('Modules\IAM\Views\permissions\audit_log', $data);
    }
}
