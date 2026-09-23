<?php

use App\Database;
use App\Response;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $pdo = Database::connection();

    $categorySlug = $_GET['category_slug'] ?? null;
    $featured = isset($_GET['featured']);
    $bestseller = isset($_GET['bestseller']);
    $newArrival = isset($_GET['new_arrival']);

    // If limit is specified (e.g. homepage sections asking for limit=16), respect it.
    // Otherwise, return all active products (e.g. for /shop catalog).
    $limit = isset($_GET['limit']) ? max(1, min(1000, (int) $_GET['limit'])) : null;

    $sql = 'SELECT id, slug, title_ar, title_en, author_ar, author_en, publisher_ar, publisher_en, description_ar, description_en, price, compare_at_price, price_usd, compare_at_price_usd, cover_url, category_id, pages, isbn, rating, reviews_count, stock, unlimited_stock, is_active, is_bestseller, is_new_arrival, is_featured, display_order, created_at FROM products WHERE is_active = 1';
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

        // Match by primary category_id or junction product_categories
        $sql .= ' AND (category_id = :category_id OR id IN (SELECT product_id FROM product_categories WHERE category_id = :cat_id_sub))';
        $params['category_id'] = $categoryId;
        $params['cat_id_sub'] = $categoryId;
    }

    $sql .= ' ORDER BY (CASE WHEN cover_url IS NOT NULL AND cover_url != "" THEN 0 ELSE 1 END) ASC, display_order ASC, created_at DESC';
    if ($limit !== null) {
        $sql .= ' LIMIT ' . $limit;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    foreach ($products as &$p) {
        if (preg_match('/[\x{2000}-\x{206F}\x{FEFF}\x{FFFD}]/u', $p['slug'])) {
            $c = preg_replace('/[\x{2000}-\x{206F}\x{FEFF}\x{FFFD}]/u', '', $p['slug']);
            if (str_contains($c, "\ufffd") || trim($c) === '') {
                $c = preg_replace('/[_\-"\'«»()\[\]]/u', ' ', $p['title_ar']);
                $c = preg_replace('/\s+/u', '-', trim($c));
            }
            $c = trim($c, '-');
            if ($c !== '') {
                $p['slug'] = $c;
            }
        }
        $p['is_active'] = (bool) $p['is_active'];
        $p['is_bestseller'] = (bool) $p['is_bestseller'];
        $p['is_new_arrival'] = (bool) $p['is_new_arrival'];
        $p['is_featured'] = (bool) $p['is_featured'];
        $p['unlimited_stock'] = (bool) $p['unlimited_stock'];
    }
    unset($p);

    Response::ok(['products' => $products]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
