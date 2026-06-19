<?php

namespace App\Modules\Billing\Config;

use Config\Services;

$routes = Services::routes();

$routes->group('finance', ['namespace' => 'Modules\Billing\Controllers'], function ($routes) {
    $routes->get('/', 'FinanceController::index');
    $routes->get('print-trip/(:num)', 'FinanceController::printTrip/$1');
    $routes->post('bulk-print', 'FinanceController::bulkPrint');
    $routes->get('export-csv', 'FinanceController::exportCsv');

    // Driver Payouts
    $routes->get('payouts', 'PayoutController::index');
    $routes->post('payouts/request', 'PayoutController::request');
    $routes->post('payouts/complete/(:num)', 'PayoutController::complete/$1');
    $routes->post('payouts/cancel/(:num)', 'PayoutController::cancel/$1');
});

// Global route exposure
$routes->get('finance', 'FinanceController::index', ['namespace' => 'Modules\Billing\Controllers']);
