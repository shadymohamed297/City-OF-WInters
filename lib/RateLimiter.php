<?php

namespace App;

class RateLimiter
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function check(string $key, int $maxAttempts, int $windowSeconds): bool
    {
        $now = time();
        $stmt = $this->pdo->prepare('SELECT attempts, reset_at FROM rate_limits WHERE `key` = :key');
        $stmt->execute(['key' => $key]);
        $row = $stmt->fetch();

        if (!$row || $now > (int) $row['reset_at']) {
            $this->upsert($key, 1, $now + $windowSeconds);
            return true;
        }

        $attempts = (int) $row['attempts'] + 1;
        if ($attempts > $maxAttempts) {
            Response::error('RATE_LIMIT_EXCEEDED', 'Too many requests', 429);
        }

        $this->upsert($key, $attempts, (int) $row['reset_at']);
        return true;
    }

    private function upsert(string $key, int $attempts, int $resetAt): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO rate_limits (`key`, attempts, reset_at) VALUES (:key, :attempts, :reset_at) ON DUPLICATE KEY UPDATE attempts = :attempts, reset_at = :reset_at');
        $stmt->execute([
            'key' => $key,
            'attempts' => $attempts,
            'reset_at' => $resetAt,
        ]);
    }
}
