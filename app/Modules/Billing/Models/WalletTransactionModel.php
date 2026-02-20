<?php

namespace Modules\Billing\Models;

use CodeIgniter\Model;

class WalletTransactionModel extends Model
{
    protected $table            = 'wallet_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'user_type', 'user_id', 'type', 'amount', 'description', 'transaction_id'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'user_type' => 'required|in_list[customer,driver]',
        'user_id'   => 'required|integer',
        'type'      => 'required|in_list[deposit,withdrawal,payment,refund,commission]',
        'amount'    => 'required|numeric',
    ];
}
