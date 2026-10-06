<?php

use App\Database;

require_once __DIR__ . '/../../vendor/autoload.php';

// IPN Callback from PayTabs (Server-to-Server)
// Do not use CSRF middleware here.

$rawBody = file_get_contents('php://input');
$data = json_decode($rawBody, true) ?: $_POST;

if (empty($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'No data received']);
    exit;
}

$pdo = Database::connection();

// Extract payload details
$tranRef   = $data['tran_ref'] ?? null;
$cartId    = $data['cart_id'] ?? null; // order_id in our DB
$respCode  = $data['payment_result']['response_code'] ?? ($data['response_code'] ?? null);
$respStatus = $data['payment_result']['response_status'] ?? ($data['response_status'] ?? null); // 'A' for Authorised/Approved
$respMsg   = $data['payment_result']['response_message'] ?? ($data['response_message'] ?? '');

if (!$cartId) {
    http_response_code(400);
    echo json_encode(['error' => 'cart_id missing']);
    exit;
}

// Find order
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $cartId]);
$order = $stmt->fetch();

if (!$order) {
    http_response_code(404);
    echo json_encode(['error' => 'Order not found']);
    exit;
}

// Check status: 'A' means successful payment
$isSuccess = ($respStatus === 'A' || $respCode === '100');

$pdo->beginTransaction();
try {
    if ($isSuccess) {
        // If order was not marked paid yet, decrement stock and mark paid
        if ($order['payment_status'] !== 'paid') {
            $stmt = $pdo->prepare('
                UPDATE orders 
                SET payment_status = "paid", 
                    status = "confirmed", 
                    paytabs_tran_ref = COALESCE(:ref, paytabs_tran_ref),
                    updated_at = NOW() 
                WHERE id = :id
            ');
            $stmt->execute(['ref' => $tranRef, 'id' => $cartId]);

            // Decrement stock for order items
            $itemStmt = $pdo->prepare('SELECT product_id, quantity FROM order_items WHERE order_id = :order_id');
            $itemStmt->execute(['order_id' => $cartId]);
            $items = $itemStmt->fetchAll();

            foreach ($items as $item) {
                $updStock = $pdo->prepare('UPDATE products SET stock = GREATEST(0, stock - :qty) WHERE id = :id');
                $updStock->execute(['qty' => $item['quantity'], 'id' => $item['product_id']]);
            }

            // Increment coupon usage if applicable
            if (!empty($order['coupon_id'])) {
                $cStmt = $pdo->prepare('UPDATE coupons SET used_count = used_count + 1 WHERE id = :id');
                $cStmt->execute(['id' => $order['coupon_id']]);
            }
        }
    } else {
        // Payment failed
        if ($order['payment_status'] !== 'paid') {
            $stmt = $pdo->prepare('
                UPDATE orders 
                SET payment_status = "failed",
                    paytabs_tran_ref = COALESCE(:ref, paytabs_tran_ref),
                    updated_at = NOW() 
                WHERE id = :id
            ');
            $stmt->execute(['ref' => $tranRef, 'id' => $cartId]);
        }
    }

    $pdo->commit();
    http_response_code(200);
    echo json_encode(['status' => 'success', 'order_id' => $cartId]);
} catch (\Throwable $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
