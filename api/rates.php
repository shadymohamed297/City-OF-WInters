<?php

use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

$rates = [
    'EGP' => 1,
    'USD' => 0.0205,
    'EUR' => 0.019,
    'RUB' => 1.65,
    'TRY' => 0.68,
    'GBP' => 0.016,
    'AED' => 0.075,
    'SAR' => 0.077,
    'KWD' => 0.0063,
    'QAR' => 0.074,
    'BHD' => 0.0077,
    'OMR' => 0.0079,
    'JOD' => 0.0145,
    'MAD' => 0.20,
    'DZD' => 2.75,
    'TND' => 0.063,
    'LYD' => 0.096,
    'IQD' => 26.5,
    'LBP' => 305,
    'JPY' => 3.1,
    'CNY' => 0.15,
    'BRL' => 0.10,
    'MXN' => 0.34,
    'INR' => 1.75,
    'NGN' => 8.3,
    'KES' => 2.65,
    'CAD' => 0.028,
    'AUD' => 0.032,
];

Response::ok([
    'rates' => $rates,
    'updatedAt' => time(),
    'source' => 'api',
]);
