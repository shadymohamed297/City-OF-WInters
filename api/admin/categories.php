<?php

use App\Csrf;
use App\Database;
use App\Response;
use App\Slugify;
use App\Validator;

require_once __DIR__ . '/../../../vendor/autoload.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = Database::connection();

if ($method === 'GET') {
    $stmt = $pdo->prepare('SELECT * FROM categories ORDER BY display_order ASC, created_at DESC');
    $stmt->execute();
    $categories = $stmt->fetchAll();
    Response::ok(['categories' => $categories]);
    exit;
}

Csrf::middleware();
$auth = new App\Auth($pdo);
$auth->requireAdmin();

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if ($method === 'POST') {
    $nameAr = Validator::string($input['name_ar'] ?? '', 120, 'Name AR');
    $nameEn = Validator::string($input['name_en'] ?? '', 120, 'Name EN');

    $slug = Validator::stringOrNull($input['slug'] ?? '', 120, 'Slug');
    if (!$slug || mb_strlen($slug) < 2) {
        $slug = Slugify::ensureUnique('categories', $nameEn);
    }

    $data = [
        'slug' => $slug,
        'name_ar' => $nameAr,
        'name_en' => $nameEn,
        'description_ar' => Validator::stringOrNull($input['description_ar'] ?? '', 500),
        'description_en' => Validator::stringOrNull($input['description_en'] ?? '', 500),
        'image_url' => Validator::stringOrNull($input['image_url'] ?? '', 500),
        'icon' => Validator::stringOrNull($input['icon'] ?? '', 50),
        'display_order' => Validator::int($input['display_order'] ?? 0, 0, 9999),
        'nav_order' => Validator::int($input['nav_order'] ?? 0, 0, 9999),
        'parent_id' => filter_var($input['parent_id'] ?? null, FILTER_VALIDATE_UUID) ?: null,
        'show_in_nav' => Validator::bool($input['show_in_nav'] ?? true),
        'is_active' => Validator::bool($input['is_active'] ?? true),
    ];

    if (isset($input['id'])) {
        $id = Validator::uuid($input['id']);
        Database::table('categories')->update('id', $id, $data);
        Response::ok(['category' => ['id' => $id, ...$data]]);
    } else {
        $id = Database::table('categories')->insert($data);
        Response::ok(['category' => ['id' => $id, ...$data]]);
    }
    exit;
}

if ($method === 'DELETE') {
    $id = Validator::uuid($_GET['id'] ?? '');
    Database::table('categories')->delete('id', $id);
    Response::ok(['message' => 'Deleted']);
    exit;
}

Response::error('METHOD_NOT_ALLOWED', 'Method not allowed', 405);
