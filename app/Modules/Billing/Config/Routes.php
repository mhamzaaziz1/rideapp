<?php

namespace Modules\Billing\Config;

use Config\Services;

$routes = Services::routes();

$routes->group('finance', ['namespace' => 'Modules\Billing\Controllers'], function ($routes) {
    $routes->get('/', 'FinanceController::index');
    $routes->get('print-trip/(:num)', 'FinanceController::printTrip/$1');
    $routes->post('bulk-print', 'FinanceController::bulkPrint');
    $routes->get('export-csv', 'FinanceController::exportCsv');
});

// Global route exposure
$routes->get('finance', 'FinanceController::index', ['namespace' => 'Modules\Billing\Controllers']);
