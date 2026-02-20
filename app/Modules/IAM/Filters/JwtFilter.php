<?php

namespace Modules\IAM\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = getenv('encryption.key') ?: 'your-secret-key-CHANGE-ME-IN-PROD';
        $header = $request->getHeaderLine("Authorization");
        $token = null;

        // Extract token from format: "Bearer <token>"
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $token = $matches[1];
            }
        }

        if (is_null($token)) {
             return \Config\Services::response()
                        ->setJSON(['error' => 'Token not provided'])
                        ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            // Store user data in Request for Controllers to access
            // Note: CI4 Request is immutable, but we can use generic property or Registry
            // For now, simpler to just allow through if valid.
        } catch (Exception $e) {
            return \Config\Services::response()
                        ->setJSON(['error' => 'Invalid Token: ' . $e->getMessage()])
                        ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
