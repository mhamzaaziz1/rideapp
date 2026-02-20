<?php

namespace Modules\Customer\Config;

use Config\Services;

$routes = Services::routes();

$routes->group('customers', ['namespace' => 'Modules\Customer\Controllers'], function ($routes) {
    $routes->get('/', 'CustomerController::index');
    $routes->get('create', 'CustomerController::new');
    $routes->get('new', 'CustomerController::new');
    $routes->post('create', 'CustomerController::create');
    $routes->get('edit/(:num)', 'CustomerController::edit/$1');
    $routes->post('update/(:num)', 'CustomerController::update/$1');
    $routes->get('delete/(:num)', 'CustomerController::delete/$1');
    $routes->post('update_status', 'CustomerController::updateStatus');
    $routes->get('profile/(:num)', 'CustomerController::profile/$1');
    $routes->post('add_fund', 'CustomerController::addFund');
    $routes->get('print_statement/(:num)', 'CustomerController::printStatement/$1');
    $routes->get('export_statement/(:num)', 'CustomerController::exportStatement/$1');
    $routes->get('addresses/(:num)', 'CustomerController::getAddresses/$1'); // JSON API for dispatch modal
    
    // Address Management
    $routes->post('address/create', 'CustomerAddressController::create');
    $routes->post('address/update/(:num)', 'CustomerAddressController::update/$1');
    $routes->get('address/delete/(:num)', 'CustomerAddressController::delete/$1');
    $routes->get('address/set_default/(:num)', 'CustomerAddressController::setDefault/$1');

    // Card Management
    $routes->post('card/create', 'CustomerCardController::create');
    $routes->get('card/delete/(:num)', 'CustomerCardController::delete/$1');
    $routes->get('card/set_default/(:num)', 'CustomerCardController::setDefault/$1');
});

// Customer Portal (Self-Service)
$routes->group('customer', ['namespace' => 'Modules\Customer\Controllers'], function ($routes) {
    $routes->get('book', 'BookingController::new');
    $routes->post('book', 'BookingController::create');
    $routes->post('estimate', 'BookingController::estimate');
    $routes->get('trips', 'BookingController::history');
});

// Explicit global get for robustness
$routes->get('customers', 'CustomerController::index', ['namespace' => 'Modules\Customer\Controllers']);
