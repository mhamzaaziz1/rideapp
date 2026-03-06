<?php

namespace Modules\Dispatch\Models;

use CodeIgniter\Model;
use Modules\Dispatch\Entities\CommunicationLog;

class CommunicationLogModel extends Model
{
    protected $table            = 'communication_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = CommunicationLog::class;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'type', 'direction', 'from_number', 'to_number', 'user_type', 
        'user_id', 'content', 'action_taken'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Custom query to get rich logs with user details if applicable
    public function getLogsWithDetails($limit = 50, $offset = 0)
    {
        return $this->select('communication_logs.*, 
            COALESCE(drivers.first_name, customers.first_name) as user_first_name,
            COALESCE(drivers.last_name, customers.last_name) as user_last_name')
            ->join('drivers', 'drivers.id = communication_logs.user_id AND communication_logs.user_type = \'driver\'', 'left')
            ->join('customers', 'customers.id = communication_logs.user_id AND communication_logs.user_type = \'customer\'', 'left')
            ->orderBy('communication_logs.created_at', 'DESC')
            ->findAll($limit, $offset);
    }
}
