<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// ENV
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require __DIR__ . '/../config/database.php';


// ERROR MODE (solo dev)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ROUTES
$routes = require __DIR__ . '/../routes/web.php';

$basePath = '/php-starter/public';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace($basePath, '', $uri);

foreach ($routes as $route) {
    if ($route['path'] === $uri) {

        [$controller, $method] = explode('@', $route['action']);
        $class = "App\\Controllers\\$controller";

        (new $class)->$method();
        exit;
    }
}

var_dump($uri);
exit;
http_response_code(404);
echo "404 Not Found";