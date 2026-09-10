<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $pdo = Database::connection();

    $rawQ = trim($_GET['q'] ?? '');
    $categorySlug = trim($_GET['category_slug'] ?? '');
    $minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float) $_GET['min_price'] : null;
    $maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float) $_GET['max_price'] : null;
    $minRating = isset($_GET['min_rating']) && $_GET['min_rating'] !== '' ? (float) $_GET['min_rating'] : null;
    $inStock = isset($_GET['in_stock']) && $_GET['in_stock'] !== '0' && $_GET['in_stock'] !== '';
    $sort = $_GET['sort'] ?? 'relevance';
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 60;
    $limit = max(1, min(100, $limit));

    $sql = 'SELECT p.id, p.slug, p.title_ar, p.title_en, p.author_ar, p.author_en, p.publisher_ar, p.publisher_en, p.description_ar, p.description_en, p.price, p.compare_at_price, p.cover_url, p.category_id, p.pages, p.isbn, p.rating, p.reviews_count, p.stock, p.unlimited_stock, p.is_active, p.is_bestseller, p.is_new_arrival, p.is_featured, p.display_order, p.created_at FROM products p WHERE p.is_active = 1';
    $params = [];

    // Helper: Normalize Arabic string in PHP
    $normalizeArabic = function (string $str): string {
        // Strip diacritics / tashkeel
        $str = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $str);
        // Strip tatweel / kashida (ـ)
        $str = preg_replace('/\x{0640}/u', '', $str);
        // Normalize Alef variants
        $str = preg_replace('/[إأآٱ]/u', 'ا', $str);
        // Normalize Taa Marbouta to Haa
        $str = preg_replace('/ة/u', 'ه', $str);
        // Normalize Alef Maqsoura to Yaa
        $str = preg_replace('/ى/u', 'ي', $str);
        return $str;
    };

    if ($rawQ !== '') {
        $cleanQ = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $rawQ);
        $cleanQ = trim(preg_replace('/\s+/u', ' ', $cleanQ));

        $normQ = $normalizeArabic($cleanQ);

        // SQL function to normalize Arabic in columns
        $sqlNorm = function (string $col): string {
            return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE({$col}, 'إ', 'ا'), 'أ', 'ا'), 'آ', 'ا'), 'ة', 'ه'), 'ى', 'ي'), 'ـ', '')";
        };

        // SQL function to normalize slugs (replaces hyphens with space)
        $sqlNormSlug = function (string $col) use ($sqlNorm): string {
            $n = $sqlNorm($col);
            return "REPLACE({$n}, '-', ' ')";
        };

        // Columns to search
        $searchFields = [
            $sqlNorm('p.title_ar'),
            $sqlNorm('p.title_en'),
            $sqlNormSlug('p.slug'),
            $sqlNorm('p.author_ar'),
            $sqlNorm('p.author_en'),
            $sqlNorm('p.publisher_ar'),
            $sqlNorm('p.publisher_en'),
            $sqlNorm("COALESCE(p.description_ar, '')"),
            $sqlNorm("COALESCE(p.description_en, '')"),
        ];

        $rawFields = [
            'p.title_ar', 'p.title_en', 'p.slug', 'p.author_ar', 'p.author_en',
            'p.publisher_ar', 'p.publisher_en', "COALESCE(p.description_ar, '')", "COALESCE(p.description_en, '')"
        ];

        // Tokens
        $tokens = array_values(array_filter(
            explode(' ', $normQ),
            fn($w) => mb_strlen(trim($w)) >= 2
        ));

        // Strip common prefix words if there are other tokens
        $stopWords = ['كتاب', 'روايه', 'رواية', 'قصه', 'قصة', 'سلسله', 'سلسلة', 'دار', 'مكتبه', 'مكتبة', 'مجلد'];
        $filteredTokens = array_values(array_filter($tokens, fn($w) => !in_array($w, $stopWords, true)));
        $effectiveTokens = !empty($filteredTokens) ? $filteredTokens : $tokens;

        $pIndex = 0;
        $matchOrs = [];

        // 1. Full phrase match (normalized)
        $phraseGroup = [];
        foreach ($searchFields as $sf) {
            $pName = 'fp_n_' . ($pIndex++);
            $phraseGroup[] = "{$sf} LIKE :{$pName}";
            $params[$pName] = '%' . $normQ . '%';
        }
        $matchOrs[] = '(' . implode(' OR ', $phraseGroup) . ')';

        // Full phrase raw match
        if ($rawQ !== $normQ) {
            $rawGroup = [];
            foreach ($rawFields as $rf) {
                $pName = 'fp_r_' . ($pIndex++);
                $rawGroup[] = "{$rf} LIKE :{$pName}";
                $params[$pName] = '%' . $rawQ . '%';
            }
            $matchOrs[] = '(' . implode(' OR ', $rawGroup) . ')';
        }

        // 2. All tokens match
        if (count($effectiveTokens) > 1) {
            $allTokenAnds = [];
            foreach ($effectiveTokens as $tok) {
                $tokGroup = [];
                foreach ($searchFields as $sf) {
                    $pName = 'tok_a_' . ($pIndex++);
                    $tokGroup[] = "{$sf} LIKE :{$pName}";
                    $params[$pName] = '%' . $tok . '%';
                }
                $allTokenAnds[] = '(' . implode(' OR ', $tokGroup) . ')';
            }
            $matchOrs[] = '(' . implode(' AND ', $allTokenAnds) . ')';
        }

        // 3. Significant token match on title / slug / author
        if (!empty($effectiveTokens)) {
            $keyFields = [
                $sqlNorm('p.title_ar'),
                $sqlNormSlug('p.slug'),
                $sqlNorm('p.author_ar'),
            ];
            $anyTokenOrs = [];
            foreach ($effectiveTokens as $tok) {
                foreach ($keyFields as $kf) {
                    $pName = 'tok_any_' . ($pIndex++);
                    $anyTokenOrs[] = "{$kf} LIKE :{$pName}";
                    $params[$pName] = '%' . $tok . '%';
                }
            }
            if (!empty($anyTokenOrs)) {
                $matchOrs[] = '(' . implode(' OR ', $anyTokenOrs) . ')';
            }
        }

        if (!empty($matchOrs)) {
            $sql .= ' AND (' . implode(' OR ', $matchOrs) . ')';
        }
    }

    if ($categorySlug !== '') {
        $stmt = $pdo->prepare('SELECT id FROM categories WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $categorySlug]);
        $cat = $stmt->fetch();
        if ($cat) {
            $sql .= ' AND p.category_id = :category_id';
            $params['category_id'] = $cat['id'];
        }
    }

    if ($minPrice !== null && $minPrice > 0) {
        $sql .= ' AND p.price >= :min_price';
        $params['min_price'] = $minPrice;
    }
    if ($maxPrice !== null && $maxPrice > 0) {
        $sql .= ' AND p.price <= :max_price';
        $params['max_price'] = $maxPrice;
    }
    if ($minRating !== null && $minRating > 0) {
        $sql .= ' AND p.rating >= :min_rating';
        $params['min_rating'] = $minRating;
    }
    if ($inStock) {
        $sql .= ' AND (p.unlimited_stock = 1 OR p.stock > 0)';
    }

    switch ($sort) {
        case 'price-asc':  $sql .= ' ORDER BY p.price ASC'; break;
        case 'price-desc': $sql .= ' ORDER BY p.price DESC'; break;
        case 'rating':     $sql .= ' ORDER BY p.rating DESC'; break;
        case 'new':        $sql .= ' ORDER BY p.created_at DESC'; break;
        default:           $sql .= ' ORDER BY p.display_order ASC, p.created_at DESC';
    }

    $sql .= ' LIMIT ' . $limit;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    // In-memory relevance ranking when searching
    if ($rawQ !== '' && $sort === 'relevance' && !empty($products)) {
        $targetNorm = $normQ;
        $targetWords = $effectiveTokens;

        usort($products, function ($a, $b) use ($normalizeArabic, $targetNorm, $targetWords) {
            $calcScore = function ($p) use ($normalizeArabic, $targetNorm, $targetWords) {
                $score = 0;
                $tAr = $normalizeArabic($p['title_ar'] ?? '');
                $sSlug = $normalizeArabic(str_replace('-', ' ', $p['slug'] ?? ''));
                $authAr = $normalizeArabic($p['author_ar'] ?? '');

                // Exact title match
                if ($tAr === $targetNorm) {
                    $score += 200;
                } elseif (str_starts_with($tAr, $targetNorm)) {
                    $score += 150;
                } elseif (str_contains($tAr, $targetNorm)) {
                    $score += 100;
                }

                // Slug match
                if (str_contains($sSlug, $targetNorm)) {
                    $score += 80;
                }

                // Author match
                if ($authAr !== '—' && $authAr !== '' && str_contains($authAr, $targetNorm)) {
                    $score += 120;
                }

                // Token coverage
                if (!empty($targetWords)) {
                    $matchedWords = 0;
                    foreach ($targetWords as $w) {
                        if (str_contains($tAr, $w) || str_contains($sSlug, $w)) {
                            $matchedWords++;
                            $score += 30;
                        }
                    }
                    if ($matchedWords === count($targetWords)) {
                        $score += 50;
                    }
                }

                return $score;
            };

            $scoreA = $calcScore($a);
            $scoreB = $calcScore($b);

            if ($scoreA !== $scoreB) {
                return $scoreB <=> $scoreA; // Highest score first
            }

            return ($a['display_order'] ?? 0) <=> ($b['display_order'] ?? 0);
        });
    }

    Response::ok(['products' => $products, 'total' => count($products)]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
