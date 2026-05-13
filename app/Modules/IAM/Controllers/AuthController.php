<?php

namespace Modules\IAM\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Modules\IAM\Services\AuthService;
use Exception;

class AuthController extends ResourceController
{
    protected $authService;
    protected $format = 'json';

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Show login page (web)
     */
    public function index()
    {
        // If already logged in via session, redirect to dashboard
        if (session('user_id')) {
            return redirect()->to('/dispatch');
        }
        
        $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $this->response->setHeader('Cache-Control', 'post-check=0, pre-check=0', false);
        $this->response->setHeader('Pragma', 'no-cache');
        
        return view('Modules\IAM\Views\login');
    }

    /**
     * API: Register
     */
    public function register()
    {
        $data = $this->request->getJSON(true);

        try {
            $user = $this->authService->register($data);
            return $this->respondCreated([
                'status' => 'success',
                'data'   => [
                    'id'    => $user->id,
                    'email' => $user->email
                ]
            ]);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * API: Login (JSON) — returns JWT token + sets session
     */
    public function login()
    {
        // Check database connection first
        try {
            $db = \Config\Database::connect();
            $db->initialize();
        } catch (\Throwable $e) {
            $dbConfig = config('Database');
            $group = $dbConfig->defaultGroup ?? 'default';
            $creds = $dbConfig->{$group} ?? [];

            $host = $creds['hostname'] ?? 'unknown';
            $user = $creds['username'] ?? 'unknown';
            $pass = $creds['password'] ?? 'unknown';
            $dbName = $creds['database'] ?? 'unknown';

            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'error' => 'Database not connected. Credentials used - Host: ' . $host . ', User: ' . $user . ', Password: ' . $pass . ', DB: ' . $dbName
            ]);
            exit;
        }

        // Raw input reading to avoid CI framework filtering issues for now
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        try {
            if (!isset($data['email']) || !isset($data['password'])) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Email and password are required.']);
                exit;
            }

            $token = $this->authService->login($data['email'], $data['password']);
            
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'token' => $token]);
            exit;

        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * Web: Form-based login (POST from login form)
     */
    public function attemptLogin()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // If submitted via AJAX/JSON (login page JS), delegate to API login
        if ($this->request->isAJAX() || $this->request->getHeaderLine('Content-Type') === 'application/json') {
            return $this->login();
        }

        if (empty($email) || empty($password)) {
            return redirect()->to('/login')->with('error', 'Email and password are required.');
        }

        try {
            $user = $this->authService->webLogin($email, $password);
            return redirect()->to('/dispatch')->with('success', 'Welcome back, ' . $user->first_name . '!');
        } catch (Exception $e) {
            return redirect()->to('/login')->with('error', $e->getMessage());
        }
    }

    /**
     * Logout — destroy session and redirect
     */
    public function logout()
    {
        $this->authService->logout();

        // Clear JWT from client-side via a simple page
        return view('Modules\IAM\Views\logout');
    }
}
