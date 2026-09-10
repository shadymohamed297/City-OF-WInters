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

$stmt = $pdo->prepare('SELECT u.id, u.email, u.created_at, u.last_sign_in_at, p.full_name, p.phone, COUNT(o.id) as order_count, COALESCE(SUM(o.total), 0) as total_spent FROM users u LEFT JOIN profiles p ON u.id = p.id LEFT JOIN orders o ON u.id = o.user_id GROUP BY u.id ORDER BY u.created_at DESC');
$stmt->execute();
$customers = $stmt->fetchAll();

foreach ($customers as &$c) {
    $stmt = $pdo->prepare('SELECT role FROM user_roles WHERE user_id = :uid');
    $stmt->execute(['uid' => $c['id']]);
    $roles = $stmt->fetchAll();
    $c['roles'] = array_column($roles, 'role');
}

Response::ok(['customers' => $customers]);
