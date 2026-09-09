<?php

namespace App\Migration;

use App\Database;

require_once __DIR__ . '/../vendor/autoload.php';

// Check for secret parameter (web access) or CLI mode
$secret = $_GET['secret'] ?? null;
$isWeb = $secret !== null;

if ($isWeb) {
    header('Content-Type: text/html; charset=utf-8');
    
    $expectedSecret = getenv('MIGRATION_SECRET') ?: 'cc9b20525806a840ecad34ec';
    if ($secret !== $expectedSecret) {
        http_response_code(403);
        echo "<h1>Forbidden</h1>";
        echo "<p>Invalid migration secret.</p>";
        exit;
    }
}

$logger = new MigrationLogger();

try {
    $logger->info('Initializing migration...');
    
    // Check if Supabase credentials are configured
    $supabaseUrl = getenv('SUPABASE_URL');
    $supabaseKey = getenv('SUPABASE_SECRET_KEY') ?: getenv('SUPABASE_PUBLISHABLE_KEY');
    
    if (!$supabaseUrl || !$supabaseKey) {
        throw new \RuntimeException('Missing SUPABASE_URL or SUPABASE_SECRET_KEY in .env');
    }
    
    $source = new SupabaseClient();
    $target = Database::connection();
    
    $logger->info('Connected to Supabase');
    $logger->info('Connected to MySQL');
    
    $migrator = new Migrator($source, $target, $logger);
    $migrator->run();
    
    $message = "Migration completed successfully. Check reports/ for details.";
    $logger->info($message);
    
    if ($isWeb) {
        echo "<h1>Migration Complete</h1>";
        echo "<p>{$message}</p>";
        echo "<p>Check backend/reports/ for detailed logs.</p>";
    } else {
        echo "\n{$message}\n";
    }
    
} catch (\Throwable $e) {
    $error = 'Migration failed: ' . $e->getMessage();
    $logger->error($error);
    
    if ($isWeb) {
        echo "<h1>Migration Failed</h1>";
        echo "<p style='color:red;'>{$error}</p>";
    } else {
        echo "\n{$error}\n";
    }
    exit(1);
}
