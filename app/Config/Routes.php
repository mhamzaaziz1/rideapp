<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Load Module Routes
$modules = ['IAM', 'Dispatch', 'Fleet', 'Customer', 'Billing', 'Setting', 'CallCenter'];
foreach ($modules as $module) {
    if (file_exists(APPPATH . 'Modules/' . $module . '/Config/Routes.php')) {
        require APPPATH . 'Modules/' . $module . '/Config/Routes.php';
    }
}

