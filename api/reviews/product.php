<?php

use App\Database;
use App\Response;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';

$pdo = Database::connection();
$productId = Validator::uuid($_GET['product_id'] ?? '');

$stmt = $pdo->prepare('SELECT id, user_id, rating, title, comment, created_at FROM reviews WHERE product_id = :product_id ORDER BY created_at DESC LIMIT 100');
$stmt->execute(['product_id' => $productId]);
$reviews = $stmt->fetchAll();

// Fetch names
$userIds = array_unique(array_column($reviews, 'user_id'));
$names = [];
if ($userIds) {
    $in = implode(',', array_fill(0, count($userIds), '?'));
    $stmt = $pdo->prepare("SELECT id, full_name FROM profiles WHERE id IN ({$in})");
    $stmt->execute($userIds);
    foreach ($stmt->fetchAll() as $row) {
        $names[$row['id']] = $row['full_name'] ?: 'مستخدم';
    }
}

foreach ($reviews as &$r) {
    $r['user_name'] = $names[$r['user_id']] ?? 'مستخدم';
}

Response::ok(['reviews' => $reviews]);
