<?php

namespace Modules\Pricing\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePricingZonesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pricing_rule_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100', // e.g., "JFK Airport"
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => '255', // e.g., "Manhattan -> JFK"
                'null'       => true,
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
            ],
            'is_active' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pricing_rule_id', 'pricing_rules', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pricing_zones');
    }

    public function down()
    {
        $this->forge->dropTable('pricing_zones');
    }
}
