<?php

namespace Modules\Dispatch\Entities;

use CodeIgniter\Entity\Entity;

class Trip extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at', 'scheduled_at', 'started_at', 'completed_at'];
    protected $casts   = [
        'pickup_lat'       => 'double',
        'pickup_lng'       => 'double',
        'dropoff_lat'      => 'double',
        'dropoff_lng'      => 'double',
        'distance_miles'   => 'double',
        'fare_amount'      => 'double',
        'duration_minutes' => 'integer',
        'passengers'       => 'integer',
        'customer_id'      => 'integer',
        'driver_id'        => 'integer',
    ];

    public function generateTripNumber()
    {
        $this->trip_number = 'TRP-' . strtoupper(substr(uniqid(), -6));
    }
}
