<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChatSystem extends Migration
{
    public function up()
    {
        // 1. chat_conversations table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'user_type' => [
                'type'       => 'ENUM',
                'constraint' => ['customer', 'driver'],
                'default'    => 'customer',
            ],
            'agent_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['open', 'closed', 'bot_active', 'agent_active'],
                'default'    => 'bot_active',
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
        $this->forge->addKey('user_id');
        $this->forge->addKey('status');
        $this->forge->createTable('chat_conversations', true);

        // 2. chat_messages table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'conversation_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'sender_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0, // 0 for bot
            ],
            'sender_role' => [
                'type'       => 'ENUM',
                'constraint' => ['user', 'agent', 'bot'],
                'default'    => 'user',
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'is_read' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('conversation_id');
        $this->forge->createTable('chat_messages', true);

        // 3. chat_faq table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'question_keyword' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'answer' => [
                'type' => 'TEXT',
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'general',
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
        $this->forge->addKey('question_keyword');
        $this->forge->createTable('chat_faq', true);
    }

    public function down()
    {
        $this->forge->dropTable('chat_messages', true);
        $this->forge->dropTable('chat_conversations', true);
        $this->forge->dropTable('chat_faq', true);
    }
}
