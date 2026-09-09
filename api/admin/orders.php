<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;
use App\Validator;

require_once __DIR__ . '/../../../vendor/autoload.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = Database::connection();

if ($method === 'GET') {
    $status = $_GET['status'] ?? null;
    $from = $_GET['from'] ?? null;
    $to = $_GET['to'] ?? null;

    $sql = 'SELECT id, order_number, status, payment_method, payment_status, subtotal, shipping_cost, discount, total, shipping_address, guest_name, guest_phone, guest_email, notes, tracking_number, user_id, created_at, updated_at FROM orders WHERE 1=1';
    $params = [];

    if ($status) {
        $sql .= ' AND status = :status';
        $params['status'] = $status;
    }
    if ($from) {
        $sql .= ' AND created_at >= :from';
        $params['from'] = $from;
    }
    if ($to) {
        $sql .= ' AND created_at <= :to';
        $params['to'] = $to;
    }

    $sql .= ' ORDER BY created_at DESC LIMIT 500';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();

    Response::ok(['orders' => $orders]);
    exit;
}

Csrf::middleware();
$auth = new App\Auth($pdo);
$auth->requireAdmin();

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if ($method === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'status') {
    $id = Validator::uuid($input['id'] ?? '');
    $status = Validator::enum($input['status'] ?? '', ['pending','confirmed','processing','shipped','delivered','cancelled','refunded'], 'Status');
    $stmt = $pdo->prepare('UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :id');
    $stmt->execute(['status' => $status, 'id' => $id]);
    Response::ok(['message' => 'Status updated']);
    exit;
}

if ($method === 'DELETE') {
    $id = Validator::uuid($_GET['id'] ?? '');
    $stmt = $pdo->prepare('DELETE FROM order_items WHERE order_id = :id');
    $stmt->execute(['id' => $id]);
    $stmt = $pdo->prepare('DELETE FROM orders WHERE id = :id');
    $stmt->execute(['id' => $id]);
    Response::ok(['message' => 'Deleted']);
    exit;
}

Response::error('METHOD_NOT_ALLOWED', 'Method not allowed', 405);
