<?php

namespace Modules\CallCenter\Models;

use CodeIgniter\Model;
use Modules\CallCenter\Entities\CallLog;

class CallLogModel extends Model
{
    protected $table            = 'call_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = CallLog::class;
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'caller_name', 'caller_number', 'direction', 'status', 'duration', 'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'caller_number' => 'required',
        'status'        => 'required',
    ];
}
