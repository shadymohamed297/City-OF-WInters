<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;

require_once __DIR__ . '/../../../vendor/autoload.php';

Csrf::middleware();
$auth = new App\Auth(Database::connection());
$auth->requireAdmin();

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$allowed = [
    'logo_url', 'favicon_url', 'site_name_ar', 'site_name_en',
    'tagline_ar', 'tagline_en', 'meta_description_ar', 'meta_description_en',
    'hero_title_ar', 'hero_title_en', 'hero_subtitle_ar', 'hero_subtitle_en',
    'social_facebook', 'social_instagram', 'social_twitter', 'social_tiktok',
    'social_youtube', 'social_whatsapp', 'contact_phone', 'contact_email',
    'contact_address_ar', 'contact_address_en', 'footer_about_ar', 'footer_about_en',
    'privacy_policy_ar', 'privacy_policy_en', 'terms_ar', 'terms_en',
    'refund_policy_ar', 'refund_policy_en', 'shipping_policy_ar', 'shipping_policy_en',
    'about_ar', 'about_en',
];

$update = [];
foreach ($allowed as $field) {
    if (isset($input[$field])) {
        $update[$field] = $input[$field];
    }
}

if (isset($input['hero_images']) && is_array($input['hero_images'])) {
    $update['hero_images'] = json_encode($input['hero_images'], JSON_UNESCAPED_UNICODE);
}
if (isset($input['custom_strings']) && is_array($input['custom_strings'])) {
    $update['custom_strings'] = json_encode($input['custom_strings'], JSON_UNESCAPED_UNICODE);
}

if (!empty($update)) {
    $update['updated_at'] = date('Y-m-d H:i:s');
    $set = [];
    foreach (array_keys($update) as $k) {
        $set[] = "`{$k}` = :{$k}";
    }
    $sql = 'UPDATE site_settings SET ' . implode(', ', $set) . ' WHERE id = 1';
    $stmt = Database::connection()->prepare($sql);
    $stmt->execute($update);
}

Response::ok(['message' => 'Settings updated']);
