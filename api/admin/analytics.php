<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;

require_once __DIR__ . '/../../../vendor/autoload.php';

Csrf::middleware();
$auth = new App\Auth(Database::connection());
$auth->requireAdmin();

$pdo = Database::connection();
$from = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$to = $_GET['to'] ?? date('Y-m-d');

$stmt = $pdo->prepare('SELECT id, order_number, created_at, status, total, subtotal, shipping_cost, discount, payment_method, guest_name, guest_phone, shipping_address FROM orders WHERE created_at BETWEEN :from AND :to ORDER BY created_at DESC');
$stmt->execute(['from' => $from . ' 00:00:00', 'to' => $to . ' 23:59:59']);
$orders = $stmt->fetchAll();

// Revenue by day
$dayMap = [];
for ($t = strtotime($from); $t <= strtotime($to); $t += 86400) {
    $d = date('Y-m-d', $t);
    $dayMap[$d] = ['sales' => 0, 'orders' => 0];
}
foreach ($orders as $o) {
    $d = date('Y-m-d', strtotime($o['created_at']));
    if (!isset($dayMap[$d])) $dayMap[$d] = ['sales' => 0, 'orders' => 0];
    $dayMap[$d]['sales'] += (float)$o['total'];
    $dayMap[$d]['orders'] += 1;
}
$salesByDay = [];
foreach ($dayMap as $d => $v) {
    $salesByDay[] = ['date' => $d, ...$v];
}

// KPIs
$totalRevenue = array_sum(array_column($orders, 'total'));
$totalOrders = count($orders);
$avgOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

// Status breakdown
$statusMap = [];
foreach ($orders as $o) {
    $statusMap[$o['status']] = ($statusMap[$o['status']] ?? 0) + 1;
}
$statusData = [];
foreach ($statusMap as $s => $v) {
    $statusData[] = ['status' => $s, 'value' => $v];
}

// Top products from order_items
$stmt = $pdo->prepare('SELECT oi.product_title_ar, oi.product_title_en, SUM(oi.quantity) as units, SUM(oi.line_total) as revenue FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.created_at BETWEEN :from AND :to GROUP BY oi.product_id ORDER BY revenue DESC LIMIT 5');
$stmt->execute(['from' => $from . ' 00:00:00', 'to' => $to . ' 23:59:59']);
$topProducts = $stmt->fetchAll();

// Top cities
$cityMap = [];
foreach ($orders as $o) {
    $addr = json_decode($o['shipping_address'], true) ?: [];
    $city = $addr['governorate'] ?? $addr['city'] ?? '—';
    if (!isset($cityMap[$city])) $cityMap[$city] = ['revenue' => 0, 'orders' => 0];
    $cityMap[$city]['revenue'] += (float)$o['total'];
    $cityMap[$city]['orders'] += 1;
}
$topCities = [];
foreach ($cityMap as $c => $v) {
    $topCities[] = ['city' => $c, ...$v];
}
usort($topCities, fn($a, $b) => $b['revenue'] <=> $a['revenue']);
$topCities = array_slice($topCities, 0, 5);

// Payment methods
$payMap = [];
foreach ($orders as $o) {
    $payMap[$o['payment_method']] = ($payMap[$o['payment_method']] ?? 0) + (float)$o['total'];
}
$paymentData = [];
foreach ($payMap as $m => $v) {
    $paymentData[] = ['method' => $m, 'value' => round($v)];
}

// Hourly
$hourMap = array_fill(0, 24, 0);
foreach ($orders as $o) {
    $h = (int)date('H', strtotime($o['created_at']));
    $hourMap[$h]++;
}
$hourly = [];
for ($h = 0; $h < 24; $h++) {
    $hourly[] = ['hour' => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00', 'orders' => $hourMap[$h]];
}

$recentOrders = array_slice($orders, 0, 6);

// Products stats
$stmt = $pdo->query('SELECT COUNT(*) as total, SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock FROM products');
$productStats = $stmt->fetch();
$totalProducts = (int)($productStats['total'] ?? 0);
$outOfStock = (int)($productStats['out_of_stock'] ?? 0);

Response::ok([
    'from' => $from,
    'to' => $to,
    'salesByDay' => $salesByDay,
    'totalRevenue' => $totalRevenue,
    'totalOrders' => $totalOrders,
    'avgOrder' => $avgOrder,
    'totalCustomers' => 0,
    'statusData' => $statusData,
    'topProducts' => $topProducts,
    'topCities' => $topCities,
    'paymentData' => $paymentData,
    'hourly' => $hourly,
    'recentOrders' => $recentOrders,
    'totalProducts' => $totalProducts,
    'outOfStock' => $outOfStock,
]);
