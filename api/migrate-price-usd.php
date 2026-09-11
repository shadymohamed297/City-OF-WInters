<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $pdo = Database::connection();

    // 1. Check if price_usd column exists
    $colCheck = $pdo->query("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'products' 
          AND COLUMN_NAME = 'price_usd'
    ");
    $hasPriceUsd = (bool) $colCheck->fetchColumn();

    if (!$hasPriceUsd) {
        $pdo->exec("
            ALTER TABLE `products` 
            ADD COLUMN `price_usd` decimal(10,2) DEFAULT NULL AFTER `compare_at_price`,
            ADD COLUMN `compare_at_price_usd` decimal(10,2) DEFAULT NULL AFTER `price_usd`
        ");
        $added = true;
    } else {
        $added = false;
    }

    // 2. Set sample USD prices for testing if currently NULL
    $pdo->exec("
        UPDATE `products` 
        SET `price_usd` = 4.50, `compare_at_price_usd` = 6.00 
        WHERE `slug` LIKE '%ماريسا%' AND `price_usd` IS NULL
    ");
    $pdo->exec("
        UPDATE `products` 
        SET `price_usd` = 5.00, `compare_at_price_usd` = 7.00 
        WHERE `slug` LIKE '%هابيل%' AND `price_usd` IS NULL
    ");

    // 3. Query sample products to verify
    $samples = $pdo->query("SELECT id, slug, title_ar, price, compare_at_price, price_usd, compare_at_price_usd FROM products WHERE price_usd IS NOT NULL LIMIT 5")->fetchAll();

    Response::ok([
        'message' => $added ? 'Columns price_usd and compare_at_price_usd added successfully' : 'Columns already exist',
        'samples' => $samples,
    ]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
