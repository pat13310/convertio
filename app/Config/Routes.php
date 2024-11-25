<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Page d'accueil
$routes->get('/', 'Home::index');

// Routes de conversion
$routes->post('upload', 'Home::upload');
$routes->post('convert-action', 'Home::convertAction');
$routes->get('convert/progress/(:num)', 'Home::getFileProgress/$1');

// Routes de redimensionnement
$routes->get('scale', 'Home::scale');
$routes->post('scale-action', 'Home::scale_action');
$routes->get('scale/progress', 'Home::getScaleProgress');

// Autres routes
$routes->get('infos', 'Home::infos');
