<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCommissionRateToDrivers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('drivers', [
            'commission_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 25.00
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('drivers', 'commission_rate');
    }
}
