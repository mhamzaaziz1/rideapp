<?php

namespace Modules\Pricing\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePricingPeakHoursTable extends Migration
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
            'day_of_week' => [
                'type'       => 'VARCHAR',
                'constraint' => '20', // Monday, Tuesday, etc.
            ],
            'start_time' => [
                'type' => 'TIME',
            ],
            'end_time' => [
                'type' => 'TIME',
            ],
            'multiplier' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '1.00',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pricing_rule_id', 'pricing_rules', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pricing_peak_hours');
    }

    public function down()
    {
        $this->forge->dropTable('pricing_peak_hours');
    }
}
