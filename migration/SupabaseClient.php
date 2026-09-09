<?php

namespace App\Migration;

use App\Database;
use PDO;

class SupabaseClient
{
    private string $url;
    private string $key;

    public function __construct()
    {
        $this->url = rtrim(getenv('SUPABASE_URL') ?: '', '/');
        $this->key = getenv('SUPABASE_SECRET_KEY') ?: getenv('SUPABASE_PUBLISHABLE_KEY') ?: '';

        if (!$this->url || !$this->key) {
            throw new \RuntimeException('Missing SUPABASE_URL or SUPABASE_SECRET_KEY in .env');
        }
    }

    public function fetchAll(string $table, array $params = []): array
    {
        $url = $this->url . '/rest/v1/' . $table;
        if (!empty($params)) {
            $qs = http_build_query($params);
            $url .= '?' . $qs;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'apikey: ' . $this->key,
                'Authorization: Bearer ' . $this->key,
                'Content-Type: application/json',
                'Prefer: return=representation',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \RuntimeException("Failed to fetch {$table}: HTTP {$httpCode}");
        }

        $data = json_decode($response, true);
        return is_array($data) ? $data : [];
    }

    public function count(string $table): int
    {
        $url = $this->url . '/rest/v1/' . $table;
        $url .= '?select=count';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'apikey: ' . $this->key,
                'Authorization: Bearer ' . $this->key,
                'Content-Type: application/json',
                'Prefer: count=exact',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return 0;
        }

        $data = json_decode($response, true);
        return isset($data[0]['count']) ? (int) $data[0]['count'] : 0;
    }
}
