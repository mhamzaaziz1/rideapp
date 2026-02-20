<?php

namespace Modules\Fleet\Models;

use CodeIgniter\Model;
use Modules\Fleet\Entities\Driver;

class DriverModel extends Model
{
    protected $table            = 'drivers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Driver::class;
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'first_name', 'last_name', 'email', 'phone', 'license_number', 'status',
        'vehicle_make', 'vehicle_model', 'vehicle_year', 'vehicle_color', 'license_plate', 'vehicle_type',
        'total_trips', 'rating', 'current_lat', 'current_lng', 'last_active_at',
        'kyc_status', 'doc_license_front', 'doc_license_back', 'doc_id_proof',
        'doc_license_front_status', 'doc_license_back_status', 'doc_id_proof_status',
        'avatar', 'vehicle_image', 'wallet_balance', 'commission_rate'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[drivers.email,id,{id}]',
        'first_name' => 'required',
        'last_name'  => 'required',
    ];
}
