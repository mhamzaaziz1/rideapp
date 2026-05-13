<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSmsMessagesTable extends Migration
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
            'provider' => [
                'type'       => 'ENUM',
                'constraint' => ['twilio', 'telnyx'],
                'default'    => 'twilio',
            ],
            'direction' => [
                'type'       => 'ENUM',
                'constraint' => ['inbound', 'outbound'],
                'default'    => 'outbound',
            ],
            'from_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'to_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'body' => [
                'type' => 'TEXT',
            ],
            'external_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'queued',
                'comment'    => 'queued, sent, delivered, failed, received',
            ],
            'related_trip_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'related_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'error_message' => [
                'type' => 'TEXT',
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
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('from_number');
        $this->forge->addKey('to_number');
        $this->forge->addKey('provider');
        $this->forge->addKey('direction');
        $this->forge->addKey('status');
        $this->forge->addKey('external_id');

        $this->forge->createTable('sms_messages', true);
    }

    public function down()
    {
        $this->forge->dropTable('sms_messages', true);
    }
}
