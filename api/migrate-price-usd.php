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

    // 2. Query columns to verify
    $cols = $pdo->query("SHOW COLUMNS FROM products LIKE '%price%'")->fetchAll();

    Response::ok([
        'message' => $added ? 'Columns price_usd and compare_at_price_usd added successfully' : 'Columns already exist',
        'columns' => $cols,
    ]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
