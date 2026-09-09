<?php

namespace App\Migration;

use App\Database;
use PDO;

class Migrator
{
    private PDO $target;
    private MigrationLogger $logger;
    private SupabaseClient $source;

    private array $order = [
        'users',
        'profiles',
        'user_roles',
        'categories',
        'products',
        'product_categories',
        'site_settings',
        'shipping_rates',
        'coupons',
        'addresses',
        'orders',
        'order_items',
        'reviews',
        'wishlist',
        'marketing_costs',
    ];

    public function __construct(SupabaseClient $source, PDO $target, MigrationLogger $logger)
    {
        $this->source = $source;
        $this->target = $target;
        $this->logger = $logger;
    }

    public function run(): void
    {
        $this->logger->info('Starting migration...');

        foreach ($this->order as $table) {
            $this->migrateTable($table);
        }

        $this->logger->info('Migration completed.');
        $this->logger->saveReport();
    }

    private function migrateTable(string $table): void
    {
        try {
            $sourceCount = $this->source->count($table);
            $this->logger->info("Migrating {$table}: {$sourceCount} rows");

            if ($sourceCount === 0) {
                $this->logger->log($table, 'SKIPPED', 'No rows in source');
                return;
            }

            $rows = $this->source->fetchAll($table);
            if (empty($rows)) {
                $this->logger->log($table, 'SKIPPED', 'No rows fetched');
                return;
            }

            $migrated = 0;
            foreach ($rows as $row) {
                try {
                    $this->upsertRow($table, $row);
                    $migrated++;
                } catch (\Throwable $e) {
                    $this->logger->log($table, 'ERROR', $e->getMessage());
                }
            }

            $targetCount = $this->getTargetCount($table);
            $status = $targetCount === $sourceCount ? 'OK' : 'MISMATCH';
            $this->logger->log($table, $status, "Source: {$sourceCount}, Target: {$targetCount}");
        } catch (\Throwable $e) {
            $this->logger->log($table, 'ERROR', $e->getMessage());
        }
    }

    private function getTargetCount(string $table): int
    {
        $stmt = $this->target->prepare("SELECT COUNT(*) FROM {$table}");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    private function upsertRow(string $table, array $row): void
    {
        $columns = array_keys($row);
        $placeholders = array_map(fn($c) => ":{$c}", $columns);
        $update = array_map(fn($c) => "`{$c}` = :{$c}", $columns);

        $sql = "INSERT INTO {$table} (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $placeholders) . ")";
        $sql .= " ON DUPLICATE KEY UPDATE " . implode(', ', $update);

        $stmt = $this->target->prepare($sql);
        $stmt->execute($row);
    }
}
