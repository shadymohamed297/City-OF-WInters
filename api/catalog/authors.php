<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $pdo = Database::connection();

    $stmt = $pdo->prepare('
        SELECT a.id, a.slug, a.name_ar, a.name_en, a.photo_url, a.bio_ar, a.bio_en,
          (SELECT COUNT(*) FROM products p WHERE (p.author_id = a.id OR (p.author_ar = a.name_ar AND (p.author_id IS NULL OR p.author_id = ""))) AND p.is_active = 1) AS books_count
        FROM authors a
        WHERE a.is_active = 1
        ORDER BY a.display_order ASC, a.name_ar ASC
    ');
    $stmt->execute();
    $authors = $stmt->fetchAll();

    Response::ok(['authors' => $authors]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
