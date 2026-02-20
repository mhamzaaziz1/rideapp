<?php

namespace Modules\IAM\Config;

use Config\Services;

$routes = Services::routes();

$routes->group('api/auth', ['namespace' => 'Modules\IAM\Controllers'], function ($routes) {
    $routes->post('register', 'AuthController::register');
    $routes->post('login', 'AuthController::login');
});

$routes->get('login', 'AuthController::index', ['namespace' => 'Modules\IAM\Controllers']);
$routes->get('logout', 'AuthController::logout', ['namespace' => 'Modules\IAM\Controllers']);
$routes->post('login', 'AuthController::attemptLogin', ['namespace' => 'Modules\IAM\Controllers']);

// Legacy API Routes
$routes->get('api_login.php', 'AuthController::index', ['namespace' => 'Modules\IAM\Controllers']);
$routes->post('api_login.php', 'AuthController::login', ['namespace' => 'Modules\IAM\Controllers']);

// Staff Management Routes
$routes->group('staff', ['namespace' => 'Modules\IAM\Controllers'], function ($routes) {
    $routes->get('/', 'StaffController::index');
    $routes->get('new', 'StaffController::new');
    $routes->post('create', 'StaffController::create');
    $routes->get('edit/(:num)', 'StaffController::edit/$1');
    $routes->post('update/(:num)', 'StaffController::update/$1');
    $routes->get('delete/(:num)', 'StaffController::delete/$1');
});

