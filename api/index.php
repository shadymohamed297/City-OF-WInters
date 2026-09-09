<?php

namespace App;

use App\Middleware\CorsMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

// CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    CorsMiddleware::handle();
    exit;
}

// Global CORS headers
CorsMiddleware::applyHeaders();

// Route request
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Remove query string and base path if any
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptDir && $scriptDir !== '/' && str_starts_with($path, $scriptDir)) {
    $path = substr($path, strlen($scriptDir));
}
$path = rtrim($path, '/') ?: '/';

// Simple router
$routes = require __DIR__ . '/../routes.php';
$handler = $routes[$method][$path] ?? null;

if (!$handler) {
    Response::notFound();
}

require_once $handler;
