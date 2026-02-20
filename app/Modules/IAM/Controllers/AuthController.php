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

    public function index()
    {
        return view('Modules\IAM\Views\login');
    }

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

    public function login()
    {
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

    public function logout()
    {
        return view('Modules\IAM\Views\logout');
    }
}
