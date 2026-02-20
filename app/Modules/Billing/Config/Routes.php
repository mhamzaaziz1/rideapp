<?php

namespace Modules\Billing\Config;

use Config\Services;

$routes = Services::routes();

$routes->group('finance', ['namespace' => 'Modules\Billing\Controllers'], function ($routes) {
    $routes->get('/', 'FinanceController::index');
});

// Global route exposure
$routes->get('finance', 'FinanceController::index', ['namespace' => 'Modules\Billing\Controllers']);
