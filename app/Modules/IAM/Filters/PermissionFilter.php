<?php

namespace App\Modules\IAM\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Modules\IAM\Services\PermissionService;

/**
 * PermissionFilter - Route-level permission enforcement
 * 
 * Usage in routes:
 *   $routes->get('drivers', 'DriversController::index', ['filter' => 'permission:fleet.drivers.view']);
 *   $routes->post('create', 'DriversController::create', ['filter' => 'permission:fleet.drivers.create']);
 * 
 * Or in group filters:
 *   $routes->group('admin', ['filter' => 'permission:iam.staff.view'], function($routes) { ... });
 */
class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Load the helper
        helper('App\Modules\IAM\Helpers\permission');

        $session = session();
        $userId = $session->get('user_id');

        // If no user is logged in, redirect to login
        if (!$userId) {
            if ($request->isAJAX()) {
                return \Config\Services::response()
                    ->setJSON(['error' => 'Authentication required'])
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        // If no specific permission arguments, just check authentication
        if (empty($arguments)) {
            return;
        }

        $permService = new PermissionService();

        // Check each permission argument — user must have ALL listed
        foreach ($arguments as $permission) {
            if (!$permService->userCan((int)$userId, $permission)) {
                if ($request->isAJAX()) {
                    return \Config\Services::response()
                        ->setJSON([
                            'error'   => 'Access denied',
                            'message' => "You do not have the required permission: $permission",
                        ])
                        ->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
                }

                // For web requests, redirect with error
                return redirect()->back()->with('error', 'You do not have permission to perform this action.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing after
    }
}
