<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use App\Database;
use App\Response;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $pdo = Database::connection();

    // 1. Ensure authors table exists
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

    // Ensure columns exist on products
    $colCheck1 = $pdo->query("
        SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'author_id'
    ")->fetchColumn();
    if (!$colCheck1) {
        $pdo->exec("ALTER TABLE `products` ADD COLUMN `author_id` char(36) DEFAULT NULL AFTER `category_id`, ADD INDEX (`author_id`)");
    }

    $colCheck2 = $pdo->query("
        SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'price_usd'
    ")->fetchColumn();
    if (!$colCheck2) {
        $pdo->exec("ALTER TABLE `products` ADD COLUMN `price_usd` decimal(10,2) DEFAULT NULL AFTER `compare_at_price`, ADD COLUMN `compare_at_price_usd` decimal(10,2) DEFAULT NULL AFTER `price_usd`");
    }

    $authorsJson = file_get_contents(__DIR__ . '/authors_data.json');
    $booksJson = file_get_contents(__DIR__ . '/books_data.json');

    $authorsList = json_decode($authorsJson, true) ?: [];
    $booksList = json_decode($booksJson, true) ?: [];

    // 2. Upsert all 60 Authors
    $authorMap = [];
    $authorsCreated = 0;
    $authorsUpdated = 0;

    foreach ($authorsList as $a) {
        $check = $pdo->prepare("SELECT id FROM authors WHERE name_ar = :name_ar OR slug = :slug LIMIT 1");
        $check->execute(['name_ar' => $a['name_ar'], 'slug' => $a['slug']]);
        $existing = $check->fetch();

        if ($existing) {
            $authorId = $existing['id'];
            $upd = $pdo->prepare("
                UPDATE authors 
                SET name_ar = :name_ar, name_en = :name_en, slug = :slug, bio_ar = :bio_ar, bio_en = :bio_en, is_active = 1
                WHERE id = :id
            ");
            $upd->execute([
                'name_ar' => $a['name_ar'],
                'name_en' => $a['name_en'],
                'slug' => $a['slug'],
                'bio_ar' => $a['bio_ar'],
                'bio_en' => $a['bio_en'],
                'id' => $authorId,
            ]);
            $authorsUpdated++;
        } else {
            $authorId = Database::uuid();
            $ins = $pdo->prepare("
                INSERT INTO authors (id, slug, name_ar, name_en, bio_ar, bio_en, is_active, display_order)
                VALUES (:id, :slug, :name_ar, :name_en, :bio_ar, :bio_en, 1, 0)
            ");
            $ins->execute([
                'id' => $authorId,
                'slug' => $a['slug'],
                'name_ar' => $a['name_ar'],
                'name_en' => $a['name_en'],
                'bio_ar' => $a['bio_ar'],
                'bio_en' => $a['bio_en'],
            ]);
            $authorsCreated++;
        }
        $authorMap[$a['name_ar']] = [
            'id' => $authorId,
            'name_ar' => $a['name_ar'],
            'name_en' => $a['name_en'],
            'slug' => $a['slug'],
        ];
    }

    // Normalizer helper
    $cleanAr = function (?string $text): string {
        if (!$text) return '';
        $t = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text);
        $t = preg_replace('/["״\'«»()—\-\–\.\،\:\/]/u', ' ', $t);
        $t = preg_replace('/[إأآاٱ]/u', 'ا', $t);
        $t = preg_replace('/ة/u', 'ه', $t);
        $t = preg_replace('/ى/u', 'ي', $t);
        $t = preg_replace('/\s+/u', ' ', $t);
        return trim($t);
    };

    $titleAliases = [
        $cleanAr('صرخات أنثى 1 - ثلاث كتب') => $cleanAr('صرخات أنثى "ثلاث كتب" الأجزاء الأولى'),
        $cleanAr('صرخات أنثى 2 - أربع كتب') => $cleanAr('صرخات أنثى "أربع كتب" الأجزاء الأخيرة'),
        $cleanAr('معزوفة الحب والوجع (قلوب حائرة "الجزء الثاني") - 4 كتب') => $cleanAr('معزوفة الحب والوجع "أربع كتب معًا"'),
        $cleanAr('قلوب حائرة "ثلاث كتب معًا"') => $cleanAr('قلوب حائرة "ثلاث كتب"'),
        $cleanAr('لها بلقبك شيء') => $cleanAr('لها بقلبك شيء'),
    ];

    // 3. Fetch all current products
    $allProducts = $pdo->query("SELECT id, slug, title_ar, title_en, price, compare_at_price, category_id FROM products")->fetchAll();

    $productsUpdated = 0;
    $productsInserted = 0;
    $matchedIds = [];

    $catNovels = $pdo->query("SELECT id FROM categories WHERE slug = 'novels' LIMIT 1")->fetchColumn();
    $catSelfHelp = $pdo->query("SELECT id FROM categories WHERE slug = 'self-help' LIMIT 1")->fetchColumn();
    $catScience = $pdo->query("SELECT id FROM categories WHERE slug = 'science' LIMIT 1")->fetchColumn();

    foreach ($booksList as $b) {
        $authorName = $b['author'];
        $authorInfo = $authorMap[$authorName] ?? null;
        if (!$authorInfo) continue;

        $rawBook = $b['book'];
        $cleanBook = $cleanAr($rawBook);
        $targetClean = $titleAliases[$cleanBook] ?? $cleanBook;
        $usdPrice = (float) $b['price_usd'];

        // Find match in products
        $matchedProduct = null;
        foreach ($allProducts as $p) {
            if (isset($matchedIds[$p['id']])) continue;
            $pClean = $cleanAr($p['title_ar']);
            $pSlugClean = $cleanAr(str_replace('-', ' ', $p['slug']));

            if ($targetClean === $pClean || 
                $targetClean === $pSlugClean ||
                ($cleanBook !== '' && str_contains($pClean, $cleanBook)) ||
                ($targetClean !== '' && str_contains($pClean, $targetClean)) ||
                ($pClean !== '' && str_contains($targetClean, $pClean))) {
                $matchedProduct = $p;
                break;
            }
        }

        if ($matchedProduct) {
            $pid = $matchedProduct['id'];
            $matchedIds[$pid] = true;

            $compareUsd = null;
            if (!empty($matchedProduct['compare_at_price']) && !empty($matchedProduct['price']) && $matchedProduct['compare_at_price'] > $matchedProduct['price']) {
                $ratio = (float) $matchedProduct['compare_at_price'] / (float) $matchedProduct['price'];
                $compareUsd = round($usdPrice * $ratio, 2);
            }

            $upd = $pdo->prepare("
                UPDATE products 
                SET author_id = :aid,
                    author_ar = :aname_ar,
                    author_en = :aname_en,
                    price_usd = :price_usd,
                    compare_at_price_usd = :compare_usd
                WHERE id = :id
            ");
            $upd->execute([
                'aid' => $authorInfo['id'],
                'aname_ar' => $authorInfo['name_ar'],
                'aname_en' => $authorInfo['name_en'],
                'price_usd' => $usdPrice,
                'compare_usd' => $compareUsd,
                'id' => $pid,
            ]);
            $productsUpdated++;
        } else {
            $newId = Database::uuid();
            $matchedIds[$newId] = true;

            $cleanSlug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $rawBook);
            $cleanSlug = preg_replace('/\s+/u', '-', trim($cleanSlug));
            if ($cleanSlug === '') {
                $cleanSlug = 'book-' . substr($newId, 0, 8);
            }

            $catId = $catNovels;
            if (str_contains($rawBook, 'مشاعرك') || str_contains($rawBook, 'العلاقات') || str_contains($rawBook, 'طبيب نفسي')) {
                $catId = $catSelfHelp ?: $catNovels;
            } elseif (str_contains($rawBook, 'القرآن')) {
                $catId = $catScience ?: $catNovels;
            }

            $egpPrice = round($usdPrice * 25.0, 0);

            $ins = $pdo->prepare("
                INSERT INTO products (
                    id, slug, title_ar, title_en, author_id, author_ar, author_en,
                    category_id, price, price_usd, compare_at_price_usd, stock, unlimited_stock,
                    publisher_ar, publisher_en, is_active, display_order
                ) VALUES (
                    :id, :slug, :title_ar, :title_en, :author_id, :author_ar, :author_en,
                    :category_id, :price, :price_usd, NULL, 50, 1,
                    'دار نشر مدينة الأدباء', 'Madinat Al-Odabaa Publishing', 1, 0
                )
            ");
            $ins->execute([
                'id' => $newId,
                'slug' => $cleanSlug,
                'title_ar' => $rawBook,
                'title_en' => $rawBook,
                'author_id' => $authorInfo['id'],
                'author_ar' => $authorInfo['name_ar'],
                'author_en' => $authorInfo['name_en'],
                'category_id' => $catId,
                'price' => $egpPrice,
                'price_usd' => $usdPrice,
            ]);
            $productsInserted++;
        }
    }

    // 4. Remove stale test authors that have 0 books
    $del = $pdo->prepare("
        DELETE FROM authors 
        WHERE id NOT IN (SELECT DISTINCT author_id FROM products WHERE author_id IS NOT NULL AND author_id != '')
          AND slug IN ('ahmed-khaled-tawfik', 'khawla-hamdi', 'marwan-al-roumi')
    ");
    $del->execute();

    // 5. Query verification stats
    $totalAuthorsInDb = (int) $pdo->query("SELECT COUNT(*) FROM authors WHERE is_active = 1")->fetchColumn();
    $totalProductsInDb = (int) $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
    $productsWithAuthor = (int) $pdo->query("SELECT COUNT(*) FROM products WHERE author_id IS NOT NULL AND is_active = 1")->fetchColumn();
    $productsWithUsd = (int) $pdo->query("SELECT COUNT(*) FROM products WHERE price_usd IS NOT NULL AND is_active = 1")->fetchColumn();

    $sampleAuthors = $pdo->query("
        SELECT a.name_ar, a.slug, COUNT(p.id) as books_count, ROUND(AVG(p.price_usd), 2) as avg_usd
        FROM authors a
        JOIN products p ON p.author_id = a.id
        GROUP BY a.id, a.name_ar, a.slug
        ORDER BY books_count DESC
        LIMIT 10
    ")->fetchAll();

    Response::ok([
        'message' => 'Catalog, authors, and USD pricing synced successfully!',
        'authors_created' => $authorsCreated,
        'authors_updated' => $authorsUpdated,
        'products_updated' => $productsUpdated,
        'products_inserted' => $productsInserted,
        'total_active_authors' => $totalAuthorsInDb,
        'total_active_products' => $totalProductsInDb,
        'products_with_author_id' => $productsWithAuthor,
        'products_with_price_usd' => $productsWithUsd,
        'top_authors' => $sampleAuthors,
    ]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
}
