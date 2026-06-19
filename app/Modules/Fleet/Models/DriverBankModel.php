<?php

namespace App\Modules\Fleet\Models;

use CodeIgniter\Model;

class DriverBankModel extends Model
{
    protected $table            = 'driver_bank_accounts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'driver_id', 'bank_name', 'account_name', 'account_number', 'routing_number', 'swift_code', 'is_default'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function setDefault($accountId, $driverId)
    {
        $this->db->transStart();
        
        // Remove default from all other records for this driver
        $this->where('driver_id', $driverId)->set(['is_default' => 0])->update();
        
        // Set new default
        $this->update($accountId, ['is_default' => 1]);
        
        $this->db->transComplete();
        return $this->db->transStatus();
    }
}
