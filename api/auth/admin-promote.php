<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$secret = $input['secret'] ?? '';
$expected = getenv('ADMIN_INIT_SECRET') ?: '';

if (!$expected || $secret !== $expected) {
    Response::error('INVALID_SECRET', 'بيانات غير صحيحة', 403);
}

$email = Validator::email($input['email'] ?? '');

$pdo = Database::connection();
$user = Database::table('users')->find('email', $email);
if (!$user) {
    Response::error('USER_NOT_FOUND', 'هذا البريد غير مسجل في النظام', 404);
}

$stmt = $pdo->prepare('INSERT IGNORE INTO user_roles (user_id, role) VALUES (:uid, :role)');
$stmt->execute(['uid' => $user['id'], 'role' => 'admin']);

Response::ok(['ok' => true, 'id' => $user['id']]);
