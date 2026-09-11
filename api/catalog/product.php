<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $pdo = Database::connection();

    $rawSlug = $_GET['slug'] ?? '';
    $slug = trim($rawSlug);

    if ($slug === '' || $slug === '[object Object]') {
        Response::ok(['product' => null]);
    }

    $selectCols = 'id, slug, title_ar, title_en, author_ar, author_en, publisher_ar, publisher_en, description_ar, description_en, price, compare_at_price, cover_url, category_id, pages, isbn, rating, reviews_count, stock, unlimited_stock, is_active, is_bestseller, is_new_arrival, is_featured, display_order, created_at';

    // Normalizer helper
    $normalize = function (string $text): string {
        $clean = preg_replace('/[\x{2000}-\x{206F}\x{FEFF}\x{FFFD}]/u', '', $text);
        $clean = preg_replace('/[_\-"\'«»()\[\]]/u', ' ', $clean);
        $clean = preg_replace('/\s+/u', ' ', $clean);
        return trim($clean);
    };

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

    // 3. Strip invisible directional formatting marks and replacement characters
    $cleanSlug = preg_replace('/[\x{2000}-\x{206F}\x{FEFF}\x{FFFD}]/u', '', $slug);
    $cleanSlug = trim($cleanSlug);

    if (!$product && $cleanSlug !== '' && $cleanSlug !== $slug) {
        $stmt = $pdo->prepare("SELECT {$selectCols} FROM products WHERE (slug = :slug OR id = :id) AND is_active = 1 LIMIT 1");
        $stmt->execute(['slug' => $cleanSlug, 'id' => $cleanSlug]);
        $product = $stmt->fetch();
    }

    // 4. Try LIKE search matching clean slug
    if (!$product && $cleanSlug !== '') {
        $stmt = $pdo->prepare("SELECT {$selectCols} FROM products WHERE is_active = 1 AND (slug LIKE :like_slug OR slug LIKE :like_clean) LIMIT 1");
        $stmt->execute([
            'like_slug'  => '%'. $slug . '%',
            'like_clean' => '%'. $cleanSlug . '%',
        ]);
        $product = $stmt->fetch();
    }

    // 5. Match by normalized title or title guess
    if (!$product) {
        $normQuery = $normalize($slug);
        if ($normQuery !== '') {
            $stmt = $pdo->prepare("SELECT {$selectCols} FROM products WHERE is_active = 1 AND (title_ar LIKE :like_title OR title_en LIKE :like_title) LIMIT 10");
            $stmt->execute(['like_title' => '%'. $normQuery . '%']);
            $candidates = $stmt->fetchAll();

            foreach ($candidates as $candidate) {
                if ($normalize($candidate['title_ar']) === $normQuery || $normalize($candidate['title_en']) === $normQuery) {
                    $product = $candidate;
                    break;
                }
            }

            if (!$product && !empty($candidates)) {
                $product = $candidates[0];
            }
        }
    }

    // 6. Auto-heal: If product slug in database contains bidi or corrupted chars, update it to clean slug
    if ($product && preg_match('/[\x{2000}-\x{206F}\x{FEFF}\x{FFFD}]/u', $product['slug'])) {
        $canonical = preg_replace('/[\x{2000}-\x{206F}\x{FEFF}\x{FFFD}]/u', '', $product['slug']);
        if (str_contains($canonical, "\ufffd") || trim($canonical) === '') {
            $canonical = preg_replace('/\s+/u', '-', $normalize($product['title_ar']));
        }
        $canonical = trim($canonical, '-');
        if ($canonical !== '' && $canonical !== $product['slug']) {
            try {
                $upd = $pdo->prepare("UPDATE products SET slug = :slug WHERE id = :id");
                $upd->execute(['slug' => $canonical, 'id' => $product['id']]);
                $product['slug'] = $canonical;
            } catch (\Throwable $e) {
                // Ignore uniqueness collision
            }
        }
    }

    Response::ok(['product' => $product ?: null]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
