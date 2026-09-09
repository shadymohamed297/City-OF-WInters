<?php
// Simple migration script - inline version without class autoloading issues

// Load .env
function loadEnv(string $path): void {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) continue;
        $name = trim($parts[0]);
        $value = trim($parts[1]);
        if (preg_match('/^"(.*)"$/', $value, $matches)) $value = $matches[1];
        elseif (preg_match("/^'(.*)'$/", $value, $matches)) $value = $matches[1];
        if (!getenv($name)) putenv("$name=$value");
    }
}

loadEnv(__DIR__ . '/.env');

$secret = $_GET['secret'] ?? null;
$isWeb = $secret !== null;

if ($isWeb) {
    $expectedSecret = getenv('MIGRATION_SECRET') ?: 'cc9b20525806a840ecad34ec';
    if ($secret !== $expectedSecret) {
        http_response_code(403);
        echo "<h1>Forbidden</h1>";
        exit;
    }
    header('Content-Type: text/html; charset=utf-8');
}

// Logging
$logs = [];
function logMsg($msg) {
    global $logs;
    $logs[] = date('H:i:s') . ' ' . $msg;
}

// JSON columns per table: values for these columns get json_encode()'d
// before being bound to the SQL statement (MySQL JSON/CHECK columns need
// a real JSON string, not a raw PHP array/bool).
$jsonColumns = [
    'products' => ['images', 'tags', 'metadata', 'attributes'],
    'site_settings' => ['hero_images', 'social_links', 'metadata'],
    'categories' => ['metadata'],
    'orders' => ['shipping_address', 'metadata'],
];

function prepareRowForInsert(array $row, array $jsonCols): array {
    foreach ($row as $col => $val) {
        if (is_array($val)) {
            // Any array value MUST be JSON-encoded, whether or not the
            // column was in our known list, or MySQL's JSON/CHECK
            // constraint will reject it.
            $row[$col] = json_encode($val, JSON_UNESCAPED_UNICODE);
        } elseif (is_bool($val)) {
            $row[$col] = $val ? 1 : 0;
        } elseif (in_array($col, $jsonCols, true) && $val !== null && !is_string($val)) {
            $row[$col] = json_encode($val, JSON_UNESCAPED_UNICODE);
        }
    }
    return $row;
}

// ---------------------------------------------------------------------
// Auto schema creation (only kicks in when a table does NOT already
// exist in MySQL). If the table is already there — like the ones you
// built by hand for this project — this is skipped entirely and your
// existing schema is left untouched; only insert/upsert happens.
// ---------------------------------------------------------------------

function inferColumnType($value): string {
    if ($value === null) return 'TEXT NULL';
    if (is_bool($value)) return 'TINYINT(1) NULL';
    if (is_int($value)) return 'BIGINT NULL';
    if (is_float($value)) return 'DECIMAL(18,4) NULL';
    if (is_array($value)) return 'JSON NULL';
    if (is_string($value)) {
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value)) {
            return 'VARCHAR(36) NULL';
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}([T ]\d{2}:\d{2}:\d{2})?/', $value)) {
            return 'DATETIME NULL';
        }
        if (strlen($value) > 1000) return 'LONGTEXT NULL';
        return 'TEXT NULL';
    }
    return 'TEXT NULL';
}

/**
 * Makes sure $table exists in MySQL before we try to insert into it.
 * - If it already exists: only adds any brand-new columns found in the
 *   incoming data (never touches existing columns/constraints).
 * - If it does NOT exist: creates it from the shape of $sampleRow. A
 *   surrogate auto-increment `_row_pk` is always the real PRIMARY KEY
 *   (so rows with a null "id" never break the insert), and the source
 *   "id" column (if present) becomes a UNIQUE KEY instead of a PRIMARY
 *   KEY, so it can safely be null for some rows.
 */
function ensureTableSchema(PDO $pdo, string $table, array $sampleRow): void {
    $stmt = $pdo->query("SHOW TABLES LIKE " . $pdo->quote($table));
    $tableExists = (bool)$stmt->fetch();

    if (!$tableExists) {
        $cols = [];
        foreach ($sampleRow as $col => $val) {
            $cols[] = "`{$col}` " . inferColumnType($val);
        }
        array_unshift($cols, "`_row_pk` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY");

        $uniqueSql = '';
        if (array_key_exists('id', $sampleRow)) {
            $uniqueSql = ", UNIQUE KEY `uniq_id` (`id`)";
        }

        $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (" . implode(', ', $cols) . "{$uniqueSql}) "
             . "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $pdo->exec($sql);
        logMsg("  Table `{$table}` did not exist — created it automatically.");
    } else {
        $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
        $existingCols = array_map(fn($r) => $r['Field'], $stmt->fetchAll());
        foreach ($sampleRow as $col => $val) {
            if (!in_array($col, $existingCols, true)) {
                $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$col}` " . inferColumnType($val));
                logMsg("  Table `{$table}` exists — added missing column `{$col}`.");
            }
        }
    }
}

try {
    logMsg('Starting migration...');

    $supabaseUrl = rtrim(getenv('SUPABASE_URL') ?: '', '/');
    $supabaseKey = getenv('SUPABASE_SECRET_KEY') ?: getenv('SUPABASE_PUBLISHABLE_KEY') ?: '';

    if (!$supabaseUrl || !$supabaseKey) {
        throw new RuntimeException('Missing SUPABASE_URL or SUPABASE_SECRET_KEY');
    }

    // Database connection
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $name = getenv('DB_NAME') ?: 'city_of_writers';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASSWORD') ?: '';

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    logMsg('Connected to MySQL: ' . $name);

    // Generic HTTP GET against either PostgREST or the GoTrue admin API
    function apiFetch($url, $key) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'apikey: ' . $key,
                'Authorization: Bearer ' . $key,
                'Content-Type: application/json',
                'Prefer: return=representation',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);
        return [$httpCode, json_decode($response, true), $curlErr, $response];
    }

    // Tables to migrate in order via PostgREST (/rest/v1/<table>)
    $tables = [
        'profiles' => ['select' => '*'],
        'user_roles' => ['select' => '*'],
        'categories' => ['select' => '*'],
        'products' => ['select' => '*'],
        'product_categories' => ['select' => '*'],
        'site_settings' => ['select' => '*'],
        'shipping_rates' => ['select' => '*'],
        'coupons' => ['select' => '*'],
        'addresses' => ['select' => '*'],
        'orders' => ['select' => 'id,order_number,user_id,guest_email,guest_phone,guest_name,status,payment_method,payment_status,subtotal,shipping_cost,discount,total,shipping_address,notes,tracking_number,coupon_code,coupon_id,created_at,updated_at'],
        'order_items' => ['select' => '*'],
        'wishlist' => ['select' => '*'],
        'reviews' => ['select' => '*'],
        'marketing_costs' => ['select' => '*'],
    ];

    // --- 1) users: fetched from the GoTrue ADMIN API, not PostgREST,
    //     because auth.users is not exposed over /rest/v1/ ---
    logMsg('Migrating users...');
    $page = 1;
    $perPage = 1000;
    $totalUsers = 0;
    $users = [];
    do {
        $authUrl = "{$supabaseUrl}/auth/v1/admin/users?page={$page}&per_page={$perPage}";
        [$httpCode, $decoded, $curlErr, $raw] = apiFetch($authUrl, $supabaseKey);
        logMsg("  HTTP Code: {$httpCode}" . ($curlErr ? " (curl error: {$curlErr})" : ''));

        if ($httpCode !== 200) {
            logMsg('  Response: ' . substr((string)$raw, 0, 300));
            break;
        }

        $users = $decoded['users'] ?? [];
        logMsg('  Page ' . $page . ' user count: ' . count($users));

        if (!empty($users)) {
            ensureTableSchema($pdo, 'users', [
                'id' => $users[0]['id'] ?? null,
                'email' => $users[0]['email'] ?? null,
                'email_verified' => 0,
                'is_active' => 1,
                'created_at' => $users[0]['created_at'] ?? null,
                'last_sign_in_at' => $users[0]['last_sign_in_at'] ?? null,
            ]);
        }

        foreach ($users as $u) {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO `users` (`id`, `email`, `email_verified`, `is_active`, `created_at`, `last_sign_in_at`)
                     VALUES (:id, :email, :email_verified, :is_active, :created_at, :last_sign_in_at)
                     ON DUPLICATE KEY UPDATE
                       `email` = :email, `email_verified` = :email_verified,
                       `is_active` = :is_active, `last_sign_in_at` = :last_sign_in_at"
                );
                $stmt->execute([
                    'id' => $u['id'] ?? null,
                    'email' => $u['email'] ?? null,
                    'email_verified' => !empty($u['email_confirmed_at']) ? 1 : 0,
                    'is_active' => empty($u['banned_until']) ? 1 : 0,
                    'created_at' => $u['created_at'] ?? null,
                    'last_sign_in_at' => $u['last_sign_in_at'] ?? null,
                ]);
                $totalUsers++;
            } catch (Exception $e) {
                logMsg('  Error: ' . $e->getMessage());
            }
        }

        $page++;
    } while (count($users) === $perPage);

    logMsg("  Migrated {$totalUsers} users total");

    // --- 2) everything else via PostgREST ---
    foreach ($tables as $table => $config) {
        logMsg("Migrating {$table}...");

        $url = "{$supabaseUrl}/rest/v1/{$table}?select={$config['select']}&limit=10000";
        [$httpCode, $data, $curlErr, $raw] = apiFetch($url, $supabaseKey);

        logMsg("  HTTP Code: {$httpCode}" . ($curlErr ? " (curl error: {$curlErr})" : ''));

        if ($httpCode !== 200) {
            logMsg('  Response: ' . substr((string)$raw, 0, 300));
            logMsg("  Skipped (HTTP {$httpCode})");
            continue;
        }

        $data = $data ?: [];
        logMsg('  Data count: ' . count($data));

        if (empty($data)) {
            logMsg('  Skipped (empty)');
            continue;
        }

        ensureTableSchema($pdo, $table, $data[0]);

        $count = 0;
        foreach ($data as $row) {
            try {
                $row = prepareRowForInsert($row, $jsonColumns[$table] ?? []);
                $columns = array_keys($row);
                $placeholders = array_map(fn($c) => ":{$c}", $columns);
                $update = array_map(fn($c) => "`{$c}` = :{$c}", $columns);

                $sql = "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $placeholders) . ")";
                $sql .= " ON DUPLICATE KEY UPDATE " . implode(', ', $update);

                $stmt = $pdo->prepare($sql);
                $stmt->execute($row);
                $count++;
            } catch (Exception $e) {
                logMsg("  Error: " . $e->getMessage());
            }
        }

        logMsg("  Migrated {$count} rows");
    }

    $message = "Migration completed successfully!";
    logMsg($message);

    if ($isWeb) {
        echo "<h1>Migration Complete</h1>";
        echo "<p>{$message}</p>";
        echo "<pre>" . implode("\n", $logs) . "</pre>";
    } else {
        echo implode("\n", $logs) . "\n";
    }

} catch (Exception $e) {
    $error = 'Migration failed: ' . $e->getMessage();
    logMsg($error);

    if ($isWeb) {
        echo "<h1>Migration Failed</h1>";
        echo "<p style='color:red;'>{$error}</p>";
        echo "<pre>" . implode("\n", $logs) . "</pre>";
    } else {
        echo implode("\n", $logs) . "\n";
    }
    exit(1);
}