<?php

namespace Modules\Dispatch\Models;

use CodeIgniter\Model;

class RatingModel extends Model
{
    protected $table            = 'ratings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // or Entity
    protected $useSoftDeletes   = false; // Maybe true if we allow undoing
    protected $allowedFields    = [
        'trip_id', 'rater_type', 'rater_id', 'ratee_type', 'ratee_id', 'rating', 'comment'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'trip_id'    => 'required|integer',
        'rater_type' => 'required|in_list[driver,customer]',
        'rater_id'   => 'required|integer',
        'ratee_type' => 'required|in_list[driver,customer]',
        'ratee_id'   => 'required|integer',
        'rating'     => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
    ];

    // Relationships or helper methods
    public function getCustomerRatings(int $customerId)
    {
        return $this->where('ratee_type', 'customer')
                    ->where('ratee_id', $customerId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getDriverRatings(int $driverId)
    {
        return $this->where('ratee_type', 'driver')
                    ->where('ratee_id', $driverId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
