<?php

namespace App;

class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validate(?string $token): bool
    {
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

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $token = null;

        if (stripos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true);
            $token = $input['csrf_token'] ?? null;
        } else {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        }

        if (!self::validate($token)) {
            Response::error('CSRF_INVALID', 'Invalid CSRF token', 419);
        }
    }
}
