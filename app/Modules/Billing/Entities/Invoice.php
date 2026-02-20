<?php

namespace Modules\Billing\Entities;

use CodeIgniter\Entity\Entity;

class Invoice extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at', 'issued_at', 'paid_at'];
    protected $casts   = [
        'amount' => 'double',
        'customer_id' => 'integer',
        'trip_id' => 'integer',
    ];
}
