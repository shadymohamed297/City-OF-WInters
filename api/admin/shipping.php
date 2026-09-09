<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;

require_once __DIR__ . '/../../../vendor/autoload.php';

Csrf::middleware();
$auth = new App\Auth(Database::connection());
$auth->requireAdmin();

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$rates = $input['rates'] ?? [];
if (!is_array($rates)) {
    Response::validationError('rates must be an array');
}

$pdo = Database::connection();
$stmt = $pdo->prepare('INSERT INTO shipping_rates (governorate_en, governorate_ar, price, enabled) VALUES (:governorate_en, :governorate_ar, :price, :enabled) ON DUPLICATE KEY UPDATE price = :price, enabled = :enabled, updated_at = NOW()');

foreach ($rates as $r) {
    $stmt->execute([
        'governorate_en' => $r['governorate_en'] ?? '',
        'governorate_ar' => $r['governorate_ar'] ?? '',
        'price' => $r['price'] ?? 0,
        'enabled' => $r['enabled'] ?? true,
    ]);
}

Response::ok(['message' => 'Rates updated']);
