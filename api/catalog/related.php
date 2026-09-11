<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $pdo = Database::connection();

    $productId = trim($_GET['product_id'] ?? '');
    $slug = trim($_GET['slug'] ?? '');
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 8;
    $limit = max(1, min(20, $limit));

    $current = null;
    if (preg_match('/^[0-9a-f-]{36}$/i', $productId)) {
        $stmt = $pdo->prepare('SELECT id, slug, title_ar, title_en, author_ar, author_en, category_id FROM products WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $productId]);
        $current = $stmt->fetch();
    } elseif ($slug !== '') {
        $stmt = $pdo->prepare('SELECT id, slug, title_ar, title_en, author_ar, author_en, category_id FROM products WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $current = $stmt->fetch();
    }

    if (!$current) {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (preg_match('~/product/([^/?#]+)~', $referer, $m)) {
            $refSlug = urldecode($m[1]);
            $stmt = $pdo->prepare('SELECT id, slug, title_ar, title_en, author_ar, author_en, category_id FROM products WHERE (slug = :slug OR id = :id) LIMIT 1');
            $stmt->execute(['slug' => $refSlug, 'id' => $refSlug]);
            $current = $stmt->fetch();
        }
    }

    $currId = $current['id'] ?? ($productId ?: '00000000-0000-0000-0000-000000000000');
    $authorAr = trim($current['author_ar'] ?? '');
    $authorEn = trim($current['author_en'] ?? '');
    $catId = $current['category_id'] ?? null;

    $authorProducts = [];
    $seenIds = [];
    if ($currId) {
        $seenIds[$currId] = true;
    }

    // 1. Author match if author is set and valid
    if (($authorAr !== '' && $authorAr !== '—') || ($authorEn !== '' && $authorEn !== '—')) {
        $authSql = 'SELECT id, slug, title_ar, title_en, author_ar, author_en, publisher_ar, publisher_en, price, compare_at_price, price_usd, compare_at_price_usd, cover_url, category_id, pages, isbn, rating, reviews_count, stock, unlimited_stock, is_active, is_bestseller, is_new_arrival, is_featured, display_order, created_at FROM products WHERE is_active = 1 AND id != :curr_id AND (';
        $authParams = ['curr_id' => $currId];
        $authOrs = [];

        if ($authorAr !== '' && $authorAr !== '—') {
            $authOrs[] = '(author_ar = :auth_ar AND author_ar != "—")';
            $authParams['auth_ar'] = $authorAr;
        }
        if ($authorEn !== '' && $authorEn !== '—') {
            $authOrs[] = '(author_en = :auth_en AND author_en != "—")';
            $authParams['auth_en'] = $authorEn;
        }

        $authSql .= implode(' OR ', $authOrs) . ') ORDER BY rating DESC, created_at DESC LIMIT 12';
        $stmt = $pdo->prepare($authSql);
        $stmt->execute($authParams);
        foreach ($stmt->fetchAll() as $row) {
            $authorProducts[] = $row;
            $seenIds[$row['id']] = true;
        }
    }

    // 2. Series match (e.g. books sharing prefix like "عزيزي ثيو", "مهمشون", "الروم", "صرخات أنثى", "براءة ظلمه")
    $seriesRoot = null;
    if ($current && !empty($current['title_ar'])) {
        $cleaned = trim(preg_replace('/(\s*["\(«].*|\s*الجزء.*|\s*الأجزاء.*|\s*كتابين.*|\s*ثلاث كتب.*|\s*أربع كتب.*|\s*خمس كتب.*)/u', '', $current['title_ar']));
        if (mb_strlen($cleaned) >= 4 && !in_array($cleaned, ['كتاب', 'رواية', 'مجموعة'], true)) {
            $seriesRoot = $cleaned;
            $sSql = 'SELECT id, slug, title_ar, title_en, author_ar, author_en, publisher_ar, publisher_en, price, compare_at_price, price_usd, compare_at_price_usd, cover_url, category_id, pages, isbn, rating, reviews_count, stock, unlimited_stock, is_active, is_bestseller, is_new_arrival, is_featured, display_order, created_at FROM products WHERE is_active = 1 AND id != :curr_id AND (title_ar LIKE :s_pref OR slug LIKE :s_slug) LIMIT 12';
            $sStmt = $pdo->prepare($sSql);
            $sStmt->execute([
                'curr_id' => $currId,
                's_pref' => $seriesRoot . '%',
                's_slug' => str_replace(' ', '-', $seriesRoot) . '%',
            ]);
            foreach ($sStmt->fetchAll() as $row) {
                if (!isset($seenIds[$row['id']])) {
                    $authorProducts[] = $row;
                    $seenIds[$row['id']] = true;
                }
            }
        }
    }

    // 3. Related products (same category)
    $relatedProducts = [];
    if ($catId) {
        $relSql = 'SELECT id, slug, title_ar, title_en, author_ar, author_en, publisher_ar, publisher_en, price, compare_at_price, price_usd, compare_at_price_usd, cover_url, category_id, pages, isbn, rating, reviews_count, stock, unlimited_stock, is_active, is_bestseller, is_new_arrival, is_featured, display_order, created_at FROM products WHERE is_active = 1 AND id != :curr_id AND category_id = :cat_id';
        $relParams = ['curr_id' => $currId, 'cat_id' => $catId];

        if (!empty($seenIds)) {
            $excludeIdx = 0;
            $excludePlaceholders = [];
            foreach (array_keys($seenIds) as $sid) {
                $pName = 'ex_' . ($excludeIdx++);
                $excludePlaceholders[] = ':' . $pName;
                $relParams[$pName] = $sid;
            }
            $relSql .= ' AND id NOT IN (' . implode(', ', $excludePlaceholders) . ')';
        }

        $relSql .= ' ORDER BY rating DESC, created_at DESC LIMIT ' . $limit;
        $relStmt = $pdo->prepare($relSql);
        $relStmt->execute($relParams);
        $relatedProducts = $relStmt->fetchAll();
    }

    // Fallback if not enough products
    if (count($relatedProducts) + count($authorProducts) < 4) {
        $fallSql = 'SELECT id, slug, title_ar, title_en, author_ar, author_en, publisher_ar, publisher_en, price, compare_at_price, price_usd, compare_at_price_usd, cover_url, category_id, pages, isbn, rating, reviews_count, stock, unlimited_stock, is_active, is_bestseller, is_new_arrival, is_featured, display_order, created_at FROM products WHERE is_active = 1 AND id != :curr_id';
        $fallParams = ['curr_id' => $currId];
        if (!empty($seenIds)) {
            $excludeIdx = 0;
            $excludePlaceholders = [];
            foreach (array_keys($seenIds) as $sid) {
                $pName = 'fex_' . ($excludeIdx++);
                $excludePlaceholders[] = ':' . $pName;
                $fallParams[$pName] = $sid;
            }
            $fallSql .= ' AND id NOT IN (' . implode(', ', $excludePlaceholders) . ')';
        }
        $fallSql .= ' ORDER BY display_order ASC, created_at DESC LIMIT ' . $limit;
        $fallStmt = $pdo->prepare($fallSql);
        $fallStmt->execute($fallParams);
        foreach ($fallStmt->fetchAll() as $row) {
            $relatedProducts[] = $row;
        }
    }

    $cleanSlugFn = function (&$list) {
        foreach ($list as &$p) {
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
        }
        unset($p);
    };

    $cleanSlugFn($authorProducts);
    $cleanSlugFn($relatedProducts);

    $allCombined = array_merge($authorProducts, $relatedProducts);

    Response::ok([
        'author_name' => ($authorAr !== '' && $authorAr !== '—') ? $authorAr : ($authorEn !== '' && $authorEn !== '—' ? $authorEn : null),
        'series_name' => $seriesRoot,
        'author_products' => $authorProducts,
        'related_products' => $relatedProducts,
        'products' => $allCombined,
    ]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
