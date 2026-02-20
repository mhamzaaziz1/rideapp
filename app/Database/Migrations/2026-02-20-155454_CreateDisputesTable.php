<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDisputesTable extends Migration
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
            'trip_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, // Might be general dispute? usually trip related
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
            'reported_by' => [
                'type'       => 'ENUM',
                'constraint' => ['customer', 'driver'],
                'default'    => 'customer',
            ],
            'dispute_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['open', 'investigating', 'resolved', 'closed'],
                'default'    => 'open',
            ],
            'resolution' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'resolved_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addKey('trip_id');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('driver_id');
        $this->forge->addKey('status');
        $this->forge->createTable('disputes');
    }

    public function down()
    {
        $this->forge->dropTable('disputes', true);
    }
}
