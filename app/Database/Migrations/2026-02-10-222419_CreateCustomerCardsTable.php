<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomerCardsTable extends Migration
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
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'card_brand' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'card_last_four' => [
                'type'       => 'VARCHAR',
                'constraint' => '4',
            ],
            'expiry_month' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'expiry_year' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'card_holder_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'is_default' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('customer_cards');
    }

    public function down()
    {
        $this->forge->dropTable('customer_cards');
    }
}
