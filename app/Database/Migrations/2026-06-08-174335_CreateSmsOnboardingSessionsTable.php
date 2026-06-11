<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSmsOnboardingSessionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'phone_number' => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'state'        => ['type' => 'VARCHAR', 'constraint' => 50],
            'data'         => ['type' => 'TEXT', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('sms_onboarding_sessions');
    }

    public function down()
    {
        $this->forge->dropTable('sms_onboarding_sessions');
    }
}
