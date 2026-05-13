<?php

namespace Modules\IAM\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table            = 'permissions';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $allowedFields    = ['name', 'module', 'description', 'group_name'];

    /**
     * Get all permissions grouped by module -> resource
     */
    public function getGrouped(): array
    {
        $perms = $this->orderBy('module', 'ASC')
                      ->orderBy('group_name', 'ASC')
                      ->orderBy('name', 'ASC')
                      ->findAll();

        $grouped = [];
        foreach ($perms as $p) {
            $module = $p->module ?? 'General';
            $group  = $p->group_name ?? 'Other';
            $grouped[$module][$group][] = $p;
        }
        return $grouped;
    }

    /**
     * Get permission IDs for a given role
     */
    public function getIdsForRole(int $roleId): array
    {
        $db = \Config\Database::connect();
        $rows = $db->table('roles_permissions')
                   ->where('role_id', $roleId)
                   ->get()
                   ->getResult();

        return array_map(fn($r) => (int)$r->permission_id, $rows);
    }

    /**
     * Get permission IDs directly assigned/denied for a user
     */
    public function getDirectForUser(int $userId): array
    {
        $db = \Config\Database::connect();
        $rows = $db->table('user_permissions')
                   ->where('user_id', $userId)
                   ->get()
                   ->getResult();

        $result = [];
        foreach ($rows as $row) {
            $result[(int)$row->permission_id] = (int)$row->granted;
        }
        return $result; // [permission_id => 1|0]
    }

    /**
     * Get all distinct modules
     */
    public function getModules(): array
    {
        return $this->distinct()
                    ->select('module')
                    ->where('module IS NOT NULL')
                    ->orderBy('module')
                    ->findAll();
    }
}
