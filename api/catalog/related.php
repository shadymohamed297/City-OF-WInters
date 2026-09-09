<?php

use App\Database;
use App\Response;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';

$pdo = Database::connection();

$productId = Validator::uuid($_GET['product_id'] ?? '');
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 8;
$limit = max(1, min(20, $limit));

$stmt = $pdo->prepare('SELECT category_id FROM products WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $productId]);
$product = $stmt->fetch();

$sql = 'SELECT id, slug, title_ar, title_en, author_ar, author_en, publisher_ar, publisher_en, description_ar, description_en, price, compare_at_price, cover_url, category_id, pages, isbn, rating, reviews_count, stock, unlimited_stock, is_active, is_bestseller, is_new_arrival, is_featured, display_order, created_at FROM products WHERE is_active = 1 AND id != :id';
$params = ['id' => $productId];

if ($product && $product['category_id']) {
    $sql .= ' AND category_id = :category_id';
    $params['category_id'] = $product['category_id'];
}

$sql .= ' ORDER BY rating DESC LIMIT ' . $limit;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

Response::ok(['products' => $products]);
