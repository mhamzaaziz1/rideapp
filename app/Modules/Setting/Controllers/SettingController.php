<?php

namespace Modules\Setting\Controllers;

use App\Controllers\BaseController;
use Modules\IAM\Models\UserModel;
use Modules\IAM\Models\RoleModel;
use Modules\IAM\Models\PermissionModel;

class SettingController extends BaseController
{
    protected $settingsFile;

    public function __construct()
    {
        $this->settingsFile = WRITEPATH . 'settings.json';
    }

    public function index()
    {
        $settings = [];
        if (file_exists($this->settingsFile)) {
            $settings = json_decode(file_get_contents($this->settingsFile), true) ?? [];
        }

        $tab = $this->request->getGet('tab') ?? 'general';
        $staff = [];

        if ($tab == 'account') {
            $db = \Config\Database::connect();
            $builder = $db->table('users');
            $builder->select('users.*, roles.name as role_name, roles.description as role_desc');
            $builder->join('users_roles', 'users_roles.user_id = users.id', 'left');
            $builder->join('roles', 'roles.id = users_roles.role_id', 'left');
            $builder->where('users.deleted_at', null);
            $builder->orderBy('users.created_at', 'DESC');
            $staff = $builder->get()->getResult();
        }

        $roles = [];
        $permissions = [];
        $rolePermissions = [];

        if ($tab == 'permissions') {
            $roleModel = new RoleModel();
            $permissionModel = new PermissionModel();
            
            $roles = $roleModel->findAll();
            $permissions = $permissionModel->findAll();
            
            $db = \Config\Database::connect();
            $existing = $db->table('roles_permissions')->get()->getResult();
            
            foreach ($existing as $row) {
                $rolePermissions[$row->role_id][] = $row->permission_id;
            }
        }

        $data = [
            'title' => 'Settings',
            'title' => 'Settings',
            'tab' => $tab,
            'settings' => $settings,
            'staff' => $staff,
            'roles' => $roles,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions
        ];

        return view('Modules\Setting\Views\index', $data);
    }

    public function update()
    {
        $tab = $this->request->getPost('tab') ?? 'general';
        
        // Load existing settings
        $settings = [];
        if (file_exists($this->settingsFile)) {
            $settings = json_decode(file_get_contents($this->settingsFile), true) ?? [];
        }

        // Handle File Upload (Company Logo)
        $file = $this->request->getFile('company_logo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/settings', $newName);
            $settings['company_logo'] = $newName;
        }

        // Handle specific fields based on tab
        if ($tab == 'general') {
            $fields = [
                'company_name', 'company_address', 'company_city', 
                'company_state', 'company_country_code', 'company_zip_code', 
                'company_phone', 'company_vat'
            ];
            
            foreach ($fields as $field) {
                if ($this->request->getPost($field) !== null) {
                    $settings[$field] = $this->request->getPost($field);
                }
            }
        } elseif ($tab == 'permissions') {
            $perms = $this->request->getPost('perms');
            $db = \Config\Database::connect();
            
            // Clear all existing permissions first (simplest approach for matrix update)
            $db->table('roles_permissions')->truncate();
            
            if ($perms && is_array($perms)) {
                $data = [];
                foreach ($perms as $roleId => $rolePerms) {
                    foreach ($rolePerms as $permId => $val) {
                        $data[] = [
                            'role_id' => $roleId,
                            'permission_id' => $permId
                        ];
                    }
                }
                
                if (!empty($data)) {
                    $db->table('roles_permissions')->insertBatch($data);
                }
            }
            
            return redirect()->to(base_url('settings?tab=permissions'))->with('success', 'Permissions updated successfully.');
        }

        // Save back to file
        file_put_contents($this->settingsFile, json_encode($settings, JSON_PRETTY_PRINT));

        return redirect()->to(base_url('settings?tab=' . $tab))->with('success', 'Settings updated successfully.');
    }
}
