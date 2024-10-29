<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->post('/lead/(:any)/update-payments', 'DashboardController::updatePayments/$1');
$routes->get('/lead/(:any)/fetch-payments', 'DashboardController::fetchPayments/$1');

$routes->post('/lead/(:any)/create-account', 'DashboardController::createAccount/$1');
$routes->get('/lead/(:any)/fetch-accounts', 'DashboardController::fetchAccounts/$1');

$routes->post('/lead/(:any)/update-details', 'DashboardController::updateDetails/$1');
$routes->get('/lead/(:any)/fetch-details', 'DashboardController::fetchDetails/$1');

$routes->get('/lead/(:any)', 'DashboardController::leadSingle/$1');

$routes->post('/change-branch', 'DashboardController::changeBranch');

$routes->get('/clients', 'DashboardController::clients');
$routes->get('/dashboard', 'DashboardController::index');
$routes->get('/', 'DashboardController::index');

$routes->get('/logout', 'AuthController::logout');
$routes->post('/attempt-login', 'AuthController::attemptLogin');
$routes->get('/login', 'AuthController::login');

$routes->get('/ghl-api/fetch-pipelines', 'GHLAPIController::fetchPipelines');

// $routes->post('/upload-certificate', 'Web::uploadCertificate');
// $routes->get('/sustainability-star-student', 'Web::userCertificateSustainabilityStarStudent');
// $routes->get('/eco-pioneer', 'Web::userCertificateEcoPioneer');
// $routes->get('/globe-mechanic', 'Web::userCertificateGlobeMechanic');
// $routes->get('/planet-healer', 'Web::userCertificatePlanetHealer');
// $routes->get('/certificate', 'Web::certificate');
// $routes->post('/store-user-record', 'Web::storeUserRecord');
// $routes->get('/form', 'Web::form');
// $routes->get('/questions', 'Web::questions');
// $routes->get('/', 'Web::index');
// $routes->get('migrate', 'MigrationController::migrate');
// $routes->get('seed/(:any)', 'MigrationController::seed/$1');

