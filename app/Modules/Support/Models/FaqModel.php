<?php

namespace App\Modules\Support\Models;

use CodeIgniter\Model;

class FaqModel extends Model
{
    protected $table      = 'chat_faq';
    protected $primaryKey = 'id';

    protected $allowedFields = ['question_keyword', 'answer', 'category', 'created_at', 'updated_at'];

    protected $useTimestamps = true;

    public function findAnswer($query)
    {
        // Simple keyword search
        return $this->like('question_keyword', $query)
                    ->first();
    }
}
