<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';
Csrf::middleware();

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$token = Validator::string($input['token'] ?? '', 100, 'Token');
$password = Validator::string($input['password'] ?? '', 72, 'Password');

$pdo = Database::connection();
$stmt = $pdo->prepare('SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW() LIMIT 1');
$stmt->execute(['token' => $token]);
$reset = $stmt->fetch();

if (!$reset) {
    Response::error('INVALID_TOKEN', 'Invalid or expired token', 400);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('UPDATE users SET password_hash = :hash WHERE email = :email');
$stmt->execute(['hash' => $hash, 'email' => $reset['email']]);

$stmt = $pdo->prepare('DELETE FROM password_resets WHERE token = :token');
$stmt->execute(['token' => $token]);

Response::ok(['message' => 'Password reset successful']);
