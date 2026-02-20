<?php

namespace Modules\Dispatch\Config;

use Config\Services;

$routes = Services::routes();

$routes->group('dispatch', ['namespace' => 'Modules\Dispatch\Controllers'], function ($routes) {
    $routes->get('/', 'DispatchController::index');
    $routes->post('trips/create', 'TripController::create'); // Keep existing API-like route
    
// Map Data Endpoint
    $routes->get('map-data', 'DispatchController::getMapData');
    
    // CRUD Routes
    $routes->get('trips', 'TripController::index');
    $routes->get('trips/new', 'TripController::new');
    $routes->get('trips/view/(:num)', 'TripController::view/$1'); // Details View
    $routes->get('trips/edit/(:num)', 'TripController::edit/$1');
    $routes->post('trips/update/(:num)', 'TripController::update/$1');
    $routes->get('trips/delete/(:num)', 'TripController::delete/$1');
    $routes->post('trips/update_status', 'TripController::updateStatus');
// Rating Routes
    $routes->post('ratings/submit', 'RatingController::submit');
    $routes->get('ratings/list', 'RatingController::list');
});

// Expose /trips directly as requested
$routes->get('trips', 'TripController::index', ['namespace' => 'Modules\Dispatch\Controllers']);
