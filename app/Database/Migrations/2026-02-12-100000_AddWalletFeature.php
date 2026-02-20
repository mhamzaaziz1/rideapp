<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWalletFeature extends Migration
{
    public function up()
    {
        // Add wallet_balance to customers
        $this->forge->addColumn('customers', [
            'wallet_balance' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'after' => 'total_spent'
            ]
        ]);

        // Add wallet_balance to drivers
        $this->forge->addColumn('drivers', [
            'wallet_balance' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'after' => 'rating'
            ]
        ]);

        // Create transactions table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_type' => [
                'type'       => 'ENUM',
                'constraint' => ['customer', 'driver'],
            ],
            'user_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['deposit', 'withdrawal', 'payment', 'refund', 'commission'],
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'transaction_id' => [
                 'type'       => 'VARCHAR',
                 'constraint' => '255',
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('wallet_transactions');
    }

    public function down()
    {
        $this->forge->dropColumn('customers', 'wallet_balance');
        $this->forge->dropColumn('drivers', 'wallet_balance');
        $this->forge->dropTable('wallet_transactions');
    }
}
