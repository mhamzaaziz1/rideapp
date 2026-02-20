<?php

namespace Modules\Pricing\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePricingRulesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'vehicle_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'base_fare' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
            ],
            'distance_rate_per_mile' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
            ],
            'time_rate_per_minute' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
            ],
            'minimum_fare' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pricing_rules');

        // Seed Default Data
        $data = [
            [
                'vehicle_type' => 'Sedan',
                'base_fare' => 5.00,
                'distance_rate_per_mile' => 2.00,
                'time_rate_per_minute' => 0.50,
                'minimum_fare' => 10.00,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'vehicle_type' => 'SUV',
                'base_fare' => 8.00,
                'distance_rate_per_mile' => 3.00,
                'time_rate_per_minute' => 0.75,
                'minimum_fare' => 15.00,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'vehicle_type' => 'Van',
                'base_fare' => 10.00,
                'distance_rate_per_mile' => 3.50,
                'time_rate_per_minute' => 0.80,
                'minimum_fare' => 20.00,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'vehicle_type' => 'Luxury',
                'base_fare' => 15.00,
                'distance_rate_per_mile' => 5.00,
                'time_rate_per_minute' => 1.50,
                'minimum_fare' => 30.00,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('pricing_rules')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('pricing_rules');
    }
}
