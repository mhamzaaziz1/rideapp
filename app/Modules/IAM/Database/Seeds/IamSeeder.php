<?php

namespace Modules\IAM\Database\Seeds;

use CodeIgniter\Database\Seeder;

class IamSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Roles
        $roles = [
            ['name' => 'Admin', 'description' => 'Full access to all modules'],
            ['name' => 'Dispatcher', 'description' => 'Can manage trips and drivers'],
            ['name' => 'Finance', 'description' => 'Access to billing and reports'],
            ['name' => 'Driver Manager', 'description' => 'Can onboard and manage drivers']
        ];

        foreach ($roles as $role) {
            // Check if exists
            if ($db->table('roles')->where('name', $role['name'])->countAllResults() == 0) {
                $db->table('roles')->insert($role);
            }
        }

        // 2. Default Admin User
        $email = 'admin@rideflow.app';
        if ($db->table('users')->where('email', $email)->countAllResults() == 0) {
            $data = [
                'email' => $email,
                'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
                'first_name' => 'System',
                'last_name' => 'Admin',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ];
            $db->table('users')->insert($data);
            $userId = $db->insertID();

            // Assign Admin Role
            $adminRoleId = $db->table('roles')->where('name', 'Admin')->get()->getRow()->id;
            $db->table('users_roles')->insert(['user_id' => $userId, 'role_id' => $adminRoleId]);
        }
    }
}
