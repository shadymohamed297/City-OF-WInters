<?php
require_once __DIR__ . '/../vendor/autoload.php';
header('Content-Type: application/json');

try {
    $pdo = App\Database::connection();

    $sql = 'SELECT id, slug, title_ar, title_en, author_ar, author_en, publisher_ar, publisher_en, description_ar, description_en, price, compare_at_price, cover_url, category_id, pages, isbn, rating, reviews_count, stock, unlimited_stock, is_active, is_bestseller, is_new_arrival, is_featured, display_order, created_at FROM products WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC LIMIT 5';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll();

    echo json_encode(['ok' => true, 'count' => count($products), 'sample' => $products]);
} catch (\Throwable $e) {
    http_response_code(200); // force 200 so the browser actually shows us the message
    echo json_encode([
        'ok' => false,
        'exception_class' => get_class($e),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);
}
