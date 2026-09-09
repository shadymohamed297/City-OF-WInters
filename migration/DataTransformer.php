<?php

namespace App\Migration;

use App\Database;
use PDO;

class DataTransformer
{
    public static function transformRow(array $row, string $table): array
    {
        $transformed = [];

        foreach ($row as $key => $value) {
            if ($value instanceof \DateTime) {
                $transformed[$key] = $value->format('Y-m-d H:i:s');
            } elseif (is_array($value) || is_object($value)) {
                $transformed[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
            } else {
                $transformed[$key] = $value;
            }
        }

        return $transformed;
    }

    public static function uuidToString($value): string
    {
        if (is_string($value)) {
            return $value;
        }
        return (string) $value;
    }
}
