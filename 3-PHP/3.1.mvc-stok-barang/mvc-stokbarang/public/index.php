<?php
declare(strict_types=1);

session_start();

define('ROOT_PATH', dirname(__DIR__));
define('BASE_URL', '');  // e.g. '' for localhost/mvc-stokbarang via .htaccess, or set subfolder if needed

require_once ROOT_PATH . '/core/Router.php';
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Model.php';

$router = new Router();

// Auth routes
$router->get('/login',  'AuthController', 'login');
$router->post('/login', 'AuthController', 'authenticate');
$router->get('/logout', 'AuthController', 'logout');

// Stock routes
$router->get('/stock',         'StockController', 'index');
$router->get('/stock/create',  'StockController', 'create');
$router->post('/stock/store',  'StockController', 'store');
$router->get('/stock/edit',    'StockController', 'edit');
$router->post('/stock/update', 'StockController', 'update');
$router->post('/stock/delete', 'StockController', 'delete');

// Root → redirect to stock (Controller handles auth check)
$router->get('/', 'StockController', 'index');

$uri        = $_SERVER['REQUEST_URI'] ?? '/';
$httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Strip subfolder prefix if running in subdirectory
// e.g. if hosted at /mvc-stokbarang/, strip that prefix
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptDir !== '/' && str_starts_with($uri, $scriptDir)) {
    $uri = substr($uri, strlen($scriptDir)) ?: '/';
}

$router->dispatch($uri, $httpMethod);
