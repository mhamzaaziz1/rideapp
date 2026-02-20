<?php

namespace Modules\IAM\Services;

use Firebase\JWT\JWT;
use Modules\IAM\Entities\User;
use Modules\IAM\Models\UserModel;
use Exception;

class AuthService
{
    protected $userModel;
    protected $key;

    public function __construct()
    {
        $this->userModel = new UserModel();
        // In production, get this from .env
        $this->key = getenv('encryption.key') ?: 'your-secret-key-CHANGE-ME-IN-PROD';
    }

    public function register(array $data): User
    {
        $user = new User($data);
        $user->setPassword($data['password']);
        
        if (!$this->userModel->save($user)) {
             throw new Exception(implode(', ', $this->userModel->errors()));
        }

        return $this->userModel->find($this->userModel->getInsertID());
    }

    public function login(string $email, string $password): string
    {
        $user = $this->userModel->where('email', $email)->first();

        if (!$user || !$user->verifyPassword($password)) {
            throw new Exception("Invalid credentials");
        }

        if ($user->status !== 'active') {
             throw new Exception("User account is " . $user->status);
        }

        return $this->generateJwt($user);
    }

    private function generateJwt(User $user): string
    {
        $payload = [
            'iss'  => base_url(),
            'sub'  => $user->id,
            'iat'  => time(),
            'exp'  => time() + (60 * 60 * 24), // 24 hours
            'role' => 'user' // Placeholder for RBAC roles
        ];

        return JWT::encode($payload, $this->key, 'HS256');
    }
}
