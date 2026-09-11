<?php

use App\Csrf;
use App\Database;
use App\Response;
use App\Slugify;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = Database::connection();

// GET endpoint: list authors or single author
if ($method === 'GET') {
    $authorId = $_GET['id'] ?? null;
    $withBooks = isset($_GET['with_books']) || isset($_GET['all_books']);

    if ($authorId) {
        $stmt = $pdo->prepare('SELECT * FROM authors WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $authorId]);
        $author = $stmt->fetch();
        if (!$author) {
            Response::notFound('Author not found');
            exit;
        }

        // Get linked books
        $bStmt = $pdo->prepare('
            SELECT id, title_ar, title_en, slug, cover_url, price, stock, is_active 
            FROM products 
            WHERE author_id = :aid OR (author_ar = :aname AND (author_id IS NULL OR author_id = ""))
            ORDER BY display_order ASC, created_at DESC
        ');
        $bStmt->execute(['aid' => $author['id'], 'aname' => $author['name_ar']]);
        $books = $bStmt->fetchAll();

        Response::ok(['author' => $author, 'books' => $books]);
        exit;
    }

    // List all authors with their book counts
    $stmt = $pdo->prepare('
        SELECT a.*, 
          (SELECT COUNT(*) FROM products p WHERE p.author_id = a.id OR (p.author_ar = a.name_ar AND (p.author_id IS NULL OR p.author_id = ""))) AS books_count
        FROM authors a
        ORDER BY a.display_order ASC, a.name_ar ASC
    ');
    $stmt->execute();
    $authors = $stmt->fetchAll();

    $response = ['authors' => $authors];

    if ($withBooks) {
        $bStmt = $pdo->query('SELECT id, title_ar, title_en, author_ar, author_id, cover_url FROM products ORDER BY title_ar ASC');
        $response['all_books'] = $bStmt->fetchAll();
    }

    Response::ok($response);
    exit;
}

// Admin mutations require CSRF and Admin auth
Csrf::middleware();
$auth = new App\Auth($pdo);
$auth->requireAdmin();

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if ($method === 'POST') {
    $nameAr = Validator::string($input['name_ar'] ?? '', 120, 'Name AR');
    $nameEn = Validator::string($input['name_en'] ?? '', 120, 'Name EN');
    if ($nameEn === '' || $nameEn === '—') {
        $nameEn = $nameAr;
    }

    $rawSlug = trim($input['slug'] ?? '');
    if ($rawSlug === '') {
        $cleanSlug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $nameAr);
        $cleanSlug = preg_replace('/\s+/u', '-', trim($cleanSlug));
        if ($cleanSlug === '') {
            $cleanSlug = Slugify::generate($nameEn);
        }
        $rawSlug = $cleanSlug;
    } else {
        $rawSlug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $rawSlug);
        $rawSlug = preg_replace('/\s+/u', '-', trim($rawSlug));
    }

    $existingId = isset($input['id']) && $input['id'] !== '' ? Validator::uuid($input['id']) : null;

    // Ensure unique slug
    $baseSlug = $rawSlug ?: 'author';
    $candidateSlug = $baseSlug;
    $idx = 2;
    while (true) {
        $sQuery = "SELECT id FROM authors WHERE slug = :slug";
        $sParams = ['slug' => $candidateSlug];
        if ($existingId) {
            $sQuery .= " AND id != :id";
            $sParams['id'] = $existingId;
        }
        $sQuery .= " LIMIT 1";
        $sStmt = $pdo->prepare($sQuery);
        $sStmt->execute($sParams);
        if (!$sStmt->fetch()) {
            break;
        }
        $candidateSlug = "{$baseSlug}-{$idx}";
        $idx++;
    }

    $data = [
        'slug' => $candidateSlug,
        'name_ar' => $nameAr,
        'name_en' => $nameEn,
        'bio_ar' => Validator::stringOrNull($input['bio_ar'] ?? '', 5000),
        'bio_en' => Validator::stringOrNull($input['bio_en'] ?? '', 5000),
        'photo_url' => Validator::stringOrNull($input['photo_url'] ?? '', 2000),
        'display_order' => Validator::int($input['display_order'] ?? 0, -99999, 99999),
        'is_active' => Validator::bool($input['is_active'] ?? true),
    ];

    if ($existingId) {
        $authorId = $existingId;
        Database::table('authors')->update('id', $authorId, $data);
    } else {
        $authorId = Database::uuid();
        $data['id'] = $authorId;
        $pdo->prepare('
            INSERT INTO authors (id, slug, name_ar, name_en, bio_ar, bio_en, photo_url, display_order, is_active)
            VALUES (:id, :slug, :name_ar, :name_en, :bio_ar, :bio_en, :photo_url, :display_order, :is_active)
        ')->execute($data);
    }

    // Keep linked products in sync with the author's current name
    $pdo->prepare('UPDATE products SET author_ar = :name_ar, author_en = :name_en WHERE author_id = :aid')->execute([
        'name_ar' => $nameAr,
        'name_en' => $nameEn,
        'aid' => $authorId,
    ]);

    // Link chosen books
    if (isset($input['book_ids']) && is_array($input['book_ids'])) {
        $bookIds = array_values(array_filter(array_unique($input['book_ids']), fn($id) => preg_match('/^[0-9a-f-]{36}$/i', $id)));
        if (!empty($bookIds)) {
            $placeholders = implode(',', array_fill(0, count($bookIds), '?'));
            $linkStmt = $pdo->prepare("UPDATE products SET author_id = ?, author_ar = ?, author_en = ? WHERE id IN ($placeholders)");
            $params = array_merge([$authorId, $nameAr, $nameEn], $bookIds);
            $linkStmt->execute($params);
        }
    }

    // Unlink explicitly removed books
    if (isset($input['unlinked_book_ids']) && is_array($input['unlinked_book_ids'])) {
        $unlinkIds = array_values(array_filter(array_unique($input['unlinked_book_ids']), fn($id) => preg_match('/^[0-9a-f-]{36}$/i', $id)));
        if (!empty($unlinkIds)) {
            $placeholders = implode(',', array_fill(0, count($unlinkIds), '?'));
            $unlinkStmt = $pdo->prepare("UPDATE products SET author_id = NULL, author_ar = '—', author_en = '—' WHERE id IN ($placeholders) AND author_id = ?");
            $params = array_merge($unlinkIds, [$authorId]);
            $unlinkStmt->execute($params);
        }
    }

    Response::ok(['author_id' => $authorId, 'author' => ['id' => $authorId, ...$data]]);
    exit;
}

if ($method === 'DELETE') {
    $id = Validator::uuid($_GET['id'] ?? '');
    // Detach from products
    $pdo->prepare('UPDATE products SET author_id = NULL WHERE author_id = :id')->execute(['id' => $id]);
    Database::table('authors')->delete('id', $id);
    Response::ok(['message' => 'Deleted']);
    exit;
}

Response::error('METHOD_NOT_ALLOWED', 'Method not allowed', 405);
