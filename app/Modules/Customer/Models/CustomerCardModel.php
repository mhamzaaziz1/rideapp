<?php

namespace Modules\Customer\Models;

use CodeIgniter\Model;

class CustomerCardModel extends Model
{
    protected $table            = 'customer_cards';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'customer_id',
        'card_brand',
        'card_last_four',
        'expiry_month',
        'expiry_year',
        'card_holder_name',
        'is_default',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'customer_id'    => 'required|integer',
        'card_brand'     => 'required|max_length[50]',
        'card_last_four' => 'required|exact_length[4]',
        'expiry_month'   => 'required|integer|greater_than[0]|less_than[13]',
        'expiry_year'    => 'required|integer|min_length[2]|max_length[4]',
    ];

    /**
     * Unset other default cards for the customer if this one is set as default.
     */
    public function unsetOtherDefaults(int $customerId, int $excludeCardId = 0)
    {
        $this->builder()
             ->where('customer_id', $customerId)
             ->where('id !=', $excludeCardId)
             ->set(['is_default' => 0])
             ->update();
    }
}
