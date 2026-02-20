<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAttachmentToDisputes extends Migration
{
    public function up()
    {
        $fields = [
            'attachment' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'description', // Place it right behind description
            ],
        ];

        $this->forge->addColumn('disputes', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('disputes', 'attachment');
    }
}
