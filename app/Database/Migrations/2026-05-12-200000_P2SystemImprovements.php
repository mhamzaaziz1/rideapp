<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * P2 Improvements Migration:
 * 1. Add resolved_at timestamp to disputes (SLA tracking)
 * 2. Upgrade wallet_transactions.amount precision from DECIMAL(10,2) to DECIMAL(15,4)
 * 3. Upgrade customers.wallet_balance precision from DECIMAL(10,2) to DECIMAL(15,4)
 * 4. Upgrade drivers.wallet_balance precision from DECIMAL(10,2) to DECIMAL(15,4)
 */
class P2SystemImprovements extends Migration
{
    public function up()
    {
        // 1. Add resolved_at to disputes for SLA tracking
        if ($this->db->tableExists('disputes')) {
            $this->forge->addColumn('disputes', [
                'resolved_at' => [
                    'type'  => 'DATETIME',
                    'null'  => true,
                    'after' => 'resolved_by',
                ],
            ]);
        }

        // 2. Upgrade wallet precision to match ledger system (DECIMAL(15,4))
        if ($this->db->tableExists('wallet_transactions')) {
            $this->forge->modifyColumn('wallet_transactions', [
                'amount' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,4',
                ],
            ]);
        }

        if ($this->db->tableExists('customers')) {
            $this->forge->modifyColumn('customers', [
                'wallet_balance' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,4',
                    'default'    => 0.0000,
                ],
            ]);
        }

        if ($this->db->tableExists('drivers')) {
            $this->forge->modifyColumn('drivers', [
                'wallet_balance' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,4',
                    'default'    => 0.0000,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('disputes')) {
            $this->forge->dropColumn('disputes', 'resolved_at');
        }

        if ($this->db->tableExists('wallet_transactions')) {
            $this->forge->modifyColumn('wallet_transactions', [
                'amount' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                ],
            ]);
        }

        if ($this->db->tableExists('customers')) {
            $this->forge->modifyColumn('customers', [
                'wallet_balance' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 0.00,
                ],
            ]);
        }

        if ($this->db->tableExists('drivers')) {
            $this->forge->modifyColumn('drivers', [
                'wallet_balance' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 0.00,
                ],
            ]);
        }
    }
}
