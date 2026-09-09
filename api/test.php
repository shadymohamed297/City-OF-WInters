<?php
require_once __DIR__ . '/../vendor/autoload.php';
header('Content-Type: application/json');
echo json_encode([
    'ok' => true,
    'db_host_loaded' => getenv('DB_HOST') ?: '(empty - .env not loading)',
    'db_name_loaded' => getenv('DB_NAME') ?: '(empty - .env not loading)',
]);
