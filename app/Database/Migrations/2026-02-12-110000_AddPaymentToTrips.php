<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentToTrips extends Migration
{
    public function up()
    {
        $this->forge->addColumn('trips', [
            'payment_method' => [
                'type' => 'ENUM',
                'constraint' => ['cash', 'card', 'wallet'],
                'default' => 'cash',
                'after' => 'fare_amount'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('trips', 'payment_method');
    }
}
