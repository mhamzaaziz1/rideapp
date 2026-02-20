<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCallLogsTable extends Migration
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
            'caller_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'caller_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'direction' => [
                'type'       => 'ENUM',
                'constraint' => ['inbound', 'outbound'],
                'default'    => 'inbound',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['missed', 'answered', 'voicemail', 'completed', 'busy'],
                'default'    => 'answered',
            ],
            'duration' => [
                'type'       => 'INT',
                'default'    => 0, // In seconds
            ],
            'notes' => [
                'type'       => 'TEXT',
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
        $this->forge->createTable('call_logs');
    }

    public function down()
    {
        $this->forge->dropTable('call_logs');
    }
}
