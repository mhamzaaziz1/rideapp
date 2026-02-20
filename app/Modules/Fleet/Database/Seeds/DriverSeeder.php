<?php

namespace Modules\Fleet\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Modules\Fleet\Models\DriverModel;

class DriverSeeder extends Seeder
{
    public function run()
    {
        $model = new DriverModel();

        $drivers = [
            [
                'first_name' => 'Mike',
                'last_name' => 'Johnson',
                'email' => 'mike@email.com',
                'phone' => '+1 (555) 987-6543',
                'license_number' => 'DL12345678',
                'status' => 'active',
                'vehicle_make' => 'Toyota',
                'vehicle_model' => 'Camry',
                'vehicle_year' => 2024,
                'vehicle_color' => 'Black',
                'license_plate' => 'ABC-1234',
                'vehicle_type' => 'Sedan',
                'total_trips' => 158,
                'rating' => 4.9,
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Williams',
                'email' => 'sarah@email.com',
                'phone' => '+1 (555) 876-5432',
                'license_number' => 'DL87654321',
                'status' => 'active',
                'vehicle_make' => 'Honda',
                'vehicle_model' => 'Pilot',
                'vehicle_year' => 2023,
                'vehicle_color' => 'White',
                'license_plate' => 'XYZ-5678',
                'vehicle_type' => 'SUV',
                'total_trips' => 98,
                'rating' => 4.85,
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Chen',
                'email' => 'david@email.com',
                'phone' => '+1 (555) 765-4321',
                'license_number' => 'DL11223344',
                'status' => 'active',
                'vehicle_make' => 'Mercedes',
                'vehicle_model' => 'E-Class',
                'vehicle_year' => 2024,
                'vehicle_color' => 'Silver',
                'license_plate' => 'LUX-9012',
                'vehicle_type' => 'Luxury',
                'total_trips' => 72,
                'rating' => 5.0,
            ],
             [
                'first_name' => 'Emily',
                'last_name' => 'Rodriguez',
                'email' => 'emily@email.com',
                'phone' => '+1 (555) 654-3210',
                'license_number' => 'DL99887766',
                'status' => 'inactive',
                'vehicle_make' => 'Chrysler',
                'vehicle_model' => 'Pacifica',
                'vehicle_year' => 2023,
                'vehicle_color' => 'Blue',
                'license_plate' => 'VAN-3456',
                'vehicle_type' => 'Van',
                'total_trips' => 45,
                'rating' => 4.7,
            ],
        ];

        foreach ($drivers as $driver) {
            $model->save(new \Modules\Fleet\Entities\Driver($driver));
        }
    }
}
