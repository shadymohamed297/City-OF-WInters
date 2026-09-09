<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $pdo = Database::connection();

    $rawSlug = $_GET['slug'] ?? '';
    $slug = trim($rawSlug);

    if ($slug === '') {
        Response::ok(['product' => null]);
    }

    $selectCols = 'id, slug, title_ar, title_en, author_ar, author_en, publisher_ar, publisher_en, description_ar, description_en, price, compare_at_price, cover_url, category_id, pages, isbn, rating, reviews_count, stock, unlimited_stock, is_active, is_bestseller, is_new_arrival, is_featured, display_order, created_at';

    // 1. Direct match by slug OR id
    $stmt = $pdo->prepare("SELECT {$selectCols} FROM products WHERE (slug = :slug OR id = :id) AND is_active = 1 LIMIT 1");
    $stmt->execute(['slug' => $slug, 'id' => $slug]);
    $product = $stmt->fetch();

    // 2. Try urldecode if slug was URL-encoded
    if (!$product && str_contains($slug, '%')) {
        $decoded = urldecode($slug);
        $stmt = $pdo->prepare("SELECT {$selectCols} FROM products WHERE (slug = :slug OR id = :id) AND is_active = 1 LIMIT 1");
        $stmt->execute(['slug' => $decoded, 'id' => $decoded]);
        $product = $stmt->fetch();
        if ($product) {
            $slug = $decoded;
        }
    }

    // 3. Strip invisible directional formatting marks (\u202a-\u202e, \u2066-\u2069, \u200e, \u200f)
    if (!$product) {
        $cleanSlug = preg_replace('/[\x{202A}-\x{202E}\x{2066}-\x{2069}\x{200E}\x{200F}]/u', '', $slug);
        $cleanSlug = trim($cleanSlug);

        // Try exact match on cleanSlug
        if ($cleanSlug !== '' && $cleanSlug !== $slug) {
            $stmt = $pdo->prepare("SELECT {$selectCols} FROM products WHERE (slug = :slug OR id = :id) AND is_active = 1 LIMIT 1");
            $stmt->execute(['slug' => $cleanSlug, 'id' => $cleanSlug]);
            $product = $stmt->fetch();
        }

        // Try LIKE match (database slug contains invisible marks, query is clean)
        if (!$product && $cleanSlug !== '') {
            $stmt = $pdo->prepare("SELECT {$selectCols} FROM products WHERE is_active = 1 AND (slug LIKE :like_slug OR slug LIKE :like_clean) LIMIT 1");
            $stmt->execute([
                'like_slug'  => '%' . $slug . '%',
                'like_clean' => '%' . $cleanSlug . '%',
            ]);
            $product = $stmt->fetch();
        }
    }

    // 4. Fallback: match by title_ar or title_en (replacing dashes with spaces)
    if (!$product) {
        $titleGuess = trim(str_replace('-', ' ', $slug));
        if ($titleGuess !== '') {
            $stmt = $pdo->prepare("SELECT {$selectCols} FROM products WHERE is_active = 1 AND (title_ar = :title OR title_en = :title_en OR title_ar LIKE :like_title) LIMIT 1");
            $stmt->execute([
                'title'      => $titleGuess,
                'title_en'   => $titleGuess,
                'like_title' => '%' . $titleGuess . '%',
            ]);
            $product = $stmt->fetch();
        }
    }

    Response::ok(['product' => $product ?: null]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
