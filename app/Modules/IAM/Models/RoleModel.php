<?php

namespace Modules\IAM\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object'; // Or Entity
    protected $allowedFields    = ['name', 'description'];

    // Method to get permissions for a role
    public function getPermissions($roleId)
    {
        $db = \Config\Database::connect();
        return $db->table('roles_permissions')
                  ->join('permissions', 'permissions.id = roles_permissions.permission_id')
                  ->where('role_id', $roleId)
                  ->get()->getResult();
    }
}
