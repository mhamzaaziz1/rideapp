<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhoneToChatGuest extends Migration
{
    public function up()
    {
        $this->forge->addColumn('chat_conversations', [
            'guest_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'guest_email'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('chat_conversations', 'guest_phone');
    }
}
