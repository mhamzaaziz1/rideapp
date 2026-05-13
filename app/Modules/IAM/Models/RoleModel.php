<?php

namespace Modules\IAM\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = ['name', 'description', 'is_system', 'is_default'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Get permissions for a role
     */
    public function getPermissions(int $roleId): array
    {
        $db = \Config\Database::connect();
        return $db->table('roles_permissions')
                  ->join('permissions', 'permissions.id = roles_permissions.permission_id')
                  ->where('role_id', $roleId)
                  ->get()
                  ->getResult();
    }

    /**
     * Get permission names (slugs) for a role
     */
    public function getPermissionNames(int $roleId): array
    {
        $perms = $this->getPermissions($roleId);
        return array_map(fn($p) => $p->name, $perms);
    }

    /**
     * Sync permissions for a role (replace all)
     */
    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        $db = \Config\Database::connect();

        // Remove existing
        $db->table('roles_permissions')
           ->where('role_id', $roleId)
           ->delete();

        // Insert new
        foreach ($permissionIds as $permId) {
            $db->table('roles_permissions')->insert([
                'role_id'       => $roleId,
                'permission_id' => (int) $permId,
            ]);
        }
    }

    /**
     * Count users assigned to this role
     */
    public function getUserCount(int $roleId): int
    {
        $db = \Config\Database::connect();
        return $db->table('users_roles')
                  ->where('role_id', $roleId)
                  ->countAllResults();
    }

    /**
     * Get all roles with user counts
     */
    public function getAllWithCounts(): array
    {
        $roles = $this->findAll();
        foreach ($roles as &$role) {
            $role->user_count = $this->getUserCount($role->id);
            $role->permission_count = count($this->getPermissions($role->id));
        }
        return $roles;
    }
}
