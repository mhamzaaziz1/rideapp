<?php

namespace Modules\Dispatch\Models;

use CodeIgniter\Model;
use Modules\Dispatch\Entities\Trip;

class TripModel extends Model
{
    protected $table            = 'trips';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Trip::class;
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'trip_number', 'customer_id', 'driver_id', 'status',
        'pickup_address', 'dropoff_address', 'pickup_lat', 'pickup_lng', 'dropoff_lat', 'dropoff_lng',
        'distance_miles', 'duration_minutes', 'fare_amount', 'driver_earnings', 'commission_amount', 'surcharge_amount', 'vehicle_type', 'passengers', 'notes',
        'scheduled_at', 'started_at', 'completed_at', 'payment_method'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'pickup_address'  => 'required',
        'dropoff_address' => 'required',
        'vehicle_type'    => 'required',
    ];
}
