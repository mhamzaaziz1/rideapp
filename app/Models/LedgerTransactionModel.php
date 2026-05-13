<?php

namespace App\Models;

use CodeIgniter\Model;

class LedgerTransactionModel extends Model
{
    protected $table            = 'ledger_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['source_ledger_id', 'destination_ledger_id', 'transaction_type', 'amount', 'status', 'reference_id'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
