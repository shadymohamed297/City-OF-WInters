<?php

use App\Csrf;
use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

// Validate coupon can be called without auth (guest checkout)
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$code = strtoupper(trim($input['code'] ?? ''));
$subtotal = isset($input['subtotal']) ? (float)$input['subtotal'] : 0;

if ($code === '') {
    Response::validationError('Coupon code is required');
}

$pdo = Database::connection();
$stmt = $pdo->prepare('SELECT id, code, type, value, min_subtotal, max_discount, usage_limit, used_count, starts_at, expires_at, is_active FROM coupons WHERE code = :code LIMIT 1');
$stmt->execute(['code' => $code]);
$coupon = $stmt->fetch();

if (!$coupon || !$coupon['is_active']) {
    Response::error('INVALID_COUPON', 'كوبون غير صالح', 404);
}

$now = new DateTime();
if ($coupon['starts_at']) {
    $starts = new DateTime($coupon['starts_at']);
    if ($now < $starts) {
        Response::error('COUPON_NOT_STARTED', 'الكوبون لم يبدأ بعد', 422);
    }
}
if ($coupon['expires_at']) {
    $expires = new DateTime($coupon['expires_at']);
    if ($now > $expires) {
        Response::error('COUPON_EXPIRED', 'الكوبون منتهي', 422);
    }
}
if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
    Response::error('COUPON_EXHAUSTED', 'استُنفد حد الاستخدام', 422);
}
if ($subtotal < (float)$coupon['min_subtotal']) {
    Response::error('COUPON_MIN_NOT_MET', 'الحد الأدنى للطلب ' . $coupon['min_subtotal'] . ' ج.م', 422);
}

if ($coupon['type'] === 'percent') {
    $discount = $subtotal * ((float)$coupon['value'] / 100);
} else {
    $discount = (float)$coupon['value'];
}
if ($coupon['max_discount']) {
    $discount = min($discount, (float)$coupon['max_discount']);
}
$discount = min($discount, $subtotal);
$discount = round($discount, 2);

Response::ok([
    'id' => $coupon['id'],
    'code' => $coupon['code'],
    'type' => $coupon['type'],
    'value' => (float)$coupon['value'],
    'discount' => $discount,
]);
