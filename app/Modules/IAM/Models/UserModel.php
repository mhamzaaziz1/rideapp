<?php

namespace Modules\IAM\Models;

use CodeIgniter\Model;
use Modules\IAM\Entities\User;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = User::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'email', 'password_hash', 'first_name', 'last_name', 'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules      = [
        'email'      => 'required|valid_email|is_unique[users.email,id,{id}]',
        'first_name' => 'required|min_length[2]',
        'last_name'  => 'required|min_length[2]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
