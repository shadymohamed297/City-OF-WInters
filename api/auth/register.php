<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$email = Validator::email($input['email'] ?? '');
$password = Validator::string($input['password'] ?? '', 72, 'Password');
$fullName = Validator::string($input['fullName'] ?? '', 80, 'Full name');
$phone = Validator::stringOrNull($input['phone'] ?? '', 30, 'Phone');

$auth = new Auth(Database::connection());
$user = $auth->register($email, $password, [
    'full_name' => $fullName,
    'phone' => $phone,
]);

$token = Csrf::token();
$secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
setcookie('csrf_token', $token, [
    'expires' => time() + 86400 * 30,
    'path' => '/',
    'secure' => $secure,
    'httponly' => false,
    'samesite' => 'Lax',
]);

Response::ok($user);
