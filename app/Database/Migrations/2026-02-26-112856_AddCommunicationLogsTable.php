<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCommunicationLogsTable extends Migration
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
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['sms', 'voice'],
            ],
            'direction' => [
                'type'       => 'ENUM',
                'constraint' => ['inbound', 'outbound'],
            ],
            'from_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'to_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'user_type' => [
                'type'       => 'ENUM',
                'constraint' => ['driver', 'customer', 'system', 'unknown'],
                'default'    => 'unknown',
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'action_taken' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('communication_logs');
    }

    public function down()
    {
        $this->forge->dropTable('communication_logs');
    }
}
