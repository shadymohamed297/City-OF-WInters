<?php

namespace App;

class Response
{
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function ok(array $data = []): void
    {
        self::json(['ok' => true, 'data' => $data]);
    }

    public static function error(string $code, string $message, int $status = 400): void
    {
        self::json([
            'ok' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ], $status);
    }

    public static function validationError(string $message): void
    {
        self::error('VALIDATION_ERROR', $message, 422);
    }

    public static function unauthorized(string $message = 'Unauthorized'): void
    {
        self::error('UNAUTHORIZED', $message, 401);
    }

    public static function forbidden(string $message = 'Forbidden'): void
    {
        self::error('FORBIDDEN', $message, 403);
    }

    public static function notFound(string $message = 'Not found'): void
    {
        self::error('NOT_FOUND', $message, 404);
    }

    public static function serverError(string $message = 'Server error'): void
    {
        self::error('SERVER_ERROR', $message, 500);
    }
}
