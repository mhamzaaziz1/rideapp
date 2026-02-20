<?php

namespace Modules\Setting\Config;

use Config\Services;

$routes = Services::routes();

$routes->group('settings', ['namespace' => 'Modules\Setting\Controllers'], function ($routes) {
    $routes->get('/', 'SettingController::index');
    $routes->post('update', 'SettingController::update');
});

// Global route for convenience if needed, though group covers it well.
$routes->get('settings', 'SettingController::index', ['namespace' => 'Modules\Setting\Controllers']);
