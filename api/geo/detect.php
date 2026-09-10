<?php

use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

// Detect visitor country to determine currency:
// - Egypt (EG) -> EGP (ج.م)
// - Outside Egypt -> USD ($)

function getClientPublicIp(): ?string
{
    $headers = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_REAL_IP',
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR',
    ];

    foreach ($headers as $header) {
        if (empty($_SERVER[$header])) {
            continue;
        }

        $ips = explode(',', (string) $_SERVER[$header]);
        foreach ($ips as $candidate) {
            $candidate = trim($candidate);
            if (filter_var($candidate, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $candidate;
            }
        }
    }

    return $_SERVER['REMOTE_ADDR'] ?? null;
}

$country = null;
$countryName = null;

// 1. Direct Cloudflare / CDN country header (fastest & 100% accurate if behind Cloudflare/Hostinger CDN)
if (!empty($_SERVER['HTTP_CF_IPCOUNTRY']) && preg_match('/^[A-Z]{2}$/i', $_SERVER['HTTP_CF_IPCOUNTRY'])) {
    $cfCountry = strtoupper(trim($_SERVER['HTTP_CF_IPCOUNTRY']));
    if ($cfCountry !== 'XX' && $cfCountry !== 'T1') {
        $country = $cfCountry;
    }
}

$ip = getClientPublicIp();
$isPublicIp = $ip && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);

// 2. Check local file cache for this IP (TTL = 24 hours)
$cacheFile = null;
if (!$country && $isPublicIp) {
    $cacheFile = sys_get_temp_dir() . '/cw_geo_' . md5($ip) . '.json';
    if (file_exists($cacheFile)) {
        $cacheRaw = @file_get_contents($cacheFile);
        if ($cacheRaw) {
            $cached = json_decode($cacheRaw, true);
            if (!empty($cached['country']) && isset($cached['ts']) && (time() - $cached['ts'] < 86400)) {
                $country = $cached['country'];
                $countryName = $cached['country_name'] ?? null;
            }
        }
    }
}

// 3. Query Geo-IP services over HTTPS if not yet identified
if (!$country && $isPublicIp) {
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 3,
            'header'  => "User-Agent: CityOfWriters/1.0\r\n",
        ],
        'ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false,
        ],
    ]);

    // Service A: api.country.is (HTTPS, fast, unlimited free)
    $res = @file_get_contents("https://api.country.is/{$ip}", false, $ctx);
    if ($res) {
        $json = json_decode($res, true);
        if (!empty($json['country']) && preg_match('/^[A-Z]{2}$/i', $json['country'])) {
            $country = strtoupper(trim($json['country']));
        }
    }

    // Service B: ipwho.is (HTTPS, detailed info)
    if (!$country) {
        $res = @file_get_contents("https://ipwho.is/{$ip}", false, $ctx);
        if ($res) {
            $json = json_decode($res, true);
            if (!empty($json['success']) && !empty($json['country_code'])) {
                $country = strtoupper(trim($json['country_code']));
                $countryName = $json['country'] ?? null;
            }
        }
    }

    // Service C: ip-api.com (fallback)
    if (!$country) {
        $res = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,countryCode,country", false, $ctx);
        if ($res) {
            $json = json_decode($res, true);
            if (!empty($json['status']) && $json['status'] === 'success' && !empty($json['countryCode'])) {
                $country = strtoupper(trim($json['countryCode']));
                $countryName = $json['country'] ?? null;
            }
        }
    }

    // Save successful lookup to cache
    if ($country && $cacheFile) {
        @file_put_contents($cacheFile, json_encode([
            'country'      => $country,
            'country_name' => $countryName,
            'ts'           => time(),
        ]), LOCK_EX);
    }
}

// Default fallback to Egypt only if detection completely failed or visitor is actually in Egypt
$country = $country ?: 'EG';
$countryName = $countryName ?: ($country === 'EG' ? 'Egypt' : $country);

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
    'ip'               => $ip,
    'region'           => $region,
    'currency'         => $currency,
];

// Provide both direct keys and nested 'data' key for frontend compatibility
Response::ok(array_merge($payload, [
    'data' => $payload,
]));
