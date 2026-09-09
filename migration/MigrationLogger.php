<?php

namespace App\Migration;

use App\Database;
use PDO;

class MigrationLogger
{
    private array $logs = [];
    private string $reportDir;

    public function __construct()
    {
        $this->reportDir = __DIR__ . '/../reports/';
        if (!is_dir($this->reportDir)) {
            mkdir($this->reportDir, 0755, true);
        }
    }

    public function log(string $table, string $status, string $message = ''): void
    {
        $this->logs[] = [
            'time' => date('Y-m-d H:i:s'),
            'table' => $table,
            'status' => $status,
            'message' => $message,
        ];
    }

    public function info(string $msg): void
    {
        echo "[INFO] {$msg}\n";
    }

    public function error(string $msg): void
    {
        echo "[ERROR] {$msg}\n";
    }

    public function saveReport(): void
    {
        $timestamp = date('Y-m-d-Hi');
        $jsonFile = $this->reportDir . "migration-{$timestamp}.json";
        $txtFile = $this->reportDir . "migration-{$timestamp}.txt";

        file_put_contents($jsonFile, json_encode($this->logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $txt = "City of Writers Migration Report\n";
        $txt .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $txt .= str_pad('TABLE', 20) . str_pad('STATUS', 10) . "MESSAGE\n";
        $txt .= str_repeat('-', 70) . "\n";
        foreach ($this->logs as $log) {
            $txt .= str_pad($log['table'], 20) . str_pad($log['status'], 10) . $log['message'] . "\n";
        }
        file_put_contents($txtFile, $txt);
    }
}
