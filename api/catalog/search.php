<?php

use App\Database;
use App\Response;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $pdo = Database::connection();

    $q = trim($_GET['q'] ?? '');
    $categorySlug = trim($_GET['category_slug'] ?? '');
    $minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float) $_GET['min_price'] : null;
    $maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float) $_GET['max_price'] : null;
    $minRating = isset($_GET['min_rating']) && $_GET['min_rating'] !== '' ? (float) $_GET['min_rating'] : null;
    $inStock = isset($_GET['in_stock']) && $_GET['in_stock'] !== '0' && $_GET['in_stock'] !== '';
    $sort = $_GET['sort'] ?? 'relevance';
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 60;
    $limit = max(1, min(100, $limit));

    $sql = 'SELECT id, slug, title_ar, title_en, author_ar, author_en, publisher_ar, publisher_en, description_ar, description_en, price, compare_at_price, cover_url, category_id, pages, isbn, rating, reviews_count, stock, unlimited_stock, is_active, is_bestseller, is_new_arrival, is_featured, display_order, created_at FROM products WHERE is_active = 1';
    $params = [];

    if ($q !== '') {
        // Strip common punctuation and normalize spaces
        $cleanQ = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $q);
        $cleanQ = trim(preg_replace('/\s+/u', ' ', $cleanQ));

        $searchTerms = array_filter([$q, $cleanQ]);
        $searchTerms = array_values(array_unique($searchTerms));

        // Generate Arabic normalization variations
        $variations = [];
        foreach ($searchTerms as $term) {
            $normA = preg_replace('/[إأآ]/u', 'ا', $term);
            if ($normA && $normA !== $term) $variations[] = $normA;

            $normT = preg_replace('/ة/u', 'ه', $term);
            if ($normT && $normT !== $term) $variations[] = $normT;

            $normY = preg_replace('/ى/u', 'ي', $term);
            if ($normY && $normY !== $term) $variations[] = $normY;
        }

        $allTerms = array_values(array_unique(array_merge($searchTerms, $variations)));

        $qConditions = [];
        $searchFields = ['title_ar', 'title_en', 'author_ar', 'author_en', 'publisher_ar', 'publisher_en'];
        $idx = 0;

        foreach ($allTerms as $term) {
            foreach ($searchFields as $field) {
                $pName = 'sq_' . $idx++;
                $qConditions[] = "{$field} LIKE :{$pName}";
                $params[$pName] = '%' . $term . '%';
            }
        }

        if (!empty($qConditions)) {
            $sql .= ' AND (' . implode(' OR ', $qConditions) . ')';
        }
    }

    if ($categorySlug !== '') {
        $stmt = $pdo->prepare('SELECT id FROM categories WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $categorySlug]);
        $cat = $stmt->fetch();
        if ($cat) {
            $sql .= ' AND category_id = :category_id';
            $params['category_id'] = $cat['id'];
        }
    }

    if ($minPrice !== null) {
        $sql .= ' AND price >= :min_price';
        $params['min_price'] = $minPrice;
    }
    if ($maxPrice !== null) {
        $sql .= ' AND price <= :max_price';
        $params['max_price'] = $maxPrice;
    }
    if ($minRating !== null) {
        $sql .= ' AND rating >= :min_rating';
        $params['min_rating'] = $minRating;
    }
    if ($inStock) {
        $sql .= ' AND (unlimited_stock = 1 OR stock > 0)';
    }

    switch ($sort) {
        case 'price-asc':  $sql .= ' ORDER BY price ASC'; break;
        case 'price-desc': $sql .= ' ORDER BY price DESC'; break;
        case 'rating':     $sql .= ' ORDER BY rating DESC'; break;
        case 'new':        $sql .= ' ORDER BY created_at DESC'; break;
        default:           $sql .= ' ORDER BY display_order ASC, created_at DESC';
    }

    $sql .= ' LIMIT ' . $limit;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    Response::ok(['products' => $products, 'total' => count($products)]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
