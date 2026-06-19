<?php

namespace App\Modules\IAM\Controllers;

use App\Controllers\BaseController;
use App\Modules\IAM\Models\RoleModel;
use App\Modules\IAM\Models\PermissionModel;
use App\Modules\IAM\Services\PermissionService;

class RoleController extends BaseController
{
    protected $roleModel;
    protected $permModel;
    protected $permService;

    public function __construct()
    {
        helper('App\Modules\IAM\Helpers\permission');
        $this->roleModel   = new RoleModel();
        $this->permModel   = new PermissionModel();
        $this->permService = new PermissionService();
    }

    /**
     * List all roles
     */
    public function index()
    {
        $data = [
            'roles' => $this->roleModel->getAllWithCounts(),
            'title' => 'Roles & Permissions',
        ];
        return view('App\Modules\IAM\Views\roles\index', $data);
    }

    /**
     * Create new role form
     */
    public function new()
    {
        $data = [
            'role'              => null,
            'groupedPerms'      => $this->permModel->getGrouped(),
            'assignedPermIds'   => [],
            'title'             => 'Create New Role',
        ];
        return view('App\Modules\IAM\Views\roles\form', $data);
    }

    /**
     * Store new role
     */
    public function create()
    {
        $rules = [
            'name'        => 'required|min_length[2]|is_unique[roles.name]',
            'description' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $roleData = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'is_system'   => 0,
            'is_default'  => $this->request->getPost('is_default') ? 1 : 0,
        ];

        if ($this->roleModel->insert($roleData)) {
            $roleId = $this->roleModel->getInsertID();

            // Sync permissions
            $permIds = $this->request->getPost('permissions') ?? [];
            $this->roleModel->syncPermissions($roleId, $permIds);

            // Audit log
            $this->permService->logAudit(
                session('user_id') ?? 0,
                'role_created',
                'role',
                $roleId,
                "Role '{$roleData['name']}' created with " . count($permIds) . " permissions"
            );

            return redirect()->to('/roles')->with('success', "Role '{$roleData['name']}' created successfully.");
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create role.');
    }

    /**
     * Edit role form
     */
    public function edit($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return redirect()->to('/roles')->with('error', 'Role not found.');
        }

        $data = [
            'role'              => $role,
            'groupedPerms'      => $this->permModel->getGrouped(),
            'assignedPermIds'   => $this->permModel->getIdsForRole($id),
            'title'             => "Edit Role: {$role->name}",
        ];
        return view('App\Modules\IAM\Views\roles\form', $data);
    }

    /**
     * Update role
     */
    public function update($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return redirect()->to('/roles')->with('error', 'Role not found.');
        }

        // Prevent editing system roles' name
        $rules = [
            'name'        => "required|min_length[2]|is_unique[roles.name,id,$id]",
            'description' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $roleData = [
            'name'        => $role->is_system ? $role->name : $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'is_default'  => $this->request->getPost('is_default') ? 1 : 0,
        ];

        if ($this->roleModel->update($id, $roleData)) {
            // Sync permissions
            $permIds = $this->request->getPost('permissions') ?? [];
            $this->roleModel->syncPermissions($id, $permIds);

            // Audit log
            $this->permService->logAudit(
                session('user_id') ?? 0,
                'role_updated',
                'role',
                $id,
                "Role '{$roleData['name']}' updated with " . count($permIds) . " permissions"
            );

            return redirect()->to('/roles')->with('success', "Role '{$roleData['name']}' updated successfully.");
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update role.');
    }

    /**
     * Delete role
     */
    public function delete($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return redirect()->to('/roles')->with('error', 'Role not found.');
        }

        if ($role->is_system) {
            return redirect()->to('/roles')->with('error', 'Cannot delete a system role.');
        }

        // Check if any users are assigned
        $userCount = $this->roleModel->getUserCount($id);
        if ($userCount > 0) {
            return redirect()->to('/roles')->with('error', "Cannot delete role '{$role->name}' — $userCount user(s) are still assigned.");
        }

        // Clean up permissions
        $db = \Config\Database::connect();
        $db->table('roles_permissions')->where('role_id', $id)->delete();

        if ($this->roleModel->delete($id)) {
            $this->permService->logAudit(
                session('user_id') ?? 0,
                'role_deleted',
                'role',
                $id,
                "Role '{$role->name}' deleted"
            );
            return redirect()->to('/roles')->with('success', "Role '{$role->name}' deleted.");
        }

        return redirect()->to('/roles')->with('error', 'Failed to delete role.');
    }

    /**
     * View role details and its assigned users
     */
    public function view($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return redirect()->to('/roles')->with('error', 'Role not found.');
        }

        $db = \Config\Database::connect();

        // Users with this role
        $users = $db->table('users')
                    ->join('users_roles', 'users_roles.user_id = users.id')
                    ->where('users_roles.role_id', $id)
                    ->where('users.deleted_at', null)
                    ->select('users.*')
                    ->get()
                    ->getResult();

        $data = [
            'role'        => $role,
            'permissions' => $this->roleModel->getPermissions($id),
            'users'       => $users,
            'title'       => "Role: {$role->name}",
        ];
        return view('App\Modules\IAM\Views\roles\view', $data);
    }

    /**
     * AJAX: Get permission matrix data for a role
     */
    public function getPermissions($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return $this->response->setJSON(['error' => 'Role not found'])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'role'        => $role,
            'permissions' => $this->roleModel->getPermissions($id),
            'permIds'     => $this->permModel->getIdsForRole($id),
        ]);
    }
}
