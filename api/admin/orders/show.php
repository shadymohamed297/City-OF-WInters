<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;
use App\Validator;

require_once __DIR__ . '/../../../vendor/autoload.php';

Csrf::middleware();
$auth = new App\Auth(Database::connection());
$auth->requireAdmin();

$id = Validator::uuid($_GET['id'] ?? '');

$order = Database::table('orders')->find('id', $id);
if (!$order) {
    Response::notFound('Order not found');
}

$stmt = Database::connection()->prepare('SELECT * FROM order_items WHERE order_id = :id');
$stmt->execute(['id' => $id]);
$items = $stmt->fetchAll();

Response::ok(['order' => $order, 'items' => $items]);
