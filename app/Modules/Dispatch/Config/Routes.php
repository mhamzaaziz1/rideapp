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
    $routes->get('trips/print/(:num)', 'TripController::printTrip/$1'); // Print Receipt
    $routes->get('trips/edit/(:num)', 'TripController::edit/$1');
    $routes->post('trips/update/(:num)', 'TripController::update/$1');
    $routes->get('trips/delete/(:num)', 'TripController::delete/$1');
    $routes->post('trips/update_status', 'TripController::updateStatus');
// Rating Routes
    $routes->post('ratings/submit', 'RatingController::submit');
    $routes->get('ratings/list', 'RatingController::list');
});

$routes->group('admin/disputes', ['namespace' => 'Modules\Dispatch\Controllers'], function ($routes) {
    $routes->get('/', 'DisputeController::index');
    $routes->get('view/(:num)', 'DisputeController::view/$1');
    $routes->post('update/(:num)', 'DisputeController::updateStatus/$1');
    $routes->post('settle/(:num)', 'DisputeController::settleFare/$1');
    $routes->post('comment/(:num)', 'DisputeController::addComment/$1');
    $routes->get('edit/(:num)', 'DisputeController::edit/$1');
    $routes->post('update_details/(:num)', 'DisputeController::updateDetails/$1');
    $routes->get('delete/(:num)', 'DisputeController::delete/$1');
    $routes->post('arrange_return_trip/(:num)', 'DisputeController::arrangeReturnTrip/$1');
});

$routes->group('api/disputes', ['namespace' => 'Modules\Dispatch\Controllers'], function ($routes) {
    $routes->post('create', 'DisputeController::apiCreate');
});

// Expose /trips directly as requested
$routes->get('trips', 'TripController::index', ['namespace' => 'Modules\Dispatch\Controllers']);

$routes->group('api/webhooks', ['namespace' => 'Modules\Dispatch\Controllers'], function ($routes) {
    // SMS endpoints
    $routes->post('twilio/receive', 'TwilioWebhookController::receive');
    
    // Voice endpoints
    $routes->post('twilio/voice', 'TwilioVoiceController::inbound');
    $routes->post('twilio/voice/gather-driver', 'TwilioVoiceController::gatherDriver');
    $routes->post('twilio/voice/gather-customer', 'TwilioVoiceController::gatherCustomer');
});

