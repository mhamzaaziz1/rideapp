<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefactorLedgerSystem extends Migration
{
    public function up()
    {
        // 1. Create the `ledgers` table. A ledger is an account holding funds (Customer, Driver, Company Revenue).
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'owner_type' => [
                'type'       => 'ENUM',
                'constraint' => ['customer', 'driver', 'company_revenue', 'system_escrow'],
                'comment'    => 'Who owns this ledger',
            ],
            'owner_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'NULL for company/system accounts',
            ],
            'balance' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,4',
                'default'    => 0.0000,
                'comment'    => 'Current holdings using robust decimal precision to prevent float rounding errors',
            ],
            'currency' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
                'default'    => 'USD',
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
        $this->forge->createTable('ledgers', true);

        // 2. Create the `ledger_transactions` table. Tracks money movements strictly between ledgers.
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'source_ledger_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true, // NULL implies External Deposit (e.g. Credit Card load)
                'comment'    => 'Sender wallet',
            ],
            'destination_ledger_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true, // NULL implies External Withdrawal to Bank
                'comment'    => 'Receiver wallet',
            ],
            'transaction_type' => [
                'type'       => 'ENUM',
                'constraint' => ['Deposit', 'Trip', 'Refund', 'Commission', 'Withdrawal', 'Adjustment'],
                'comment'    => 'Categorizes the movement of funds',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,4',
                'comment'    => 'Amount transferred. Must be recorded safely as Decimals.',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Completed', 'Failed', 'Reversed'],
                'default'    => 'Pending',
            ],
            'reference_id' => [
                 'type'       => 'VARCHAR',
                 'constraint' => 255,
                 'null'       => true,
                 'comment'    => 'Trip ID, Payout ID or PG Ref',
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
        $this->forge->addForeignKey('source_ledger_id', 'ledgers', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('destination_ledger_id', 'ledgers', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('ledger_transactions', true);
    }

    public function down()
    {
        $this->forge->dropTable('ledger_transactions', true);
        $this->forge->dropTable('ledgers', true);
    }
}
