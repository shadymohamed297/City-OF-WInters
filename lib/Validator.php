<?php

namespace App;

class Validator
{
    public static function string(mixed $value, int $max = 255, ?string $label = null): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            Response::validationError(($label ?? 'Field') . ' is required');
        }
        if (mb_strlen($value) > $max) {
            Response::validationError(($label ?? 'Field') . ' must not exceed ' . $max . ' characters');
        }
        return $value;
    }

    public static function stringOrNull(mixed $value, int $max = 255, ?string $label = null): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        return self::string($value, $max, $label);
    }

    public static function int(mixed $value, ?int $min = null, ?int $max = null, ?string $label = null): int
    {
        $int = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min ?? PHP_INT_MIN, 'max_range' => $max ?? PHP_INT_MAX]]);
        if ($int === false) {
            Response::validationError(($label ?? 'Field') . ' must be a valid integer');
        }
        return $int;
    }

    public static function number(mixed $value, ?float $min = null, ?float $max = null, ?string $label = null): float
    {
        $num = filter_var($value, FILTER_VALIDATE_FLOAT);
        if ($num === false) {
            Response::validationError(($label ?? 'Field') . ' must be a valid number');
        }
        if ($min !== null && $num < $min) {
            Response::validationError(($label ?? 'Field') . ' must be at least ' . $min);
        }
        if ($max !== null && $num > $max) {
            Response::validationError(($label ?? 'Field') . ' must not exceed ' . $max);
        }
        return $num;
    }

    public static function bool(mixed $value, ?string $label = null): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
    }

    public static function email(string $value, int $max = 255): string
    {
        $value = trim($value);
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            Response::validationError('Invalid email address');
        }
        if (mb_strlen($value) > $max) {
            Response::validationError('Email must not exceed ' . $max . ' characters');
        }
        return strtolower($value);
    }

    public static function uuid(string $value): string
    {
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value)) {
            Response::validationError('Invalid UUID');
        }
        return $value;
    }

    public static function enum(string $value, array $allowed, ?string $label = null): string
    {
        if (!in_array($value, $allowed, true)) {
            Response::validationError(($label ?? 'Field') . ' must be one of: ' . implode(', ', $allowed));
        }
        return $value;
    }
}
