<?php

namespace Modules\Customer\Models;

use CodeIgniter\Model;
use Modules\Customer\Entities\Customer;

class CustomerModel extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Customer::class;
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'first_name', 'last_name', 'email', 'phone', 'avatar',
        'status', 'rating', 'total_trips', 'total_spent', 'wallet_balance'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'first_name' => 'required|min_length[2]',
        'last_name'  => 'required|min_length[2]',
        'email'      => 'required|valid_email|is_unique[customers.email,id,{id}]',
        'phone'      => 'required|min_length[10]',
    ];
}
