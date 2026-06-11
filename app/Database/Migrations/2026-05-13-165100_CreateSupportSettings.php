<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSupportSettings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('setting_key', true);
        $this->forge->createTable('support_settings', true);

        // Insert defaults
        $db = \Config\Database::connect();
        $db->table('support_settings')->insertBatch([
            ['setting_key' => 'ai_provider', 'setting_value' => 'openai'],
            ['setting_key' => 'openai_key', 'setting_value' => ''],
            ['setting_key' => 'claude_key', 'setting_value' => ''],
            ['setting_key' => 'gemini_key', 'setting_value' => ''],
            ['setting_key' => 'ai_system_prompt', 'setting_value' => 'You are the official support assistant for RideApp. Be helpful, professional, and concise.'],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('support_settings', true);
    }
}
