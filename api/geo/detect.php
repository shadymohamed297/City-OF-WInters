<?php

use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

// Detect visitor country to determine currency:
// - Egypt (EG) -> EGP (ج.م)
// - Outside Egypt -> USD ($)

$ip = $_SERVER['HTTP_CF_CONNECTING_IP']
    ?? $_SERVER['HTTP_X_FORWARDED_FOR']
    ?? $_SERVER['REMOTE_ADDR']
    ?? null;

if ($ip && str_contains($ip, ',')) {
    $ip = trim(explode(',', $ip)[0]);
}

$country = 'EG';
$countryName = 'Egypt';

// Skip lookups for local/private IPs (dev, local docker, etc.)
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
            $country = strtoupper(trim($data['countryCode']));
            $countryName = $data['country'] ?? $country;
        }
    }
}

$isEgypt = ($country === 'EG');

if ($isEgypt) {
    $region          = 'egypt';
    $currency        = 'EGP';
    $frontendCountry = 'EG';
} else {
    $region          = 'international';
    $currency        = 'USD';
    // Mapping any country outside Egypt to 'US' ensures the frontend's Cz map
    // resolves to USD ($) regardless of which foreign country the IP belongs to.
    $frontendCountry = 'US';
}

$payload = [
    'country_code'     => $frontendCountry,
    'country_name'     => $countryName,
    'detected_country' => $country,
    'region'           => $region,
    'currency'         => $currency,
];

// CRITICAL: The frontend in assets/index-Dmn91ErK.js accesses `u?.data?.country_code`
// on the response object `u`. Because the HTTP helper `eu` unwraps `response.data`,
// nesting $payload inside `data` ensures `u.data.country_code` is defined ('US')
// while direct access `u.country_code` also works.
Response::ok(array_merge($payload, [
    'data' => $payload,
]));
