<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/upload', 'Home::upload');
$routes->post('/action', 'Home::action');
$routes->get('/infos', 'Home::infos');
