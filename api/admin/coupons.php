<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;

require_once __DIR__ . '/../../../vendor/autoload.php';

Csrf::middleware();
$auth = new App\Auth(Database::connection());
$auth->requireAdmin();

$pdo = Database::connection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT id, code, type, value, description, min_subtotal, max_discount, usage_limit, used_count, starts_at, expires_at, is_active, created_at FROM coupons ORDER BY created_at DESC');
    $stmt->execute();
    $coupons = $stmt->fetchAll();
    Response::ok(['coupons' => $coupons]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'code' => strtoupper(trim($input['code'] ?? '')),
        'type' => $input['type'] ?? 'percent',
        'value' => (float)($input['value'] ?? 0),
        'min_subtotal' => (float)($input['min_subtotal'] ?? 0),
        'max_discount' => isset($input['max_discount']) ? (float)$input['max_discount'] : null,
        'usage_limit' => isset($input['usage_limit']) ? (int)$input['usage_limit'] : null,
        'starts_at' => $input['starts_at'] ?? null,
        'expires_at' => $input['expires_at'] ?? null,
        'is_active' => (bool)($input['is_active'] ?? true),
        'description' => $input['description'] ?? null,
    ];

    if (isset($input['id'])) {
        Database::table('coupons')->update('id', $input['id'], $data);
        Response::ok(['message' => 'Updated']);
    } else {
        $id = Database::table('coupons')->insert($data);
        Response::ok(['id' => $id]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'] ?? '';
    Database::table('coupons')->delete('id', $id);
    Response::ok(['message' => 'Deleted']);
    exit;
}

Response::error('METHOD_NOT_ALLOWED', 'Method not allowed', 405);
