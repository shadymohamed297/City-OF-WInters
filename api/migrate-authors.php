<?php

use App\Database;
use App\Response;
use App\Slugify;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $pdo = Database::connection();

    // 1. Create authors table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `authors` (
          `id` char(36) NOT NULL,
          `slug` varchar(120) NOT NULL,
          `name_ar` varchar(120) NOT NULL,
          `name_en` varchar(120) NOT NULL,
          `bio_ar` text DEFAULT NULL,
          `bio_en` text DEFAULT NULL,
          `photo_url` text DEFAULT NULL,
          `is_active` tinyint(1) NOT NULL DEFAULT 1,
          `display_order` int(11) NOT NULL DEFAULT 0,
          `created_at` datetime NOT NULL DEFAULT current_timestamp(),
          `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          PRIMARY KEY (`id`),
          UNIQUE KEY `slug` (`slug`),
          KEY `name_ar` (`name_ar`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 2. Add author_id to products if not exists
    $colCheck = $pdo->query("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'products' 
          AND COLUMN_NAME = 'author_id'
    ");
    $hasAuthorId = (bool) $colCheck->fetchColumn();

    if (!$hasAuthorId) {
        $pdo->exec("ALTER TABLE `products` ADD COLUMN `author_id` char(36) DEFAULT NULL AFTER `category_id`, ADD INDEX (`author_id`)");
    }

    // 3. Auto-seed authors from existing products where author_ar is set
    $stmt = $pdo->query("
        SELECT DISTINCT author_ar, author_en 
        FROM products 
        WHERE author_ar IS NOT NULL 
          AND author_ar != '' 
          AND author_ar != '—'
    ");
    $existingProductAuthors = $stmt->fetchAll();

    $seeded = [];
    foreach ($existingProductAuthors as $row) {
        $nameAr = trim($row['author_ar']);
        $nameEn = trim($row['author_en'] ?? '');
        if ($nameEn === '' || $nameEn === '—') {
            $nameEn = $nameAr;
        }

        // Check if already in authors table
        $check = $pdo->prepare("SELECT id FROM authors WHERE name_ar = :name_ar LIMIT 1");
        $check->execute(['name_ar' => $nameAr]);
        $existingAuthor = $check->fetch();

        if (!$existingAuthor) {
            $authorId = Database::uuid();
            
            // Build a readable slug
            $cleanSlug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $nameAr);
            $cleanSlug = preg_replace('/\s+/u', '-', trim($cleanSlug));
            if ($cleanSlug === '') {
                $cleanSlug = Slugify::generate($nameEn);
            }
            // Ensure unique slug
            $baseSlug = $cleanSlug;
            $slugCandidate = $baseSlug;
            $idx = 2;
            while (true) {
                $sCheck = $pdo->prepare("SELECT id FROM authors WHERE slug = :slug LIMIT 1");
                $sCheck->execute(['slug' => $slugCandidate]);
                if (!$sCheck->fetch()) {
                    break;
                }
                $slugCandidate = "{$baseSlug}-{$idx}";
                $idx++;
            }

            $ins = $pdo->prepare("
                INSERT INTO authors (id, slug, name_ar, name_en, is_active, display_order) 
                VALUES (:id, :slug, :name_ar, :name_en, 1, 0)
            ");
            $ins->execute([
                'id' => $authorId,
                'slug' => $slugCandidate,
                'name_ar' => $nameAr,
                'name_en' => $nameEn,
            ]);

            $seeded[] = ['id' => $authorId, 'name_ar' => $nameAr, 'slug' => $slugCandidate];
        } else {
            $authorId = $existingAuthor['id'];
        }

        // Associate with products
        $upd = $pdo->prepare("UPDATE products SET author_id = :author_id WHERE author_ar = :name_ar AND (author_id IS NULL OR author_id = '')");
        $upd->execute(['author_id' => $authorId, 'name_ar' => $nameAr]);
    }

    Response::ok([
        'message' => 'Authors table migrated successfully',
        'has_author_id' => true,
        'seeded_authors_count' => count($seeded),
        'seeded' => $seeded,
    ]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
