<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $pdo = Database::connection();

    $rawSlug = $_GET['slug'] ?? $_GET['id'] ?? '';
    $slug = trim($rawSlug);

    if ($slug === '' || $slug === '[object Object]') {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (preg_match('~/author/([^/?#]+)~', $referer, $m)) {
            $slug = urldecode($m[1]);
        }
    }

    if ($slug === '' || $slug === '[object Object]') {
        Response::ok(['author' => null, 'products' => []]);
        exit;
    }

    // 1. Direct match by slug OR id
    $stmt = $pdo->prepare('SELECT * FROM authors WHERE (slug = :slug OR id = :id) AND is_active = 1 LIMIT 1');
    $stmt->execute(['slug' => $slug, 'id' => $slug]);
    $author = $stmt->fetch();

    // 2. Try urldecode if slug was URL-encoded
    if (!$author && str_contains($slug, '%')) {
        $decoded = urldecode($slug);
        $stmt = $pdo->prepare('SELECT * FROM authors WHERE (slug = :slug OR id = :id) AND is_active = 1 LIMIT 1');
        $stmt->execute(['slug' => $decoded, 'id' => $decoded]);
        $author = $stmt->fetch();
        if ($author) {
            $slug = $decoded;
        }
    }

    // 3. Clean directional formatting
    $cleanSlug = preg_replace('/[\x{2000}-\x{206F}\x{FEFF}\x{FFFD}]/u', '', $slug);
    $cleanSlug = trim($cleanSlug);

    if (!$author && $cleanSlug !== '' && $cleanSlug !== $slug) {
        $stmt = $pdo->prepare('SELECT * FROM authors WHERE (slug = :slug OR id = :id) AND is_active = 1 LIMIT 1');
        $stmt->execute(['slug' => $cleanSlug, 'id' => $cleanSlug]);
        $author = $stmt->fetch();
    }

    // 4. Match by name
    if (!$author) {
        $stmt = $pdo->prepare('SELECT * FROM authors WHERE is_active = 1 AND (name_ar = :name OR name_en = :name OR slug LIKE :like_slug) LIMIT 1');
        $stmt->execute(['name' => $slug, 'like_slug' => '%' . $slug . '%']);
        $author = $stmt->fetch();
    }

    if (!$author) {
        Response::ok(['author' => null, 'products' => []]);
        exit;
    }

    // Fetch author's active products
    $pStmt = $pdo->prepare('
        SELECT id, slug, title_ar, title_en, author_ar, author_en, publisher_ar, publisher_en,
               description_ar, description_en, price, compare_at_price, cover_url,
               pages, isbn, rating, reviews_count, stock, unlimited_stock, is_bestseller, is_new_arrival, is_featured
        FROM products
        WHERE (author_id = :aid OR (author_ar = :aname AND (author_id IS NULL OR author_id = "")))
          AND is_active = 1
        ORDER BY display_order ASC, created_at DESC
    ');
    $pStmt->execute(['aid' => $author['id'], 'aname' => $author['name_ar']]);
    $products = $pStmt->fetchAll();

    Response::ok([
        'author' => $author,
        'products' => $products,
        'total' => count($products),
    ]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
