<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $pdo = Database::connection();
    $stmt = $pdo->query("SELECT id, title_ar, author_ar, author_id FROM products WHERE author_ar != '—' AND author_ar != '' LIMIT 10");
    $booksWithAuthor = $stmt->fetchAll();

    $aStmt = $pdo->query("SELECT * FROM authors");
    $allAuthors = $aStmt->fetchAll();

    Response::ok([
        'books_with_author' => $booksWithAuthor,
        'authors_count' => count($allAuthors),
        'authors' => $allAuthors
    ]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
