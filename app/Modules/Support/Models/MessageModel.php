<?php

namespace App\Modules\Support\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table      = 'chat_messages';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = ['conversation_id', 'sender_id', 'sender_role', 'message', 'is_read', 'created_at'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // No updated_at for messages

    public function getMessagesByConversation($conversationId)
    {
        return $this->where('conversation_id', $conversationId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }
}
