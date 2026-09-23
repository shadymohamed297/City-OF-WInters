<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use App\Database;
use App\Response;

require_once __DIR__ . '/../vendor/autoload.php';

try {
     = Database::connection();

    // 1. Ensure product_categories junction table exists
    ->exec("
        CREATE TABLE IF NOT EXISTS `product_categories` (
          `product_id` char(36) NOT NULL,
          `category_id` char(36) NOT NULL,
          PRIMARY KEY (`product_id`,`category_id`),
          KEY `category_id` (`category_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 2. Check if 'latest-categories' exists
     = ->prepare("SELECT id FROM categories WHERE slug = 'latest-categories' LIMIT 1");
    ->execute();
     = ->fetchColumn();

    if (!) {
         = Database::uuid();
         = ->prepare("
            INSERT INTO categories (
                id, slug, name_ar, name_en, description_ar, description_en,
                display_order, nav_order, show_in_nav, icon, is_active
            ) VALUES (
                :id, 'latest-categories', 'أحدث التصنيفات', 'Newest Categories',
                'أحدث الإصدارات والكتب المضافة لدار نشر مدينة الأدباء',
                'Latest releases and newly added books at Madinat Al-Odabaa',
                0, 0, 1, '✨', 1
            )
        ");
        ->execute(['id' => ]);
    } else {
         = ->prepare("
            UPDATE categories 
            SET name_ar = 'أحدث التصنيفات', name_en = 'Newest Categories',
                display_order = 0, nav_order = 0, icon = '✨', is_active = 1
            WHERE id = :id
        ");
        ->execute(['id' => ]);
    }

    // 3. Update 'اكثر مبيعا' to display_order = 1 and name_ar = 'الأكثر مبيعاً'
    ->exec("
        UPDATE categories 
        SET name_ar = 'الأكثر مبيعاً', name_en = 'Best Sellers',
            display_order = 1, nav_order = 1, icon = '🏆', is_active = 1
        WHERE slug = 'item-8owv2k' OR name_ar LIKE '%مبيع%'
    ");

    // 4. Update order of other categories
    ->exec("UPDATE categories SET display_order = 2, nav_order = 2 WHERE slug = 'novels'");
    ->exec("UPDATE categories SET display_order = 3, nav_order = 3 WHERE slug = 'science'");
    ->exec("UPDATE categories SET display_order = 4, nav_order = 4 WHERE slug = 'history'");
    ->exec("UPDATE categories SET display_order = 5, nav_order = 5 WHERE slug = 'children'");
    ->exec("UPDATE categories SET display_order = 6, nav_order = 6 WHERE slug = 'self-help'");

    // 5. Populate product_categories with top 30 active products for 'latest-categories'
     = ->query("
        SELECT id FROM products 
        WHERE is_active = 1 
        ORDER BY (CASE WHEN cover_url IS NOT NULL AND cover_url != '' THEN 0 ELSE 1 END) ASC, created_at DESC 
        LIMIT 30
    ")->fetchAll(PDO::FETCH_COLUMN);

     = ->prepare("DELETE FROM product_categories WHERE category_id = :cid");
    ->execute(['cid' => ]);

     = ->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (:pid, :cid)");
     = 0;
    foreach ( as ) {
        ->execute(['pid' => , 'cid' => ]);
        ++;
    }

    // 6. Set is_new_arrival = 1 for the 16 newest products with covers
    ->exec("UPDATE products SET is_new_arrival = 0");
     = ->query("
        SELECT id FROM products 
        WHERE is_active = 1 AND cover_url IS NOT NULL AND cover_url != ''
        ORDER BY created_at DESC 
        LIMIT 16
    ")->fetchAll(PDO::FETCH_COLUMN);
    if (!empty()) {
         = implode("','", );
        ->exec("UPDATE products SET is_new_arrival = 1 WHERE id IN ('')");
    }

    // 7. Set is_bestseller = 1 for top 16 popular books
    ->exec("UPDATE products SET is_bestseller = 0");
     = ->query("
        SELECT id FROM products 
        WHERE is_active = 1 AND cover_url IS NOT NULL AND cover_url != ''
          AND (price_usd >= 7 OR author_ar IN ('رحمة نبيل', 'روز أمين', 'نورهان العشري', 'أية محمد رفعت'))
        ORDER BY rating DESC, price DESC 
        LIMIT 16
    ")->fetchAll(PDO::FETCH_COLUMN);
    if (!empty()) {
         = implode("','", );
        ->exec("UPDATE products SET is_bestseller = 1 WHERE id IN ('')");
    }

    // 8. Return stats
     = ->query("SELECT id, slug, name_ar, name_en, display_order, nav_order, icon FROM categories WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
     = (int) ->query("SELECT COUNT(*) FROM products WHERE is_bestseller = 1")->fetchColumn();
     = (int) ->query("SELECT COUNT(*) FROM products WHERE is_new_arrival = 1")->fetchColumn();

    Response::ok([
        'message' => 'Categories reordered successfully!',
        'latest_category_id' => ,
        'linked_products_to_latest' => ,
        'bestseller_count' => ,
        'new_arrival_count' => ,
        'categories' => ,
    ]);
} catch (\Throwable ) {
    Response::serverError(->getMessage() . ' in ' . ->getFile() . ':' . ->getLine());
}