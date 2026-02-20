<?php

namespace Modules\Customer\Models;

use CodeIgniter\Model;

class CustomerAddressModel extends Model
{
    protected $table = 'customer_addresses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object'; // Or entity if we want
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'customer_id',
        'type',
        'address',
        'city',
        'state',
        'zip_code',
        'latitude',
        'longitude',
        'is_default',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'customer_id' => 'required|integer',
        'type'        => 'required|max_length[50]',
        'address'     => 'required',
        'latitude'    => 'permit_empty|decimal',
        'longitude'   => 'permit_empty|decimal',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    /**
     * Unset other default addresses for the customer if this one is set as default.
     */
    public function unsetOtherDefaults(int $customerId, int $excludeAddressId = 0)
    {
        $this->builder()
             ->where('customer_id', $customerId)
             ->where('id !=', $excludeAddressId)
             ->set(['is_default' => 0])
             ->update();
    }
}
