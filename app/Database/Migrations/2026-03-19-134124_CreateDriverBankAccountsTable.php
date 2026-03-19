<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDriverBankAccountsTable extends Migration
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
            'driver_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'bank_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'account_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'account_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'routing_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'swift_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'is_default' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
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
        $this->forge->addForeignKey('driver_id', 'drivers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('driver_bank_accounts');
    }

    public function down()
    {
        $this->forge->dropTable('driver_bank_accounts');
    }
}
