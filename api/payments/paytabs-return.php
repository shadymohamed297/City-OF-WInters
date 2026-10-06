<?php

use App\Database;

require_once __DIR__ . '/../../vendor/autoload.php';

// Customer redirected back from PayTabs after payment attempt
$orderId = $_GET['order_id'] ?? null;
$siteUrl = rtrim(getenv('APP_URL') ?: 'https://www.madinatalodabaa.com', '/');

if (!$orderId) {
    header("Location: {$siteUrl}/checkout?error=" . urlencode('رقم الطلب غير موجود'));
    exit;
}

$pdo = Database::connection();
$stmt = $pdo->prepare('SELECT order_number, payment_status, total FROM orders WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $orderId]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: {$siteUrl}/checkout?error=" . urlencode('الطلب غير موجود'));
    exit;
}

// Check PayTabs response if posted via return POST or GET query params
$respStatus = $_POST['respStatus'] ?? ($_GET['respStatus'] ?? null);
$respMessage = $_POST['respMessage'] ?? ($_GET['respMessage'] ?? null);

// If status in DB is already 'paid', or incoming status indicates success ('A')
if ($order['payment_status'] === 'paid' || $respStatus === 'A') {
    // Redirect to checkout with success state and order number
    header("Location: {$siteUrl}/checkout?success=" . urlencode($order['order_number']));
    exit;
} else {
    // Redirect with error
    $msg = $respMessage ?: 'فشلت عملية الدفع، يمكنك المحاولة مرة أخرى أو اختيار الدفع عند الاستلام';
    header("Location: {$siteUrl}/checkout?payment_failed=" . urlencode($msg));
    exit;
}
