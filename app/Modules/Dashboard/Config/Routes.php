<?php

namespace Modules\Dashboard\Config;

use Config\Services;

$routes = Services::routes();

$routes->group('dashboard', ['namespace' => 'Modules\Dashboard\Controllers'], function ($routes) {
    $routes->get('/', 'DashboardController::index');
});

// Also make it the default landing page for logged-in users? 
// For now, let's just keep it at /dashboard
