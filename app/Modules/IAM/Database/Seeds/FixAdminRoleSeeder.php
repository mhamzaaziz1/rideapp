<?php

namespace App\Modules\IAM\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Fixes admin user role assignment
 */
class FixAdminRoleSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $adminUser = $db->table('users')->where('email', 'admin@rideflow.app')->get()->getRow();
        $adminRole = $db->table('roles')->where('name', 'Admin')->get()->getRow();

        if (!$adminUser || !$adminRole) {
            echo "❌ Admin user or role not found\n";
            return;
        }

        echo "User ID: {$adminUser->id}, Role ID: {$adminRole->id}\n";

        // Remove any existing role assignments for admin
        $db->table('users_roles')->where('user_id', $adminUser->id)->delete();

        // Assign Admin role
        $db->table('users_roles')->insert([
            'user_id' => $adminUser->id,
            'role_id' => $adminRole->id,
        ]);

        echo "✅ Admin role assigned to user '{$adminUser->email}'\n";
    }
}
