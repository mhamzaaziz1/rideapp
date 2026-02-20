<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImagesToDrivers extends Migration
{
    public function up()
    {
        $fields = [
            'avatar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'email'
            ],
            'vehicle_image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'license_plate'
            ],
        ];

        $this->forge->addColumn('drivers', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('drivers', ['avatar', 'vehicle_image']);
    }
}
