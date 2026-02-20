<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTripsTable extends Migration
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
            'trip_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'driver_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'dispatching', 'active', 'completed', 'cancelled', 'scheduled'],
                'default'    => 'pending',
            ],
            'pickup_address' => [
                'type' => 'TEXT',
            ],
            'dropoff_address' => [
                'type' => 'TEXT',
            ],
            'pickup_lat' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
                'null'       => true,
            ],
            'pickup_lng' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
            ],
            'dropoff_lat' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
                'null'       => true,
            ],
            'dropoff_lng' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
            ],
            'distance_miles' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2', 
                'default'    => 0.00
            ],
            'duration_minutes' => [
                'type'       => 'INT',
                'default'    => 0
            ],
            'fare_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00
            ],
            'vehicle_type' => [
                 'type'       => 'VARCHAR', // e.g. Sedan, SUV
                 'constraint' => 50,
                 'default'    => 'Sedan'
            ],
            'passengers' => [
                'type'       => 'INT',
                'default'    => 1
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'scheduled_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'started_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('trips');
    }

    public function down()
    {
        $this->forge->dropTable('trips');
    }
}
