<?php

use App\Csrf;

require_once __DIR__ . '/../../vendor/autoload.php';
Csrf::token();
header('Content-Type: application/json');
echo json_encode(['csrf_token' => Csrf::token()], JSON_UNESCAPED_UNICODE);
