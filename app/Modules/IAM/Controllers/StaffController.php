<?php

namespace Modules\IAM\Controllers;

use App\Controllers\BaseController;
use Modules\IAM\Models\UserModel;
use Modules\IAM\Models\RoleModel;

class StaffController extends BaseController
{
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('users.*, roles.name as role_name, roles.description as role_desc');
        $builder->join('users_roles', 'users_roles.user_id = users.id', 'left');
        $builder->join('roles', 'roles.id = users_roles.role_id', 'left');
        $builder->where('users.deleted_at', null);
        $builder->orderBy('users.created_at', 'DESC');
        
        $data = [
            'staff' => $builder->get()->getResult(),
            'title' => 'Staff Management'
        ];
        return view('Modules\IAM\Views\staff\index', $data);
    }

    public function new()
    {
        $data = [
            'staff' => new \Modules\IAM\Entities\User(),
            'roles' => $this->roleModel->findAll(),
            'title' => 'Add New Staff Member'
        ];
        return view('Modules\IAM\Views\staff\form', $data);
    }

    public function create()
    {
        $rules = [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|valid_email|is_unique[users.email]',
            'role_id'    => 'required', // Needs to be handled manually as pivot if many-to-many, currently assuming User belongs to Role for simplicity? No, Migration said pivot.
            'password'   => 'required|min_length[6]'
        ];
        
        // Wait, migration `users_roles` implies Many-to-Many.
        // But for typical Staff app, One-to-One (One Role per User) is easier UI.
        // I will implement "Primary Role" logic in the UI but store in pivot.
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'status' => 'active'
        ];

        if ($this->userModel->insert($userData)) {
            $userId = $this->userModel->getInsertID();
            
            // Assign Role
            $db = \Config\Database::connect();
            $db->table('users_roles')->insert([
                'user_id' => $userId,
                'role_id' => $this->request->getPost('role_id')
            ]);
            
            return redirect()->to('/staff')->with('success', 'Staff member created');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create user');
    }
    
    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/staff')->with('error', 'Staff member not found');
        }

        // Get Role
        $db = \Config\Database::connect();
        $role = $db->table('users_roles')->where('user_id', $id)->get()->getRow();
        $user->role_id = $role ? $role->role_id : null;

        $data = [
            'staff' => $user,
            'roles' => $this->roleModel->findAll(),
            'title' => 'Edit Staff Member'
        ];
        return view('Modules\IAM\Views\staff\form', $data);
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/staff')->with('error', 'Staff member not found');
        }

        $rules = [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => "required|valid_email|is_unique[users.email,id,$id]",
            'role_id'    => 'required'
        ];

        // Validating password only if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'status' => $this->request->getPost('status'),
        ];

        if (!empty($password)) {
            $userData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->userModel->update($id, $userData)) {
            // Update Role
            $db = \Config\Database::connect();
            $db->table('users_roles')->where('user_id', $id)->delete();
            $db->table('users_roles')->insert([
                'user_id' => $id,
                'role_id' => $this->request->getPost('role_id')
            ]);
            
            return redirect()->to('/staff')->with('success', 'Staff member updated');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update user');
    }
    
    public function delete($id)
    {
         if ($this->userModel->delete($id)) {
             return redirect()->to('/staff')->with('success', 'Staff member deleted');
         }
         return redirect()->to('/staff')->with('error', 'Failed to delete staff member');
    }
}
