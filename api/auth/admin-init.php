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
$password = Validator::string($input['password'] ?? '', 72, 'Password');
$fullName = Validator::string($input['fullName'] ?? '', 80, 'Full name');

$auth = new Auth(Database::connection());
$user = $auth->register($email, $password, ['full_name' => $fullName]);

// Add admin role
$pdo = Database::connection();
$stmt = $pdo->prepare('INSERT IGNORE INTO user_roles (user_id, role) VALUES (:uid, :role)');
$stmt->execute(['uid' => $user['id'], 'role' => 'admin']);

Response::ok(['ok' => true, 'id' => $user['id']]);
