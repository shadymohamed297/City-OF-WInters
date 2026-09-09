<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

$pdo = Database::connection();
$stmt = $pdo->prepare('SELECT * FROM site_settings WHERE id = 1 LIMIT 1');
$stmt->execute();
$settings = $stmt->fetch();

if (!$settings) {
    Response::ok(['settings' => null]);
}

Response::ok(['settings' => $settings]);
