<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

$pdo = Database::connection();
$stmt = $pdo->prepare('SELECT governorate_ar, governorate_en, price, enabled FROM shipping_rates ORDER BY governorate_ar ASC');
$stmt->execute();
$rates = $stmt->fetchAll();

Response::ok(['rates' => $rates]);
