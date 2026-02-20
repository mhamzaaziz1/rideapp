<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLinkedDisputeToTrips extends Migration
{
    public function up()
    {
        $this->forge->addColumn('trips', [
            'linked_dispute_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'default' => null,
                'after' => 'trip_number',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('trips', 'linked_dispute_id');
    }
}
