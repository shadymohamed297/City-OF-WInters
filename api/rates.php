<?php

use App\Response;

require_once __DIR__ . '/../vendor/autoload.php';

// --- Configuration ---
$apiKey    = getenv('EXCHANGERATE_API_KEY') ?: '';
$cacheFile = sys_get_temp_dir() . '/cw_exchange_rates.json';
$cacheTTL  = 3600; // 1 hour

// The currencies we care about (subset of what the frontend uses)
$needed = [
    'EGP','USD','EUR','RUB','TRY','GBP',
    'AED','SAR','KWD','QAR','BHD','OMR',
    'JOD','MAD','DZD','TND','LYD','IQD','LBP',
    'JPY','CNY','BRL','MXN','INR','NGN','KES',
    'CAD','AUD',
];

// --- Hardcoded fallback (used if API call fails AND no cache exists) ---
$fallback = [
    'EGP' => 1,
    'USD' => 0.0196,
    'EUR' => 0.019,
    'RUB' => 1.65,
    'TRY' => 0.68,
    'GBP' => 0.016,
    'AED' => 0.072,
    'SAR' => 0.0735,
    'KWD' => 0.006,
    'QAR' => 0.0714,
    'BHD' => 0.00737,
    'OMR' => 0.00754,
    'JOD' => 0.0139,
    'MAD' => 0.186,
    'DZD' => 2.608,
    'TND' => 0.059,
    'LYD' => 0.0931,
    'IQD' => 25.65,
    'LBP' => 1752,
    'JPY' => 2.71,
    'CNY' => 0.1365,
    'BRL' => 0.1005,
    'MXN' => 0.374,
    'INR' => 1.859,
    'NGN' => 31.3,
    'KES' => 2.528,
    'CAD' => 0.027,
    'AUD' => 0.0272,
];

// --- Try to read from cache first ---
$cached = null;
if (file_exists($cacheFile)) {
    $raw = @file_get_contents($cacheFile);
    if ($raw) {
        $cached = json_decode($raw, true);
    }
}

// Cache is valid?
if ($cached && isset($cached['ts']) && (time() - $cached['ts']) < $cacheTTL) {
    Response::ok([
        'rates'     => $cached['rates'],
        'updatedAt' => $cached['ts'],
        'source'    => 'cache',
    ]);
}

// --- Fetch fresh rates from ExchangeRate-API ---
$rates  = null;
$source = 'fallback';

if ($apiKey) {
    $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/EGP";
    $ctx = stream_context_create(['http' => ['timeout' => 5]]);
    $raw = @file_get_contents($url, false, $ctx);

    if ($raw) {
        $data = json_decode($raw, true);
        if (!empty($data['result']) && $data['result'] === 'success' && !empty($data['conversion_rates'])) {
            $all = $data['conversion_rates'];
            $rates = [];
            foreach ($needed as $code) {
                if (isset($all[$code])) {
                    $rates[$code] = $all[$code];
                }
            }
            $source = 'api';
        }
    }
}

// If API failed, try stale cache, then fallback
if (!$rates) {
    if ($cached && !empty($cached['rates'])) {
        $rates  = $cached['rates'];
        $source = 'stale-cache';
    } else {
        $rates  = $fallback;
        $source = 'fallback';
    }
}

// --- Write to cache (only if we got fresh data) ---
if ($source === 'api') {
    $payload = json_encode(['rates' => $rates, 'ts' => time()], JSON_UNESCAPED_UNICODE);
    @file_put_contents($cacheFile, $payload, LOCK_EX);
}

$updatedAt = ($source === 'stale-cache' && $cached) ? $cached['ts'] : time();

Response::ok([
    'rates'     => $rates,
    'updatedAt' => $updatedAt,
    'source'    => $source,
]);
