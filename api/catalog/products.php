<?php

use App\Database;
use App\Response;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';

$pdo = Database::connection();

$categorySlug = $_GET['category_slug'] ?? null;
$featured = isset($_GET['featured']);
$bestseller = isset($_GET['bestseller']);
$newArrival = isset($_GET['new_arrival']);
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : ($categorySlug ? 16 : 200);
$limit = max(1, min(200, $limit));

$sql = 'SELECT id, slug, title_ar, title_en, author_ar, author_en, publisher_ar, publisher_en, description_ar, description_en, price, compare_at_price, cover_url, category_id, pages, isbn, rating, reviews_count, stock, unlimited_stock, is_active, is_bestseller, is_new_arrival, is_featured, display_order, created_at FROM products WHERE is_active = 1';
$params = [];

if ($featured) $sql .= ' AND is_featured = 1';
if ($bestseller) $sql .= ' AND is_bestseller = 1';
if ($newArrival) $sql .= ' AND is_new_arrival = 1';

if ($categorySlug) {
    $stmt = $pdo->prepare('SELECT id FROM categories WHERE slug = :slug LIMIT 1');
    $stmt->execute(['slug' => $categorySlug]);
    $cat = $stmt->fetch();
    if (!$cat) {
        Response::ok(['products' => []]);
    }
    $categoryId = $cat['id'];

    $sql .= ' AND (category_id = :category_id OR id IN (SELECT product_id FROM product_categories WHERE category_id = :category_id_jc))';
    $params['category_id'] = $categoryId;
    $params['category_id_jc'] = $categoryId;
}

$sql .= ' ORDER BY display_order ASC, created_at DESC LIMIT ' . $limit;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

Response::ok(['products' => $products]);
