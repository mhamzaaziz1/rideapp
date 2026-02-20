<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKycToDrivers extends Migration
{
    public function up()
    {
        $fields = [
            'kyc_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
                'after'      => 'status'
            ],
            'doc_license_front' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'kyc_status'
            ],
            'doc_license_back' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'doc_license_front'
            ],
            'doc_id_proof' => [ // e.g. Passport or National ID
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'doc_license_back'
            ],
        ];

        $this->forge->addColumn('drivers', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('drivers', ['kyc_status', 'doc_license_front', 'doc_license_back', 'doc_id_proof']);
    }
}
