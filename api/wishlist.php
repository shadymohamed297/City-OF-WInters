<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = Database::connection();

if ($method === 'GET') {
    $auth = new Auth($pdo);
    $user = $auth->requireAuth();

    $stmt = $pdo->prepare('SELECT product_id FROM wishlist WHERE user_id = :user_id');
    $stmt->execute(['user_id' => $user['id']]);
    $rows = $stmt->fetchAll();

    $productIds = array_column($rows, 'product_id');
    if (empty($productIds)) {
        Response::ok(['products' => []]);
    }

    $in = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("SELECT id, slug, title_ar, title_en, author_ar, author_en, price, compare_at_price, cover_url, stock, rating, reviews_count, is_active FROM products WHERE id IN ({$in}) AND is_active = 1");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll();

    Response::ok(['products' => $products]);
    exit;
}

Csrf::middleware();
$auth = new Auth($pdo);
$user = $auth->requireAuth();

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if ($method === 'POST') {
    $productId = filter_var($input['product_id'] ?? '', FILTER_VALIDATE_UUID);
    if (!$productId) {
        Response::validationError('Invalid product_id');
    }

    $stmt = $pdo->prepare('SELECT id FROM wishlist WHERE user_id = :user_id AND product_id = :product_id LIMIT 1');
    $stmt->execute(['user_id' => $user['id'], 'product_id' => $productId]);
    $existing = $stmt->fetch();

    if ($existing) {
        $stmt = $pdo->prepare('DELETE FROM wishlist WHERE id = :id');
        $stmt->execute(['id' => $existing['id']]);
        Response::ok(['in' => false]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO wishlist (user_id, product_id) VALUES (:user_id, :product_id)');
        $stmt->execute(['user_id' => $user['id'], 'product_id' => $productId]);
        Response::ok(['in' => true]);
    }
}

if ($method === 'DELETE') {
    $productId = filter_var($input['product_id'] ?? '', FILTER_VALIDATE_UUID);
    if (!$productId) {
        Response::validationError('Invalid product_id');
    }

    $stmt = $pdo->prepare('DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id');
    $stmt->execute(['user_id' => $user['id'], 'product_id' => $productId]);
    Response::ok(['message' => 'Removed']);
}

Response::error('METHOD_NOT_ALLOWED', 'Method not allowed', 405);
