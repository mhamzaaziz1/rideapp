<?php

namespace app\App\Modules\Support\Config;

$routes->group('api/support', ['namespace' => 'App\Modules\Support\Controllers'], function ($routes) {
    $routes->match(['get', 'post'], 'conversation', 'ChatController::getConversation');
    $routes->post('message', 'ChatController::sendMessage');
    $routes->get('messages/(:num)', 'ChatController::getMessages/$1');
    $routes->get('embed', 'ChatController::embed');
});

$routes->group('admin/support', ['namespace' => 'App\Modules\Support\Controllers\Admin', 'filter' => ['auth', 'permission:support.chat.view']], function ($routes) {
    $routes->get('/', 'SupportController::index');
    $routes->get('conversation/(:num)', 'SupportController::viewConversation/$1');
    $routes->post('reply', 'SupportController::sendReply');
    $routes->post('close/(:num)', 'SupportController::closeConversation/$1');

    // FAQ Management
    $routes->get('faq', 'FaqController::index');
    $routes->post('faq/store', 'FaqController::store');
    $routes->post('faq/delete/(:num)', 'FaqController::delete/$1');

    // AI Settings
    $routes->get('settings', 'SupportSettingsController::index');
    $routes->post('settings/save', 'SupportSettingsController::save');
});
