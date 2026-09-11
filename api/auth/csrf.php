<?php

use App\Csrf;

require_once __DIR__ . '/../../vendor/autoload.php';
$token = Csrf::token();
setcookie('csrf_token', $token, [
    'expires' => time() + 86400 * 30,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => false,
    'samesite' => 'Lax'
]);
header('Content-Type: application/json');
echo json_encode(['csrf_token' => $token], JSON_UNESCAPED_UNICODE);
