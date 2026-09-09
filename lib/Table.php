<?php

namespace App;

use PDO;

class Table
{
    private PDO $pdo;
    private string $table;

    public function __construct(string $table, PDO $pdo)
    {
        $this->table = $table;
        $this->pdo = $pdo;
    }

    public function all(string $columns = '*'): array
    {
        $stmt = $this->pdo->prepare("SELECT {$columns} FROM `{$this->table}`");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(string $column, $value): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `{$this->table}` WHERE `{$column}` = :value LIMIT 1");
        $stmt->execute(['value' => $value]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function where(array $conditions, string $columns = '*'): array
    {
        $sql = "SELECT {$columns} FROM `{$this->table}` WHERE ";
        $parts = [];
        $params = [];
        foreach ($conditions as $col => $val) {
            $parts[] = "`{$col}` = :{$col}";
            $params[$col] = $val;
        }
        $sql .= implode(' AND ', $parts);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function insert(array $data): string
    {
        $keys = array_keys($data);
        $fields = '`' . implode('`, `', $keys) . '`';
        $placeholders = ':' . implode(', :', $keys);
        $sql = "INSERT INTO `{$this->table}` ({$fields}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $id = $this->pdo->lastInsertId();
        // If the table uses UUID CHAR(36) PK, lastInsertId returns empty or '0'
        // In that case, return the id from the data if present
        if ($id === '0' || $id === '') {
            return $data['id'] ?? '';
        }
        return $id;
    }

    public function update(string $idColumn, $idValue, array $data): int
    {
        $sets = [];
        $params = [$idColumn => $idValue];
        foreach ($data as $key => $val) {
            $sets[] = "`{$key}` = :{$key}";
            $params[$key] = $val;
        }
        $sql = "UPDATE `{$this->table}` SET " . implode(', ', $sets) . " WHERE `{$idColumn}` = :{$idColumn}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function delete(string $idColumn, $idValue): int
    {
        $stmt = $this->pdo->prepare("DELETE FROM `{$this->table}` WHERE `{$idColumn}` = :id");
        $stmt->execute(['id' => $idValue]);
        return $stmt->rowCount();
    }

    public function count(string $column, $value): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `{$this->table}` WHERE `{$column}` = :value");
        $stmt->execute(['value' => $value]);
        return (int) $stmt->fetchColumn();
    }
}
