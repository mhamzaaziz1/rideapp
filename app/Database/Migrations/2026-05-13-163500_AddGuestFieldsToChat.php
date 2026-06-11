<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGuestFieldsToChat extends Migration
{
    public function up()
    {
        $this->forge->addColumn('chat_conversations', [
            'guest_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'user_type'
            ],
            'guest_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'guest_name'
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'guest_email'
            ],
        ]);

        // Also allow user_id to be null for guest chats
        $this->db->query("ALTER TABLE chat_conversations MODIFY user_id INT(11) UNSIGNED NULL");
    }

    public function down()
    {
        $this->forge->dropColumn('chat_conversations', ['guest_name', 'guest_email', 'subject']);
        $this->db->query("ALTER TABLE chat_conversations MODIFY user_id INT(11) UNSIGNED NOT NULL");
    }
}
