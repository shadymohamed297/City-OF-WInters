<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $pdo = Database::connection();

    $stmt = $pdo->query("SELECT id, slug, title_ar FROM products");
    $products = $stmt->fetchAll();

    $normalize = function (string $text): string {
        $clean = preg_replace('/[\x{2000}-\x{206F}\x{FEFF}\x{FFFD}]/u', '', $text);
        $clean = preg_replace('/[_\-"\'«»()\[\]]/u', ' ', $clean);
        $clean = preg_replace('/\s+/u', '-, trim($clean));
        return trim($clean, '-);
    };

    $updated = [];

    foreach ($products as $p) {
        $slug = $p['slug'];
        if (preg_match('/[\x{2000}-\x{206F}\x{FEFF}\x{FFFD}]/u', $slug)) {
            $clean = preg_replace('/[\x{2000}-\x{206F}\x{FEFF}\x{FFFD}]/u', '', $slug);
            if (str_contains($clean, "\ufffd") || trim($clean) === '') {
                $clean = $normalize($p['title_ar']);
            }
            $clean = trim($clean, '-);
            if ($clean !== '' && $clean !== $slug) {
                $upd = $pdo->prepare("UPDATE products SET slug = :slug WHERE id = :id");
                $upd->execute(['slug' => $clean, 'id' => $p['id']]);
                $updated[] = ['id' => $p['id'], 'old_slug' => $slug, 'new_slug' => $clean, 'title' => $p['title_ar']];
            }
        }
    }

    Response::ok(['updated_count' => count($updated), 'updated' => $updated]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
