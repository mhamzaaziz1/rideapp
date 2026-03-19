<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentMethodToWalletTransactionsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('wallet_transactions', [
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'type'
            ],
            'bank_account_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'payment_method'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('wallet_transactions', ['payment_method', 'bank_account_id']);
    }
}
