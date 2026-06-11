<?php

namespace App\Modules\Support\Models;

use CodeIgniter\Model;

class ConversationModel extends Model
{
    protected $table      = 'chat_conversations';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = ['user_id', 'user_type', 'guest_name', 'guest_email', 'guest_phone', 'subject', 'agent_id', 'status', 'created_at', 'updated_at'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getActiveConversation($userId, $userType)
    {
        return $this->where('user_id', $userId)
                    ->where('user_type', $userType)
                    ->where('status !=', 'closed')
                    ->first();
    }
}
