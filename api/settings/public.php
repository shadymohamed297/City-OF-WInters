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

if (empty($settings['contact_email']) || str_contains($settings['contact_email'], 'almatasawilein.com') || $settings['site_name_en'] === 'Al-Motasawelin') {
    try {
        $upd = $pdo->prepare("UPDATE site_settings SET contact_email = 'info@madinetalodabaa.com', site_name_en = 'Madinat Al-Odabaa' WHERE id = 1");
        $upd->execute();
        $settings['contact_email'] = 'info@madinetalodabaa.com';
        $settings['site_name_en'] = 'Madinat Al-Odabaa';
    } catch (\Throwable $e) {
        // Fallback in case table schema constraint
        $settings['contact_email'] = 'info@madinetalodabaa.com';
        $settings['site_name_en'] = 'Madinat Al-Odabaa';
    }
}

Response::ok(['settings' => $settings]);
