<?php

namespace Modules\Pricing\Config;

use Config\Services;

$routes = Services::routes();

$routes->group('pricing', ['namespace' => 'Modules\Pricing\Controllers'], function ($routes) {
    $routes->get('/', 'PricingController::index');
    $routes->post('update/(:num)', 'PricingController::update/$1');
    $routes->post('addPeakHour', 'PricingController::addPeakHour');
    $routes->get('deletePeakHour/(:num)', 'PricingController::deletePeakHour/$1');
    $routes->post('addZone', 'PricingController::addZone');
    $routes->get('deleteZone/(:num)', 'PricingController::deleteZone/$1');
});
