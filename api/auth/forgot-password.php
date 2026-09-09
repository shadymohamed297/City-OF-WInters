<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';
Csrf::middleware();

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$email = Validator::email($input['email'] ?? '');

$pdo = Database::connection();
$user = Database::table('users')->find('email', $email);
if (!$user) {
    Response::ok(['message' => 'If account exists, reset email sent']);
}

// Generate token
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', time() + 3600);

$stmt = $pdo->prepare('INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires)');
$stmt->execute(['email' => $email, 'token' => $token, 'expires' => $expires]);

// TODO: Send email with reset link
Response::ok(['message' => 'If account exists, reset email sent']);
