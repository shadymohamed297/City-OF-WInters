<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$pdo = Database::connection();
$baseUrl = getenv('APP_URL') ?: 'https://www.madinatalodabaa.com';

$entries = [];

// Static routes
$staticRoutes = [
    '/' => ['changefreq' => 'daily', 'priority' => '1.0'],
    '/shop' => ['changefreq' => 'daily', 'priority' => '0.9'],
    '/categories' => ['changefreq' => 'weekly', 'priority' => '0.8'],
    '/about' => ['changefreq' => 'monthly', 'priority' => '0.5'],
    '/contact' => ['changefreq' => 'monthly', 'priority' => '0.5'],
    '/shipping' => ['changefreq' => 'monthly', 'priority' => '0.4'],
    '/returns' => ['changefreq' => 'monthly', 'priority' => '0.4'],
    '/privacy' => ['changefreq' => 'yearly', 'priority' => '0.3'],
    '/terms' => ['changefreq' => 'yearly', 'priority' => '0.3'],
];
foreach ($staticRoutes as $path => $meta) {
    $entries[] = [
        'loc' => $baseUrl . $path,
        'changefreq' => $meta['changefreq'],
        'priority' => $meta['priority'],
    ];
}

// Products
$stmt = $pdo->prepare('SELECT slug, updated_at FROM products WHERE is_active = 1 LIMIT 5000');
$stmt->execute();
$products = $stmt->fetchAll();
foreach ($products as $p) {
    if (!$p['slug']) continue;
    $entries[] = [
        'loc' => $baseUrl . '/product/' . rawurlencode($p['slug']),
        'lastmod' => $p['updated_at'] ? date('Y-m-d', strtotime($p['updated_at'])) : null,
        'changefreq' => 'weekly',
        'priority' => '0.7',
    ];
}

// Categories
$stmt = $pdo->prepare('SELECT slug, updated_at FROM categories WHERE is_active = 1 LIMIT 500');
$stmt->execute();
$categories = $stmt->fetchAll();
foreach ($categories as $c) {
    if (!$c['slug']) continue;
    $entries[] = [
        'loc' => $baseUrl . '/shop?category=' . rawurlencode($c['slug']),
        'lastmod' => $c['updated_at'] ? date('Y-m-d', strtotime($c['updated_at'])) : null,
        'changefreq' => 'weekly',
        'priority' => '0.6',
    ];
}

$urls = [];
foreach ($entries as $e) {
    $urls[] = "  <url>\n" .
        "    <loc>" . htmlspecialchars($e['loc'], ENT_XML1) . "</loc>\n" .
        ($e['lastmod'] ? "    <lastmod>" . htmlspecialchars($e['lastmod'], ENT_XML1) . "</lastmod>\n" : "") .
        ($e['changefreq'] ? "    <changefreq>" . htmlspecialchars($e['changefreq'], ENT_XML1) . "</changefreq>\n" : "") .
        ($e['priority'] ? "    <priority>" . htmlspecialchars($e['priority'], ENT_XML1) . "</priority>\n" : "") .
        "  </url>";
}

$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n" . implode("\n", $urls) . "\n</urlset>";

header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=3600');
echo $xml;
