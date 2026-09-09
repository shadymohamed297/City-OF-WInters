<?php
// Minimal autoloader without Composer
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDirs = [
        __DIR__ . '/../lib/',
        __DIR__ . '/../migration/',
    ];
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relativeClass = substr($class, $len);
    
    foreach ($baseDirs as $baseDir) {
        // Try direct mapping first: MigrationLogger.php for App\Migration\MigrationLogger
        $file = $baseDir . basename($relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
        
        // Try full namespace mapping: Migration/MigrationLogger.php for App\Migration\MigrationLogger
        $filePart = str_replace('\\', '/', $relativeClass);
        $file = $baseDir . $filePart . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

// Load .env
function loadEnv(string $path): void {
    if (!file_exists($path)) {
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }
        
        $name = trim($parts[0]);
        $value = trim($parts[1]);
        
        // Remove quotes
        if (preg_match('/^"(.*)"$/', $value, $matches)) {
            $value = $matches[1];
        } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
            $value = $matches[1];
        }
        
        if (!getenv($name)) {
            putenv("$name=$value");
        }
    }
}

loadEnv(__DIR__ . '/../.env');
