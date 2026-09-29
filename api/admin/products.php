<?php

use App\Csrf;
use App\Database;
use App\Response;
use App\Slugify;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = Database::connection();

// Admin auth on mutations
if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
    Csrf::middleware();
    $auth = new App\Auth($pdo);
    $user = $auth->requireAdmin();
}

if ($method === 'GET') {
    $stmt = $pdo->prepare('SELECT * FROM products ORDER BY display_order ASC, created_at DESC');
    $stmt->execute();
    $products = $stmt->fetchAll();
    Response::ok(['products' => $products]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if ($method === 'POST') {
    try {
        $titleAr = Validator::string($input['title_ar'] ?? '', 200, 'Title AR');
        $titleEn = Validator::string($input['title_en'] ?? '', 200, 'Title EN');
        $authorAr = Validator::string($input['author_ar'] ?? '', 120, 'Author AR');
        $authorEn = Validator::string($input['author_en'] ?? '', 120, 'Author EN');
        $price = Validator::number($input['price'] ?? 0, 0, 1000000, 'Price');
        $stock = Validator::int($input['stock'] ?? 0, 0, 100000, 'Stock');

        $slug = Validator::stringOrNull($input['slug'] ?? '', 120, 'Slug');
        if (!$slug || mb_strlen($slug) < 2) {
            $slug = Slugify::ensureUnique('products', $titleEn, $input['id'] ?? null);
        }

        $costPrice = isset($input['cost_price']) && is_numeric($input['cost_price']) ? (float)$input['cost_price'] : 0.0;
        $marketingCost = isset($input['marketing_cost']) && is_numeric($input['marketing_cost']) ? (float)$input['marketing_cost'] : 0.0;
        $miscExpenses = isset($input['misc_expenses']) && is_numeric($input['misc_expenses']) ? (float)$input['misc_expenses'] : 0.0;

        $data = [
            'slug' => $slug,
            'title_ar' => $titleAr,
            'title_en' => $titleEn,
            'author_ar' => $authorAr,
            'author_en' => $authorEn,
            'publisher_ar' => Validator::stringOrNull($input['publisher_ar'] ?? '', 120),
            'publisher_en' => Validator::stringOrNull($input['publisher_en'] ?? '', 120),
            'description_ar' => Validator::stringOrNull($input['description_ar'] ?? '', 2000),
            'description_en' => Validator::stringOrNull($input['description_en'] ?? '', 2000),
            'price' => $price,
            'compare_at_price' => Validator::numberOrNull($input['compare_at_price'] ?? null, 0, 1000000),
            'price_usd' => Validator::numberOrNull($input['price_usd'] ?? null, 0, 100000),
            'compare_at_price_usd' => Validator::numberOrNull($input['compare_at_price_usd'] ?? null, 0, 100000),
            'cost_price' => $costPrice,
            'marketing_cost' => $marketingCost,
            'misc_expenses' => $miscExpenses,
            'cover_url' => Validator::stringOrNull($input['cover_url'] ?? '', 5000),
            'abjjad_url' => Validator::stringOrNull($input['abjjad_url'] ?? '', 500),
            'category_id' => Validator::uuidOrNull($input['category_id'] ?? null),
            'author_id' => Validator::uuidOrNull($input['author_id'] ?? null),
            'pages' => Validator::intOrNull($input['pages'] ?? null, 0, 20000),
            'isbn' => Validator::stringOrNull($input['isbn'] ?? '', 40),
            'stock' => $stock,
            'unlimited_stock' => Validator::bool($input['unlimited_stock'] ?? false) ? 1 : 0,
            'is_active' => Validator::bool($input['is_active'] ?? true) ? 1 : 0,
            'is_bestseller' => Validator::bool($input['is_bestseller'] ?? false) ? 1 : 0,
            'is_new_arrival' => Validator::bool($input['is_new_arrival'] ?? false) ? 1 : 0,
            'is_featured' => Validator::bool($input['is_featured'] ?? false) ? 1 : 0,
            'display_order' => Validator::int($input['display_order'] ?? 0, -99999, 99999),
        ];

        // If author_id not set but author_ar is known, auto-match author
        if (empty($data['author_id']) && $authorAr !== '' && $authorAr !== '—') {
            $aLookup = $pdo->prepare('SELECT id, name_ar, name_en FROM authors');
            $aLookup->execute();
            $allAuthors = $aLookup->fetchAll();
            $cleanInput = preg_replace('/[أإآا]/u', 'ا', trim($authorAr));
            foreach ($allAuthors as $aRow) {
                if ($aRow['name_ar'] === $authorAr || $aRow['name_en'] === $authorAr) {
                    $data['author_id'] = $aRow['id'];
                    break;
                }
                $cleanAuthor = preg_replace('/[أإآا]/u', 'ا', trim($aRow['name_ar'] ?? ''));
                if ($cleanInput !== '' && $cleanInput === $cleanAuthor) {
                    $data['author_id'] = $aRow['id'];
                    break;
                }
            }
        }

        if (isset($input['id']) && !empty($input['id'])) {
            $id = Validator::uuid($input['id']);
            Database::table('products')->update('id', $id, $data);
            $productId = $id;
        } else {
            $newId = Database::uuid();
            $data['id'] = $newId;
            Database::table('products')->insert($data);
            $productId = $newId;
        }

        // Sync categories if provided
        if (isset($input['category_ids']) && is_array($input['category_ids'])) {
            $catIds = array_values(array_filter(array_unique($input['category_ids']), fn($v) => preg_match('/^[0-9a-f-]{36}$/i', $v)));
            // Set primary category_id
            $primary = $catIds[0] ?? $data['category_id'];
            Database::table('products')->update('id', $productId, ['category_id' => $primary]);

            // Clear and re-insert junction
            $stmt = $pdo->prepare('DELETE FROM product_categories WHERE product_id = :pid');
            $stmt->execute(['pid' => $productId]);
            if (!empty($catIds)) {
                $stmt = $pdo->prepare('INSERT INTO product_categories (product_id, category_id) VALUES (:pid, :cid)');
                foreach ($catIds as $cid) {
                    $stmt->execute(['pid' => $productId, 'cid' => $cid]);
                }
            }
        }

        // Sync author's other works if provided
        if (isset($input['author_work_ids']) && is_array($input['author_work_ids']) && $authorAr !== '' && $authorAr !== '—') {
            $workIds = array_values(array_filter(array_unique($input['author_work_ids']), fn($id) => preg_match('/^[0-9a-f-]{36}$/i', $id) && $id !== $productId));
            if (!empty($workIds)) {
                $inPlaceholders = implode(',', array_fill(0, count($workIds), '?'));
                $authorIdVal = $data['author_id'] ?? null;
                $updStmt = $pdo->prepare("UPDATE products SET author_ar = ?, author_en = ?, author_id = ? WHERE id IN ($inPlaceholders)");
                $updParams = array_merge([$authorAr, $authorEn ?: $authorAr, $authorIdVal], $workIds);
                $updStmt->execute($updParams);
            }
        }

        // Handle explicitly unlinked works
        if (isset($input['unlinked_work_ids']) && is_array($input['unlinked_work_ids']) && $authorAr !== '' && $authorAr !== '—') {
            $unlinkIds = array_values(array_filter(array_unique($input['unlinked_work_ids']), fn($id) => preg_match('/^[0-9a-f-]{36}$/i', $id) && $id !== $productId));
            if (!empty($unlinkIds)) {
                $inPlaceholders = implode(',', array_fill(0, count($unlinkIds), '?'));
                $unlinkStmt = $pdo->prepare("UPDATE products SET author_ar = '—', author_en = '—', author_id = NULL WHERE id IN ($inPlaceholders) AND author_ar = ?");
                $unlinkParams = array_merge($unlinkIds, [$authorAr]);
                $unlinkStmt->execute($unlinkParams);
            }
        }

        Response::ok(['product_id' => $productId]);
        exit;
    } catch (\Throwable $e) {
        Response::error('PRODUCT_SAVE_FAILED', $e->getMessage(), 400);
        exit;
    }
}

if ($method === 'DELETE') {
    try {
        $id = Validator::uuid($_GET['id'] ?? '');
        Database::table('products')->delete('id', $id);
        Response::ok(['message' => 'Deleted']);
        exit;
    } catch (\Throwable $e) {
        Response::error('PRODUCT_DELETE_FAILED', $e->getMessage(), 400);
        exit;
    }
}

Response::error('METHOD_NOT_ALLOWED', 'Method not allowed', 405);
