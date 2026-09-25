<?php

use App\Csrf;
use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

// Ensure visitors have a CSRF token initialized
if (empty($_COOKIE['csrf_token'])) {
    $token = Csrf::token();
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    setcookie('csrf_token', $token, [
        'expires' => time() + 86400 * 30,
        'path' => '/',
        'secure' => $secure,
        'httponly' => false,
        'samesite' => 'Lax',
    ]);
}

$pdo = Database::connection();
$stmt = $pdo->prepare('SELECT * FROM site_settings WHERE id = 1 LIMIT 1');
$stmt->execute();
$settings = $stmt->fetch();

if (!$settings) {
    Response::ok(['settings' => null]);
}

$needsUpdate = false;
$updateFields = [];

if (empty($settings['contact_email']) || str_contains($settings['contact_email'], 'almatasawilein.com') || $settings['site_name_en'] === 'Al-Motasawelin') {
    $settings['contact_email'] = 'info@madinetalodabaa.com';
    $settings['site_name_en'] = 'Madinat Al-Odabaa';
    $updateFields['contact_email'] = 'info@madinetalodabaa.com';
    $updateFields['site_name_en'] = 'Madinat Al-Odabaa';
    $needsUpdate = true;
}

if (empty($settings['logo_url']) || str_contains($settings['logo_url'], 'supabase') || str_contains($settings['logo_url'], 'creativessquare')) {
    $settings['logo_url'] = '/logo.png';
    $updateFields['logo_url'] = '/logo.png';
    $needsUpdate = true;
}

if (empty($settings['favicon_url']) || str_contains($settings['favicon_url'], 'supabase') || str_contains($settings['favicon_url'], 'creativessquare')) {
    $settings['favicon_url'] = '/logo.png';
    $updateFields['favicon_url'] = '/logo.png';
    $needsUpdate = true;
}

if ($needsUpdate && !empty($updateFields)) {
    try {
        $setClauses = [];
        foreach ($updateFields as $col => $val) {
            $setClauses[] = "`$col` = :$col";
        }
        $upd = $pdo->prepare("UPDATE site_settings SET " . implode(', ', $setClauses) . " WHERE id = 1");
        $upd->execute($updateFields);
    } catch (\Throwable $e) {}
}

Response::ok(['settings' => $settings]);

