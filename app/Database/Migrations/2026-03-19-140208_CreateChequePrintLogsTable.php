<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChequePrintLogsTable extends Migration
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
            'transaction_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'printed_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'printed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('transaction_id', 'wallet_transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cheque_print_logs');
    }

    public function down()
    {
        $this->forge->dropTable('cheque_print_logs');
    }
}
