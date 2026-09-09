<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

$pdo = Database::connection();

$stmt = $pdo->prepare('SELECT id, slug, name_ar, name_en, description_ar, description_en, image_url, display_order, is_active FROM categories WHERE is_active = 1 ORDER BY display_order ASC');
$stmt->execute();
$categories = $stmt->fetchAll();

Response::ok(['categories' => $categories]);
