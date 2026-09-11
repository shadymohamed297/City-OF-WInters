<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $pdo = Database::connection();
    $stmt = $pdo->query("SELECT id, slug, title_ar FROM products ORDER BY id LIMIT 15");
    $sampleBooks = $stmt->fetchAll();

    Response::ok([
        'sample_books' => $sampleBooks
    ]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
