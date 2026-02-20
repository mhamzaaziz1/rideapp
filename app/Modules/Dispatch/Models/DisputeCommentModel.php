<?php

namespace Modules\Dispatch\Models;

use CodeIgniter\Model;

class DisputeCommentModel extends Model
{
    protected $table = 'dispute_comments';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'dispute_id', 'user_id', 'comment'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getCommentsWithUser($disputeId) 
    {
        return $this->select('dispute_comments.*, users.first_name, users.last_name')
            ->join('users', 'users.id = dispute_comments.user_id', 'left')
            ->where('dispute_id', $disputeId)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }
}
