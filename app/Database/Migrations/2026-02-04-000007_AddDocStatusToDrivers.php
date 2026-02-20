<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDocStatusToDrivers extends Migration
{
    public function up()
    {
        $fields = [
            'doc_license_front_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
                'after'      => 'doc_license_front'
            ],
            'doc_license_back_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
                'after'      => 'doc_license_back'
            ],
            'doc_id_proof_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
                'after'      => 'doc_id_proof'
            ],
        ];

        $this->forge->addColumn('drivers', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('drivers', ['doc_license_front_status', 'doc_license_back_status', 'doc_id_proof_status']);
    }
}
