<?php

namespace App;

class Slugify
{
    public static function generate(string $input): string
    {
        $s = mb_strtolower(trim($input));
        $s = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $s);
        $s = preg_replace('/[\s_\/\\\\]+/u', '-', $s);
        $s = preg_replace('/[^a-z0-9-]+/u', '', $s);
        $s = preg_replace('/-+/u', '-', $s);
        $s = trim($s, '-');
        $s = mb_substr($s, 0, 80);
        if ($s === '' || mb_strlen($s) < 2) {
            return 'item-' . substr(bin2hex(random_bytes(4)), 0, 8);
        }
        return $s;
    }

    public static function ensureUnique(string $table, string $base, ?string $excludeId = null): string
    {
        $pdo = Database::connection();
        $root = self::generate($base);
        $candidate = $root;
        for ($i = 2; $i < 200; $i++) {
            $sql = "SELECT id FROM `{$table}` WHERE slug = :slug LIMIT 1";
            $params = ['slug' => $candidate];
            if ($excludeId) {
                $sql .= " AND id != :id";
                $params['id'] = $excludeId;
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            if (!$stmt->fetch()) {
                return $candidate;
            }
            $candidate = "{$root}-{$i}";
        }
        return $root . '-' . substr(bin2hex(random_bytes(3)), 0, 6);
    }
}
