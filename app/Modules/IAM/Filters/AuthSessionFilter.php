<?php

namespace App\Modules\IAM\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthSessionFilter - Ensures user is logged in via session
 * 
 * Checks that session('user_id') is set. If not, redirects to login page.
 * This is different from JwtFilter which handles API authentication.
 */
class AuthSessionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('user_id')) {
            if ($request->isAJAX()) {
                return \Config\Services::response()
                    ->setJSON(['error' => 'Authentication required. Please log in.'])
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }
            return redirect()->to('/login')->with('error', 'You must be logged in to access this page.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing after
    }
}
