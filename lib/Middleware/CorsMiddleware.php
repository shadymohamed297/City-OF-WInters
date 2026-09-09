<?php

namespace App\Middleware;

class CorsMiddleware
{
    private static function allowedOrigins(): array
    {
        $raw = getenv('CORS_ALLOWED_ORIGINS') ?: '';
        $list = array_filter(array_map('trim', explode(',', $raw)));

        // Always trust the site's own origin (same host the request
        // arrived on), so the API keeps working even if
        // CORS_ALLOWED_ORIGINS in .env is out of date (e.g. after
        // moving to a new domain).
        if (!empty($_SERVER['HTTP_HOST'])) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $list[] = $scheme . '://' . $_SERVER['HTTP_HOST'];
        }

        return array_values(array_unique($list));
    }

    public static function applyHeaders(): void
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $allowed = self::allowedOrigins();

        if ($origin !== '' && in_array($origin, $allowed, true)) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Vary: Origin');
            header('Access-Control-Allow-Credentials: true');
        }

        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token');
    }

    public static function handle(): void
    {
        self::applyHeaders();
        http_response_code(204);
        exit;
    }
}
