<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

Csrf::middleware();
$auth = new App\Auth(Database::connection());
$auth->requireAdmin();

$pdo = Database::connection();

// Get all users with roles and profiles
$stmt = $pdo->prepare('SELECT u.id, u.email, u.email_verified, u.is_active, u.created_at, u.last_sign_in_at, p.full_name, p.phone FROM users u LEFT JOIN profiles p ON u.id = p.id ORDER BY u.created_at DESC');
$stmt->execute();
$users = $stmt->fetchAll();

// Get all roles
$stmt = $pdo->prepare('SELECT user_id, role FROM user_roles');
$stmt->execute();
$roles = $stmt->fetchAll();
$roleMap = [];
foreach ($roles as $r) {
    $roleMap[$r['user_id']][] = $r['role'];
}

foreach ($users as &$u) {
    $u['roles'] = $roleMap[$u['id']] ?? [];
}

Response::ok(['users' => $users]);
