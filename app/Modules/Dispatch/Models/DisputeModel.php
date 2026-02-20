<?php

namespace Modules\Dispatch\Models;

use CodeIgniter\Model;
use Modules\Dispatch\Entities\Dispute;

class DisputeModel extends Model
{
    protected $table            = 'disputes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Dispute::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'trip_id',
        'customer_id',
        'driver_id',
        'reported_by',
        'dispute_type',
        'title',
        'description',
        'attachment',
        'status',
        'resolution',
        'resolved_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'trip_id'      => 'required|numeric',
        'reported_by'  => 'required|in_list[customer,driver]',
        'dispute_type' => 'required',
        'title'        => 'required',
        'description'  => 'required'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
    
    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    public function getDisputesWithDetails()
    {
        return $this->select('disputes.*, t.trip_number, 
                              c.first_name as c_first_name, c.last_name as c_last_name, 
                              d.first_name as d_first_name, d.last_name as d_last_name,
                              u.first_name as admin_first_name, u.last_name as admin_last_name')
                    ->join('trips t', 't.id = disputes.trip_id', 'left')
                    ->join('customers c', 'c.id = disputes.customer_id', 'left')
                    ->join('drivers d', 'd.id = disputes.driver_id', 'left')
                    ->join('users u', 'u.id = disputes.resolved_by', 'left')
                    ->orderBy('disputes.id', 'DESC')
                    ->findAll();
    }
}
