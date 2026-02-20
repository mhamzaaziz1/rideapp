<?php

namespace Modules\CallCenter\Config;

use Config\Services;

$routes = Services::routes();

$routes->group('call-logs', ['namespace' => 'Modules\CallCenter\Controllers'], function ($routes) {
    $routes->get('/', 'CallLogsController::index');
    $routes->get('new', 'CallLogsController::create');
    $routes->post('create', 'CallLogsController::store');
    $routes->get('edit/(:num)', 'CallLogsController::edit/$1');
    $routes->post('update/(:num)', 'CallLogsController::update/$1');
    $routes->get('delete/(:num)', 'CallLogsController::delete/$1');
});

// Global route exposure
$routes->get('call-logs', 'CallLogsController::index', ['namespace' => 'Modules\CallCenter\Controllers']);
