<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';
Csrf::middleware();

$auth = new Auth(Database::connection());
$user = $auth->requireAuth();

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$productId = Validator::uuid($input['product_id'] ?? '');
$rating = Validator::int($input['rating'] ?? 0, 1, 5, 'Rating');
$title = Validator::stringOrNull($input['title'] ?? '', 120, 'Title');
$comment = Validator::stringOrNull($input['comment'] ?? '', 2000, 'Comment');

$pdo = Database::connection();

// Upsert
$stmt = $pdo->prepare('INSERT INTO reviews (product_id, user_id, rating, title, comment) VALUES (:product_id, :user_id, :rating, :title, :comment) ON DUPLICATE KEY UPDATE rating = :rating, title = :title, comment = :comment, updated_at = NOW()');
$stmt->execute([
    'product_id' => $productId,
    'user_id' => $user['id'],
    'rating' => $rating,
    'title' => $title,
    'comment' => $comment,
]);

// Refresh product rating
$stmt = $pdo->prepare('SELECT COALESCE(ROUND(AVG(rating), 2), 0) as avg_rating, COUNT(*) as cnt FROM reviews WHERE product_id = :product_id');
$stmt->execute(['product_id' => $productId]);
$agg = $stmt->fetch();
$stmt = $pdo->prepare('UPDATE products SET rating = :rating, reviews_count = :count WHERE id = :id');
$stmt->execute(['rating' => $agg['avg_rating'], 'count' => $agg['cnt'], 'id' => $productId]);

Response::ok(['message' => 'Review saved']);
