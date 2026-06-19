<?php

namespace App\Modules\IAM\Config;

use Config\Services;

$routes = Services::routes();

// =========================================================
// Public Auth Routes (no auth required)
// =========================================================
$routes->group('api/auth', ['namespace' => 'App\Modules\IAM\Controllers'], function ($routes) {
    $routes->post('register', 'AuthController::register');
    $routes->post('login', 'AuthController::login');
});

$routes->get('login', 'AuthController::index', ['namespace' => 'App\Modules\IAM\Controllers']);
$routes->get('logout', 'AuthController::logout', ['namespace' => 'App\Modules\IAM\Controllers']);
$routes->post('login', 'AuthController::attemptLogin', ['namespace' => 'App\Modules\IAM\Controllers']);

// Legacy API Routes
$routes->get('api_login.php', 'AuthController::index', ['namespace' => 'App\Modules\IAM\Controllers']);
$routes->post('api_login.php', 'AuthController::login', ['namespace' => 'App\Modules\IAM\Controllers']);

// =========================================================
// Staff Management Routes
// =========================================================
$routes->group('staff', ['namespace' => 'App\Modules\IAM\Controllers'], function ($routes) {
    $routes->get('/', 'StaffController::index');
    $routes->get('new', 'StaffController::new');
    $routes->post('create', 'StaffController::create');
    $routes->get('edit/(:num)', 'StaffController::edit/$1');
    $routes->post('update/(:num)', 'StaffController::update/$1');
    $routes->get('delete/(:num)', 'StaffController::delete/$1');

    // User-level Permission Management
    $routes->get('permissions/(:num)', 'UserPermissionController::manage/$1');
    $routes->post('permissions/save/(:num)', 'UserPermissionController::save/$1');
    $routes->post('permissions/toggle', 'UserPermissionController::toggle');
});

// =========================================================
// Role Management Routes
// =========================================================
$routes->group('roles', ['namespace' => 'App\Modules\IAM\Controllers'], function ($routes) {
    $routes->get('/', 'RoleController::index');
    $routes->get('new', 'RoleController::new');
    $routes->post('create', 'RoleController::create');
    $routes->get('view/(:num)', 'RoleController::view/$1');
    $routes->get('edit/(:num)', 'RoleController::edit/$1');
    $routes->post('update/(:num)', 'RoleController::update/$1');
    $routes->get('delete/(:num)', 'RoleController::delete/$1');
    $routes->get('permissions/(:num)', 'RoleController::getPermissions/$1');
    $routes->get('audit-log', 'UserPermissionController::auditLog');
});
