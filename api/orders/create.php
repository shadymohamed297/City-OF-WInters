<?php

use App\Csrf;
use App\Database;
use App\Response;
use App\RateLimiter;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';

Csrf::middleware();

$pdo = Database::connection();
$rateLimiter = new RateLimiter($pdo);

// Rate limit by IP
$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateLimiter->check('order_create:' . $clientIp, 10, 60);

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$fullName = Validator::string($input['full_name'] ?? '', 100, 'Full name');
$phone = Validator::string($input['phone'] ?? '', 30, 'Phone');
if (!preg_match('/^01[0125][0-9]{8}$/', $phone)) {
    Response::validationError('رقم الهاتف غير صحيح');
}
$governorate = Validator::string($input['governorate'] ?? '', 60, 'Governorate');
$city = Validator::string($input['city'] ?? '', 80, 'City');
$street = Validator::string($input['street'] ?? '', 200, 'Street');
$building = Validator::stringOrNull($input['building'] ?? '', 60, 'Building');
$apartment = Validator::stringOrNull($input['apartment'] ?? '', 60, 'Apartment');
$notes = Validator::stringOrNull($input['notes'] ?? '', 500, 'Notes');
$paymentMethod = Validator::enum($input['payment_method'] ?? 'cod', ['cod'], 'Payment method');
$couponCode = Validator::stringOrNull($input['coupon_code'] ?? '', 50, 'Coupon code');

$itemsInput = $input['items'] ?? [];
if (!is_array($itemsInput) || empty($itemsInput)) {
    Response::validationError('Order must contain at least one item');
}

$itemIds = [];
$itemQtys = [];
foreach ($itemsInput as $idx => $it) {
    $pid = Validator::uuid($it['product_id'] ?? '');
    $qty = Validator::int($it['quantity'] ?? 0, 1, 99, 'Quantity for item ' . $idx);
    $itemIds[] = $pid;
    $itemQtys[] = $qty;
}

// Fetch products
$placeholders = implode(',', array_fill(0, count($itemIds), '?'));
$stmt = $pdo->prepare("SELECT id, title_ar, title_en, price, cover_url, stock, is_active FROM products WHERE id IN ({$placeholders}) AND is_active = 1");
$stmt->execute($itemIds);
$products = $stmt->fetchAll();

if (count($products) !== count($itemIds)) {
    Response::validationError('بعض المنتجات لم تعد متوفرة');
}

$productMap = [];
foreach ($products as $p) {
    $productMap[$p['id']] = $p;
}

// Calculate subtotal
$subtotal = 0.0;
$orderItems = [];
foreach ($itemIds as $idx => $pid) {
    $p = $productMap[$pid];
    if (!$p) {
        Response::validationError('منتج غير صالح');
    }
    $qty = $itemQtys[$idx];
    if ((int)$p['stock'] < $qty) {
        Response::validationError('المخزون غير كافٍ للمنتج: ' . $p['title_ar']);
    }
    $line = (float)$p['price'] * $qty;
    $subtotal += $line;
    $orderItems[] = [
        'product_id' => $p['id'],
        'product_title_ar' => $p['title_ar'],
        'product_title_en' => $p['title_en'],
        'product_cover' => $p['cover_url'],
        'unit_price' => $p['price'],
        'quantity' => $qty,
        'line_total' => $line,
    ];
}

$subtotal = round($subtotal, 2);
$shipping = $subtotal >= 500 ? 0 : 50;
$discount = 0.0;
$couponId = null;
$couponCodeStored = null;

if ($couponCode) {
    $code = strtoupper($couponCode);
    $stmt = $pdo->prepare('SELECT * FROM coupons WHERE code = :code LIMIT 1');
    $stmt->execute(['code' => $code]);
    $coupon = $stmt->fetch();

    if ($coupon && $coupon['is_active']) {
        $now = new DateTime();
        $starts = $coupon['starts_at'] ? new DateTime($coupon['starts_at']) : null;
        $expires = $coupon['expires_at'] ? new DateTime($coupon['expires_at']) : null;
        $valid = true;
        if ($starts && $now < $starts) $valid = false;
        if ($expires && $now > $expires) $valid = false;
        if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) $valid = false;
        if ($subtotal < (float)$coupon['min_subtotal']) $valid = false;

        if ($valid) {
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
            $couponId = $coupon['id'];
            $couponCodeStored = $coupon['code'];
        }
    }
}

$total = max(0.0, $subtotal + $shipping - $discount);
$total = round($total, 2);

// Determine user_id if authenticated
$authUserId = $_SESSION['user_id'] ?? null;

$shippingAddress = [
    'full_name' => $fullName,
    'phone' => $phone,
    'email' => $input['email'] ?? null,
    'governorate' => $governorate,
    'city' => $city,
    'street' => $street,
    'building' => $building,
    'apartment' => $apartment,
];

// Transaction
$pdo->beginTransaction();

try {
    $stmt = $pdo->query('SELECT next_val FROM order_number_seq FOR UPDATE');
    $seq = $stmt->fetch();
    $orderNumber = 'ORD-' . ($seq ? (int)$seq['next_val'] : 10001);
    $pdo->exec('UPDATE order_number_seq SET next_val = next_val + 1');

    $orderId = $pdo->lastInsertId();
        'user_id' => $authUserId,
        'guest_email' => $input['email'] ?? null,
        'guest_phone' => $phone,
        'guest_name' => $fullName,
        'status' => 'pending',
        'payment_method' => $paymentMethod,
        'payment_status' => 'pending',
        'subtotal' => $subtotal,
        'shipping_cost' => $shipping,
        'discount' => $discount,
        'total' => $total,
        'shipping_address' => json_encode($shippingAddress, JSON_UNESCAPED_UNICODE),
        'notes' => $notes,
        'coupon_code' => $couponCodeStored,
        'coupon_id' => $couponId,
    ]);
    $orderId = $pdo->lastInsertId();

    foreach ($orderItems as $item) {
        $stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_title_ar, product_title_en, product_cover, unit_price, quantity, line_total) VALUES (:order_id, :product_id, :product_title_ar, :product_title_en, :product_cover, :unit_price, :quantity, :line_total)');
        $stmt->execute([
            'order_id' => $orderId,
            'product_id' => $item['product_id'],
            'product_title_ar' => $item['product_title_ar'],
            'product_title_en' => $item['product_title_en'],
            'product_cover' => $item['product_cover'],
            'unit_price' => $item['unit_price'],
            'quantity' => $item['quantity'],
            'line_total' => $item['line_total'],
        ]);
    }

    // Decrement stock
    foreach ($itemIds as $idx => $pid) {
        $qty = $itemQtys[$idx];
        $stmt = $pdo->prepare('UPDATE products SET stock = GREATEST(0, stock - :qty) WHERE id = :id');
        $stmt->execute(['qty' => $qty, 'id' => $pid]);
    }

    // Update coupon usage
    if ($couponId) {
        $stmt = $pdo->prepare('UPDATE coupons SET used_count = used_count + 1 WHERE id = :id');
        $stmt->execute(['id' => $couponId]);
    }

    $pdo->commit();

    Response::ok([
        'ok' => true,
        'order_number' => $orderNumber,
        'id' => $orderId,
    ]);
} catch (\Throwable $e) {
    $pdo->rollBack();
    Response::serverError('Failed to create order: ' . $e->getMessage());
}
