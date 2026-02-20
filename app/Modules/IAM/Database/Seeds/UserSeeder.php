<?php

namespace Modules\IAM\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Modules\IAM\Services\AuthService;

class UserSeeder extends Seeder
{
    public function run()
    {
        $service = new AuthService();
        
        try {
            $service->register([
                'email'      => 'admin@rideflow.app',
                'password'   => 'password123',
                'first_name' => 'System',
                'last_name'  => 'Admin',
                'status'     => 'active'
            ]);
            echo "Admin user created successfully.\n";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
