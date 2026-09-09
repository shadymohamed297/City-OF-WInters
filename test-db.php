<?php
require __DIR__ . '/vendor/autoload.php';
use App\Database;

try {
    $pdo = Database::connection();
    $db = $pdo->query("SELECT DATABASE()")->fetchColumn();
    echo "Connected to database: " . $db . "\n";
    
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "Tables found: " . count($tables) . "\n";
    foreach ($tables as $table) {
        echo " - " . implode(", ", $table) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
