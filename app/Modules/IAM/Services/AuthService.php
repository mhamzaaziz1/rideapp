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

        // Store user data in session for web-based authentication
        $this->setUserSession($user);

        return $this->generateJwt($user);
    }

    /**
     * Web-only login (form POST) — sets session and returns user
     */
    public function webLogin(string $email, string $password): User
    {
        $user = $this->userModel->where('email', $email)->first();

        if (!$user || !$user->verifyPassword($password)) {
            throw new Exception("Invalid email or password.");
        }

        if ($user->status !== 'active') {
            throw new Exception("Your account is " . $user->status . ". Please contact an administrator.");
        }

        $this->setUserSession($user);
        return $user;
    }

    /**
     * Store user identity in session
     */
    protected function setUserSession(User $user): void
    {
        $db = \Config\Database::connect();
        $roleRow = $db->table('users_roles')
                      ->join('roles', 'roles.id = users_roles.role_id')
                      ->where('user_id', $user->id)
                      ->get()
                      ->getRow();

        $session = session();
        $session->set([
            'user_id'    => $user->id,
            'email'      => $user->email,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'role_id'    => $roleRow ? $roleRow->id : null,
            'role_name'  => $roleRow ? $roleRow->name : null,
            'logged_in'  => true,
        ]);
    }

    /**
     * Destroy session on logout
     */
    public function logout(): void
    {
        $session = session();
        $session->destroy();
    }

    private function generateJwt(User $user): string
    {
        $db = \Config\Database::connect();
        $roleRow = $db->table('users_roles')
                      ->join('roles', 'roles.id = users_roles.role_id')
                      ->where('user_id', $user->id)
                      ->get()
                      ->getRow();

        $payload = [
            'iss'  => base_url(),
            'sub'  => $user->id,
            'iat'  => time(),
            'exp'  => time() + (60 * 60 * 24), // 24 hours
            'role' => $roleRow ? $roleRow->name : 'user',
        ];

        return JWT::encode($payload, $this->key, 'HS256');
    }
}
