<?php

namespace Modules\IAM\Services;

use Modules\IAM\Models\UserModel;
use Modules\IAM\Models\RoleModel;
use Modules\IAM\Models\PermissionModel;

/**
 * PermissionService - Central permission checking engine
 * 
 * Permission resolution order:
 * 1. If user has 'Admin' role → always granted (superadmin bypass)
 * 2. Check user-level overrides (user_permissions table) — deny overrides grant
 * 3. Check role-level permissions (roles_permissions table)
 */
class PermissionService
{
    protected $userModel;
    protected $roleModel;
    protected $permModel;

    // Cache to avoid repeated DB queries per request
    protected static $cache = [];

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->permModel = new PermissionModel();
    }

    /**
     * Check if the current logged-in user has a given permission
     */
    public function currentUserCan(string $permission): bool
    {
        $userId = $this->getCurrentUserId();
        if (!$userId) {
            return false;
        }
        return $this->userCan($userId, $permission);
    }

    /**
     * Check if a specific user has a given permission
     * 
     * @param int    $userId     The user's ID
     * @param string $permission Permission slug (e.g., 'dispatch.trips.create')
     */
    public function userCan(int $userId, string $permission): bool
    {
        $cacheKey = $userId . ':' . $permission;
        if (isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }

        $result = $this->resolvePermission($userId, $permission);
        self::$cache[$cacheKey] = $result;
        return $result;
    }

    /**
     * Core resolution logic
     */
    protected function resolvePermission(int $userId, string $permission): bool
    {
        $db = \Config\Database::connect();

        // 1. Get user's role(s)
        $userRoles = $db->table('users_roles')
                        ->join('roles', 'roles.id = users_roles.role_id')
                        ->where('user_id', $userId)
                        ->get()
                        ->getResult();

        // 2. Admin bypass — if any role is "Admin", grant everything
        foreach ($userRoles as $role) {
            if (strtolower($role->name) === 'admin') {
                return true;
            }
        }

        // 3. Get the permission ID
        $perm = $db->table('permissions')
                   ->where('name', $permission)
                   ->get()
                   ->getRow();

        if (!$perm) {
            // Permission doesn't exist in system — deny by default
            return false;
        }

        // 4. Check user-level overrides first (most specific wins)
        $directOverride = $db->table('user_permissions')
                             ->where('user_id', $userId)
                             ->where('permission_id', $perm->id)
                             ->get()
                             ->getRow();

        if ($directOverride) {
            return (bool)$directOverride->granted;
        }

        // 5. Check role-level permissions
        foreach ($userRoles as $role) {
            $roleHas = $db->table('roles_permissions')
                          ->where('role_id', $role->id)
                          ->where('permission_id', $perm->id)
                          ->countAllResults();
            if ($roleHas > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has ANY of the given permissions
     */
    public function currentUserCanAny(array $permissions): bool
    {
        foreach ($permissions as $perm) {
            if ($this->currentUserCan($perm)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has ALL of the given permissions
     */
    public function currentUserCanAll(array $permissions): bool
    {
        foreach ($permissions as $perm) {
            if (!$this->currentUserCan($perm)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get all effective permissions for a user (merged from roles + overrides)
     */
    public function getEffectivePermissions(int $userId): array
    {
        $db = \Config\Database::connect();
        $allPerms = $this->permModel->findAll();
        $effective = [];

        foreach ($allPerms as $perm) {
            $effective[$perm->name] = $this->userCan($userId, $perm->name);
        }

        return $effective;
    }

    /**
     * Check if user is an Admin
     */
    public function isAdmin(?int $userId = null): bool
    {
        $userId = $userId ?? $this->getCurrentUserId();
        if (!$userId) return false;

        $db = \Config\Database::connect();
        $roles = $db->table('users_roles')
                    ->join('roles', 'roles.id = users_roles.role_id')
                    ->where('user_id', $userId)
                    ->get()
                    ->getResult();

        foreach ($roles as $role) {
            if (strtolower($role->name) === 'admin') {
                return true;
            }
        }
        return false;
    }

    /**
     * Get current user's role name
     */
    public function getCurrentUserRole(): ?string
    {
        $userId = $this->getCurrentUserId();
        if (!$userId) return null;

        $db = \Config\Database::connect();
        $role = $db->table('users_roles')
                   ->join('roles', 'roles.id = users_roles.role_id')
                   ->where('user_id', $userId)
                   ->get()
                   ->getRow();

        return $role ? $role->name : null;
    }

    /**
     * Assign a direct user-level permission override
     */
    public function grantUserPermission(int $userId, int $permissionId, int $actorId = 0): void
    {
        $db = \Config\Database::connect();

        // Upsert
        $existing = $db->table('user_permissions')
                       ->where('user_id', $userId)
                       ->where('permission_id', $permissionId)
                       ->get()
                       ->getRow();

        if ($existing) {
            $db->table('user_permissions')
               ->where('id', $existing->id)
               ->update(['granted' => 1]);
        } else {
            $db->table('user_permissions')->insert([
                'user_id'       => $userId,
                'permission_id' => $permissionId,
                'granted'       => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        $this->logAudit($actorId, 'permission_granted', 'user', $userId, "Permission ID: $permissionId");
        self::$cache = []; // Bust cache
    }

    /**
     * Deny a direct user-level permission (override)
     */
    public function denyUserPermission(int $userId, int $permissionId, int $actorId = 0): void
    {
        $db = \Config\Database::connect();

        $existing = $db->table('user_permissions')
                       ->where('user_id', $userId)
                       ->where('permission_id', $permissionId)
                       ->get()
                       ->getRow();

        if ($existing) {
            $db->table('user_permissions')
               ->where('id', $existing->id)
               ->update(['granted' => 0]);
        } else {
            $db->table('user_permissions')->insert([
                'user_id'       => $userId,
                'permission_id' => $permissionId,
                'granted'       => 0,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        $this->logAudit($actorId, 'permission_denied', 'user', $userId, "Permission ID: $permissionId");
        self::$cache = [];
    }

    /**
     * Remove a direct user-level permission override (fall back to role)
     */
    public function removeUserPermissionOverride(int $userId, int $permissionId, int $actorId = 0): void
    {
        $db = \Config\Database::connect();
        $db->table('user_permissions')
           ->where('user_id', $userId)
           ->where('permission_id', $permissionId)
           ->delete();

        $this->logAudit($actorId, 'permission_override_removed', 'user', $userId, "Permission ID: $permissionId");
        self::$cache = [];
    }

    /**
     * Log an audit trail entry
     */
    public function logAudit(int $actorId, string $action, string $targetType, int $targetId, string $details = ''): void
    {
        $db = \Config\Database::connect();
        $db->table('permission_audit_log')->insert([
            'actor_id'    => $actorId,
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'details'     => $details,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get the current user ID from session
     */
    protected function getCurrentUserId(): ?int
    {
        $session = session();
        $userId = $session->get('user_id');
        return $userId ? (int)$userId : null;
    }

    /**
     * Clear the permission cache (useful after bulk operations)
     */
    public function clearCache(): void
    {
        self::$cache = [];
    }
}
