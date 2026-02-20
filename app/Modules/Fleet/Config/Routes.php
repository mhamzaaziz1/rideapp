<?php

$routes->group('drivers', ['namespace' => 'Modules\Fleet\Controllers'], function($routes) {
    $routes->get('/', 'DriversController::index');
    $routes->get('new', 'DriversController::new');
    $routes->post('create', 'DriversController::create');
    $routes->get('edit/(:num)', 'DriversController::edit/$1');
    $routes->post('update/(:num)', 'DriversController::update/$1');
    $routes->post('update_doc_status', 'DriversController::updateDocStatus');
    $routes->get('delete/(:num)', 'DriversController::delete/$1');
    $routes->get('profile/(:num)', 'DriversController::profile/$1');
    $routes->post('add_fund', 'DriversController::addFund');
    $routes->post('update_rate', 'DriversController::updateRate');
});
