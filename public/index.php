<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// ENV
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require __DIR__ . '/../config/database.php';
require __DIR__ . '/../config/config.php';


// ERROR MODE (solo dev)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ROUTES
$routes = require __DIR__ . '/../routes/web.php';

$basePath = rtrim($_ENV['APP_BASE_PATH'] ?? '', '/');

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = $basePath !== '' ? str_replace($basePath, '', $uri) : $uri;
$uri    = '/' . trim($uri, '/');   // normaliza: "" → "/", "/users/" → "/users"
$method = $_SERVER['REQUEST_METHOD'];

foreach ($routes as $route) {
    if ($route['path'] === $uri && $route['method'] === $method) {

        [$controller, $action] = explode('@', $route['action']);
        $class = "App\\Controllers\\$controller";

        (new $class)->$action();
        exit;
    }
}


http_response_code(404);
echo "404 Not Found";