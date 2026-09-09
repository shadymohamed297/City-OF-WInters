<?php

use App\Auth;
use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

$auth = new Auth(Database::connection());
$user = $auth->requireAuth();

$pdo = Database::connection();
$stmt = $pdo->prepare('SELECT id, order_number, status, payment_method, payment_status, total, created_at FROM orders WHERE user_id = :uid ORDER BY created_at DESC');
$stmt->execute(['uid' => $user['id']]);
$orders = $stmt->fetchAll();

Response::ok(['orders' => $orders]);
