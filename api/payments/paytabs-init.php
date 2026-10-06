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

$clientIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$rateLimiter->check('paytabs_init:' . $clientIp, 10, 60);

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$fullName    = Validator::string($input['full_name'] ?? '', 100, 'Full name');
$phone       = Validator::string($input['phone'] ?? '', 30, 'Phone');
$governorate = Validator::string($input['governorate'] ?? '', 60, 'Governorate');
$city        = Validator::string($input['city'] ?? '', 80, 'City');
$street      = Validator::string($input['street'] ?? '', 200, 'Street');
$building    = Validator::stringOrNull($input['building'] ?? '', 60);
$apartment   = Validator::stringOrNull($input['apartment'] ?? '', 60);
$notes       = Validator::stringOrNull($input['notes'] ?? '', 500);
$email       = Validator::stringOrNull($input['email'] ?? '', 255);
$couponCode  = Validator::stringOrNull($input['coupon_code'] ?? '', 50);

$requestedCurrency = strtoupper(trim($input['currency'] ?? 'EGP'));
$currency = in_array($requestedCurrency, ['EGP', 'USD'], true) ? $requestedCurrency : 'EGP';

if ($currency === 'EGP' && !preg_match('/^01[0125][0-9]{8}$/', $phone)) {
    Response::validationError('رقم الهاتف غير صحيح');
}
if ($currency === 'USD') {
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Response::validationError('البريد الإلكتروني مطلوب للطلبات الدولية');
    }
}

$itemsInput = $input['items'] ?? [];
if (!is_array($itemsInput) || empty($itemsInput)) {
    Response::validationError('يجب أن يحتوي الطلب على منتج واحد على الأقل');
}

$itemIds = []; $itemQtys = [];
foreach ($itemsInput as $idx => $it) {
    $itemIds[]  = Validator::uuid($it['product_id'] ?? '');
    $itemQtys[] = Validator::int($it['quantity'] ?? 0, 1, 99, "Qty item {$idx}");
}

$placeholders = implode(',', array_fill(0, count($itemIds), '?'));
$stmt = $pdo->prepare("SELECT id, title_ar, title_en, price, price_usd, cover_url, stock, is_active FROM products WHERE id IN ({$placeholders}) AND is_active = 1");
$stmt->execute($itemIds);
$products = $stmt->fetchAll();

if (count($products) !== count($itemIds)) Response::validationError('بعض المنتجات لم تعد متوفرة');

$productMap = [];
foreach ($products as $p) $productMap[$p['id']] = $p;

$subtotal = 0.0; $orderItems = [];
foreach ($itemIds as $idx => $pid) {
    $p   = $productMap[$pid];
    $qty = $itemQtys[$idx];
    if ((int)$p['stock'] < $qty) Response::validationError('المخزون غير كافٍ: ' . $p['title_ar']);
    $unitPrice = $currency === 'USD'
        ? (float)($p['price_usd'] ?: round((float)$p['price'] / 50, 2))
        : (float)$p['price'];
    $line = round($unitPrice * $qty, 2);
    $subtotal += $line;
    $orderItems[] = [
        'product_id'       => $p['id'],
        'product_title_ar' => $p['title_ar'],
        'product_title_en' => $p['title_en'],
        'product_cover'    => $p['cover_url'],
        'unit_price'       => $unitPrice,
        'quantity'         => $qty,
        'line_total'       => $line,
    ];
}

$subtotal = round($subtotal, 2);
$shipping = $currency === 'USD' ? ($subtotal >= 15 ? 0 : 5) : ($subtotal >= 500 ? 0 : 50);

$discount = 0.0; $couponId = null; $couponCodeStored = null;
if ($couponCode) {
    $code = strtoupper($couponCode);
    $stmt = $pdo->prepare('SELECT * FROM coupons WHERE code = :code LIMIT 1');
    $stmt->execute(['code' => $code]);
    $coupon = $stmt->fetch();
    if ($coupon && $coupon['is_active']) {
        $now = new DateTime();
        $starts  = $coupon['starts_at']  ? new DateTime($coupon['starts_at'])  : null;
        $expires = $coupon['expires_at'] ? new DateTime($coupon['expires_at']) : null;
        $valid = true;
        if ($starts  && $now < $starts)  $valid = false;
        if ($expires && $now > $expires) $valid = false;
        if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) $valid = false;
        if ($subtotal < (float)$coupon['min_subtotal']) $valid = false;
        if ($valid) {
            $discount = $coupon['type'] === 'percent'
                ? $subtotal * ((float)$coupon['value'] / 100)
                : (float)$coupon['value'];
            if ($coupon['max_discount']) $discount = min($discount, (float)$coupon['max_discount']);
            $discount = min(round($discount, 2), $subtotal);
            $couponId = $coupon['id']; $couponCodeStored = $coupon['code'];
        }
    }
}

$total = round(max(0.0, $subtotal + $shipping - $discount), 2);
$authUserId = $_SESSION['user_id'] ?? null;
$shippingAddress = compact('fullName','phone','email','governorate','city','street','building','apartment');

$profileId = getenv('PAYTABS_PROFILE_ID') ?: '155739';
$serverKey = getenv('PAYTABS_SERVER_KEY')  ?: 'SMJ9WRDZ9L-J9R6ZKH6JL-6JNJNZ9DDD';
$baseUrl   = getenv('PAYTABS_BASE_URL')    ?: 'https://secure-egypt.paytabs.com';
$siteUrl   = rtrim(getenv('APP_URL') ?: 'https://www.madinatalodabaa.com', '/');

$pdo->beginTransaction();
try {
    $stmt = $pdo->query('SELECT next_val FROM order_number_seq FOR UPDATE');
    $seq  = $stmt->fetch();
    $orderNumber = 'ORD-' . ($seq ? (int)$seq['next_val'] : 10001);
    $pdo->exec('UPDATE order_number_seq SET next_val = next_val + 1');
    $orderId = Database::uuid();

    $stmt = $pdo->prepare('INSERT INTO orders (id,order_number,user_id,guest_email,guest_phone,guest_name,status,payment_method,payment_status,subtotal,shipping_cost,discount,total,shipping_address,notes,coupon_code,coupon_id) VALUES (:id,:order_number,:user_id,:guest_email,:guest_phone,:guest_name,:status,:payment_method,:payment_status,:subtotal,:shipping_cost,:discount,:total,:shipping_address,:notes,:coupon_code,:coupon_id)');
    $stmt->execute([
        'id'=>$orderId,'order_number'=>$orderNumber,'user_id'=>$authUserId,
        'guest_email'=>$email,'guest_phone'=>$phone,'guest_name'=>$fullName,
        'status'=>'pending','payment_method'=>'paytabs','payment_status'=>'pending',
        'subtotal'=>$subtotal,'shipping_cost'=>$shipping,'discount'=>$discount,'total'=>$total,
        'shipping_address'=>json_encode($shippingAddress,JSON_UNESCAPED_UNICODE),
        'notes'=>$notes,'coupon_code'=>$couponCodeStored,'coupon_id'=>$couponId,
    ]);

    $stmt = $pdo->prepare('INSERT INTO order_items (order_id,product_id,product_title_ar,product_title_en,product_cover,unit_price,quantity,line_total) VALUES (:order_id,:product_id,:product_title_ar,:product_title_en,:product_cover,:unit_price,:quantity,:line_total)');
    foreach ($orderItems as $item) $stmt->execute(array_merge(['order_id'=>$orderId], $item));

    $pdo->commit();
} catch (\Throwable $e) {
    $pdo->rollBack();
    Response::serverError('تعذر إنشاء الطلب: ' . $e->getMessage());
}

$cartDesc = mb_substr(implode(', ', array_map(fn($i) => $i['product_title_ar'].' x'.$i['quantity'], $orderItems)), 0, 250);
$countryCode = $currency === 'USD' ? 'US' : 'EG';

$ptPayload = [
    'profile_id'       => (int)$profileId,
    'tran_type'        => 'sale',
    'tran_class'       => 'ecom',
    'cart_id'          => $orderId,
    'cart_currency'    => $currency,
    'cart_amount'      => $total,
    'cart_description' => $cartDesc,
    'customer_details' => [
        'name'    => $fullName,
        'email'   => $email ?: 'guest@madinatalodabaa.com',
        'phone'   => $phone,
        'street1' => mb_substr(trim("$street " . ($building ?? '')), 0, 100),
        'city'    => $city,
        'state'   => $governorate,
        'country' => $countryCode,
        'zip'     => '00000',
        'ip'      => $clientIp,
    ],
    'shipping_details' => [
        'name'    => $fullName,
        'email'   => $email ?: 'guest@madinatalodabaa.com',
        'phone'   => $phone,
        'street1' => mb_substr(trim("$street " . ($building ?? '')), 0, 100),
        'city'    => $city,
        'state'   => $governorate,
        'country' => $countryCode,
        'zip'     => '00000',
    ],
    'return'        => "$siteUrl/api/payments/paytabs-return.php?order_id=" . urlencode($orderId),
    'callback'      => "$siteUrl/api/payments/paytabs-callback.php",
    'hide_shipping' => true,
    'framed'        => false,
    'lang'          => 'ar',
];

$ch = curl_init($baseUrl . '/payment/request');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($ptPayload),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Authorization: ' . $serverKey],
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => true,
]);

$ptResponse = curl_exec($ch);
$curlErr    = curl_error($ch);
curl_close($ch);

if ($curlErr) Response::serverError('خطأ في الاتصال ببوابة الدفع: ' . $curlErr);

$ptData = json_decode($ptResponse, true);
if (!$ptData || empty($ptData['redirect_url'])) {
    $errMsg = $ptData['message'] ?? ($ptData['details'] ?? $ptResponse);
    Response::serverError('تعذر الحصول على رابط الدفع: ' . $errMsg);
}

try {
    $pdo->prepare("UPDATE orders SET paytabs_tran_ref = :ref WHERE id = :id")
        ->execute(['ref' => $ptData['tran_ref'] ?? null, 'id' => $orderId]);
} catch (\Throwable $e) { /* column may not exist yet */ }

Response::ok([
    'payment_url'  => $ptData['redirect_url'],
    'tran_ref'     => $ptData['tran_ref'] ?? null,
    'order_id'     => $orderId,
    'order_number' => $orderNumber,
]);
