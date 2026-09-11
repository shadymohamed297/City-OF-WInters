<?php

namespace App;

class Csrf
{
    private static function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $sessionName = getenv('SESSION_NAME') ?: 'cw_session';
            session_name($sessionName);
            $lifetime = (int) (getenv('SESSION_LIFETIME') ?: 7200);
            $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => '/',
                'domain' => getenv('COOKIE_DOMAIN') ?: '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public static function token(): string
    {
        self::initSession();
        if (empty($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validate(?string $token): bool
    {
        self::initSession();
        if (!$token || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function middleware(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $safeMethods = ['GET', 'HEAD', 'OPTIONS'];
        if (in_array($method, $safeMethods, true)) {
            return;
        }

        self::initSession();

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!$token && stripos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true);
            $token = $input['csrf_token'] ?? null;
        } elseif (!$token) {
            $token = $_POST['csrf_token'] ?? null;
        }

        if (!self::validate($token)) {
            Response::error('CSRF_INVALID', 'Invalid CSRF token', 419);
        }
    }
}
