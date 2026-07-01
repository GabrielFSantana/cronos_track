<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'DashboardController::index');

$routes->get('lancamento', 'ManualEntryController::create', ['filter' => 'csrf']);
$routes->post('lancamento', 'ManualEntryController::store', ['filter' => 'csrf']);

$routes->group('api', ['filter' => 'apikey'], static function (RouteCollection $routes) {
    $routes->post('activities', 'Api\ActivitiesController::create');
    $routes->post('manual', 'Api\ManualController::create');
    $routes->get('report', 'Api\ReportController::index');
    $routes->get('projetos', 'Api\ProjetosController::index');
    $routes->post('projetos', 'Api\ProjetosController::create');
});
