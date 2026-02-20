<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDriverEarningsToTrips extends Migration
{
    public function up()
    {
        $fields = [
            'driver_earnings' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'after'      => 'fare_amount'
            ],
            'commission_amount' => [ // How much the platform keeps
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'after'      => 'driver_earnings'
            ],
            'surcharge_amount' => [ // Surge/Peak pricing extra
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'after'      => 'commission_amount'
            ]
        ];

        $this->forge->addColumn('trips', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('trips', ['driver_earnings', 'commission_amount', 'surcharge_amount']);
    }
}
