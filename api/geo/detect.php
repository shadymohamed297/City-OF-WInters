<?php

use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

// Very small country/region detection so the storefront can default to
// EGP vs Gulf pricing / shipping without a paid geo-IP service.
// Falls back safely to Egypt if anything is unavailable.

$ip = $_SERVER['HTTP_CF_CONNECTING_IP']
    ?? $_SERVER['HTTP_X_FORWARDED_FOR']
    ?? $_SERVER['REMOTE_ADDR']
    ?? null;

if ($ip && str_contains($ip, ',')) {
    $ip = trim(explode(',', $ip)[0]);
}

$country = 'EG';
$countryName = 'Egypt';

// Skip lookups for local/private IPs (dev, or behind certain proxies)
$isPrivate = $ip && !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);

if ($ip && !$isPrivate) {
    $ctx = stream_context_create(['http' => ['timeout' => 2]]);
    $response = @file_get_contents(
        "http://ip-api.com/json/{$ip}?fields=status,countryCode,country",
        false,
        $ctx
    );

    if ($response) {
        $data = json_decode($response, true);
        if (!empty($data['status']) && $data['status'] === 'success' && !empty($data['countryCode'])) {
            $country = $data['countryCode'];
            $countryName = $data['country'] ?? $country;
        }
    }
}

$gulfCountries = ['SA', 'AE', 'KW', 'QA', 'BH', 'OM'];

$gulfCurrencyMap = [
    'SA' => 'SAR', 'AE' => 'AED', 'KW' => 'KWD',
    'QA' => 'QAR', 'BH' => 'BHD', 'OM' => 'OMR',
];

if ($country === 'EG') {
    $region   = 'egypt';
    $currency = 'EGP';
} elseif (in_array($country, $gulfCountries, true)) {
    $region   = 'gulf';
    $currency = $gulfCurrencyMap[$country] ?? 'USD';
} else {
    $region   = 'international';
    $currency = 'USD';
}

Response::ok([
    'country_code' => $country,
    'country_name' => $countryName,
    'region'       => $region,
    'currency'     => $currency,
]);
