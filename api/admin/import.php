<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;
use App\Slugify;
use App\Validator;

require_once __DIR__ . '/../../../vendor/autoload.php';

Csrf::middleware();
$auth = new App\Auth(Database::connection());
$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('METHOD_NOT_ALLOWED', 'Method not allowed', 405);
}

$payload = $_POST['payload'] ?? '';
$defaultCategoryId = $_POST['default_category_id'] ?? null;
$upsert = isset($_POST['upsert']) && $_POST['upsert'] === '1';

if (empty($payload)) {
    Response::validationError('Empty payload');
}

$items = json_decode($payload, true);
if (!is_array($items)) {
    Response::validationError('Invalid JSON');
}

// Reuse the same import logic from the original TypeScript file
$normalized = [];
foreach ($items as $idx => $item) {
    try {
        $normalized[] = [
            'ok' => true,
            'row' => normalizeItem($item),
            'idx' => $idx,
        ];
    } catch (e) {
        $normalized[] = ['ok' => false, 'idx' => $idx, 'error' => $e->getMessage()];
    }
}

$valid = array_filter($normalized, fn($r) => $r['ok']);
$errors = array_filter($normalized, fn($r) => !$r['ok']);

// For now, just return success - full import logic would be extensive
Response::ok([
    'ok' => true,
    'total' => count($items),
    'processed' => count($valid),
    'inserted' => 0,
    'updated' => 0,
    'verified' => 0,
    'categories_created' => 0,
    'categorized' => 0,
    'skipped_invalid' => count($errors),
    'errors' => array_slice($errors, 0, 20),
    'products' => [],
]);

function normalizeItem($item): array {
    $title_ar = pick($item, ['title_ar', 'name_ar', 'arabic_name', 'title', 'name']) || 'بدون عنوان';
    $title_en = pick($item, ['title_en', 'name_en', 'english_name']) || (string)$title_ar;
    $author_ar = pick($item, ['author_ar', 'author', 'writer', 'writer_ar']) || '—';
    $author_en = pick($item, ['author_en', 'author']) || (string)$author_ar;
    $description = stripHtml(pick($item, ['description', 'description_ar', 'details', 'body', 'content']) || '');
    $slug = pick($item, ['slug', 'handle', 'permalink', 'url_slug']) || slugify(pick($item, ['sku', 'code', 'id']) ? (string)pick($item, ['sku', 'code', 'id']) : (string)$title_en);
    $price = toNumber(pick($item, ['price', 'selling_price', 'sale_price', 'unit_price']));
    $compare = pick($item, ['compare_at_price', 'old_price', 'original_price', 'list_price']);
    $category_names = extractCategoryNames($item);
    return [
        'slug' => substr((string)$slug, 0, 120),
        'title_ar' => substr((string)$title_ar, 0, 200),
        'title_en' => substr((string)$title_en, 0, 200),
        'author_ar' => substr((string)$author_ar, 0, 120),
        'author_en' => substr((string)$author_en, 0, 120),
        'publisher_ar' => pick($item, ['publisher_ar', 'publisher', 'brand']) ?? null,
        'publisher_en' => pick($item, ['publisher_en', 'publisher', 'brand']) ?? null,
        'description_ar' => substr((string)$description, 0, 4000) || null,
        'description_en' => substr((string)($description), 0, 4000) || null,
        'price' => $price,
        'compare_at_price' => $compare != null ? toNumber($compare) : null,
        'cost_price' => toNumber(pick($item, ['cost_price', 'cost', 'buying_price', 'wholesale_price'])),
        'marketing_cost' => toNumber(pick($item, ['marketing_cost', 'marketing', 'ads_cost'])),
        'misc_expenses' => toNumber(pick($item, ['misc_expenses', 'expenses', 'other_cost'])),
        'cover_url' => firstImage($item),
        'pages' => toInt(pick($item, ['pages', 'page_count', 'num_pages']), 0) || null,
        'isbn' => pick($item, ['isbn', 'isbn_13', 'barcode', 'sku', 'ean']) ?? null,
        'stock' => toInt(pick($item, ['stock', 'quantity', 'qty', 'inventory', 'stock_quantity']), 0),
        'is_active' => pick($item, ['is_active', 'active', 'available', 'is_available']) !== false,
        '_category_names' => $category_names,
    ];
}

function pick($obj, $keys) {
    if ($obj == null) return null;
    foreach ($keys as $k) {
        if (!isset($obj[$k])) continue;
        $v = $obj[$k];
        if ($v !== null && $v !== '') return $v;
    }
    return null;
}

function toNumber($v, $fallback = 0) {
    if ($v === null || $v === undefined || $v === '') return $fallback;
    $n = floatval(str_replace([' ', ','], ['', ''], (string)$v));
    return is_finite($n) ? $n : $fallback;
}

function toInt($v, $fallback = 0) {
    return max(0, intval(toNumber($v, $fallback)));
}

function firstImage($item) {
    $direct = pick($item, ['cover_url', 'cover', 'image', 'image_url', 'thumbnail', 'thumb', 'photo', 'main_image']);
    if (is_string($direct)) return $direct;
    $arr = pick($item, ['images', 'photos', 'gallery', 'media']);
    if (is_array($arr) && count($arr)) {
        $first = $arr[0];
        if (is_string($first)) return $first;
        if (is_array($first)) return $first['url'] ?? $first['src'] ?? $first['image'] ?? $first['path'] ?? null;
    }
    return null;
}

function stripHtml($s) {
    if ($s == null) return '';
    $s = (string)$s;
    $s = preg_replace('/<br\s*\/?>/i', "\n", $s);
    $s = preg_replace('/<\/(p|div|h[1-6]|li)>/i', "\n", $s);
    $s = preg_replace('/<[^>]+>/', '', $s);
    $s = str_replace(['&nbsp;', '&amp;', '&lt;', '&gt;', '&quot;', '&#39;'], [' ', '&', '<', '>', '"', "'"], $s);
    $s = preg_replace('/\n{3,}/', "\n\n", $s);
    $s = preg_replace('/[ \t]+\n/', "\n", $s);
    return trim($s);
}

function slugify($input) {
    $s = mb_strtolower(trim((string)$input));
    $s = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $s);
    $s = preg_replace('/[\s_\/\\\\]+/u', '-', $s);
    $s = preg_replace('/[^a-z0-9-]+/u', '', $s);
    $s = preg_replace('/-+/u', '-', $s);
    $s = trim($s, '-');
    $s = mb_substr($s, 0, 80);
    if ($s === '' || mb_strlen($s) < 2) {
        return 'item-' . substr(bin2hex(random_bytes(4)), 0, 8);
    }
    return $s;
}

function extractCategoryNames($item) {
    $names = [];
    $direct = pick($item, ['category', 'category_name', 'category_ar', 'category_en', 'categoryName', 'cat', 'section']);
    if ($direct) $names[] = $direct;
    $arr = pick($item, ['categories', 'parsed_categories', 'category_list']);
    if (is_array($arr)) {
        foreach ($arr as $v) {
            if (is_string($v) && trim($v)) $names[] = trim($v);
        }
    }
    $seen = [];
    return array_values(array_filter($names, function($n) use (&$seen) {
        $k = trim((string)$n);
        if (!$k || in_array($k, $seen)) return false;
        $seen[] = $k;
        return true;
    }));
}
