<?php

namespace Modules\Dispatch\Entities;

use CodeIgniter\Entity\Entity;

class Dispute extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [
        'id'          => 'integer',
        'trip_id'     => 'integer',
        'customer_id' => 'integer',
        'driver_id'   => 'integer',
        'resolved_by' => 'integer',
    ];
}
