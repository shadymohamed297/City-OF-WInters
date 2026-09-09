<?php

use App\Database;
use App\Response;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';

$pdo = Database::connection();

$slug = Validator::string($_GET['slug'] ?? '', 120, 'slug');

$stmt = $pdo->prepare('SELECT id, slug, title_ar, title_en, author_ar, author_en, publisher_ar, publisher_en, description_ar, description_en, price, compare_at_price, cover_url, category_id, pages, isbn, rating, reviews_count, stock, unlimited_stock, is_active, is_bestseller, is_new_arrival, is_featured, display_order, created_at FROM products WHERE slug = :slug AND is_active = 1 LIMIT 1');
$stmt->execute(['slug' => $slug]);
$product = $stmt->fetch();

if (!$product) {
    Response::ok(['product' => null]);
}

Response::ok(['product' => $product]);
