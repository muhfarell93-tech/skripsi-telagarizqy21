<?php
// Vercel Serverless Bridge for Laravel 10
// Telaga Rizqy 21 Web Application

// Ensure writable temporary directories in serverless environment
$storagePath = '/tmp/storage';
$subdirs = [
    $storagePath . '/framework/views',
    $storagePath . '/framework/cache',
    $storagePath . '/framework/sessions',
    $storagePath . '/logs',
    $storagePath . '/app/public'
];

foreach ($subdirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Set environment variables for serverless runtime
putenv("APP_STORAGE={$storagePath}");
putenv("VIEW_COMPILED_PATH={$storagePath}/framework/views");
putenv("SESSION_DRIVER=cookie");
putenv("CACHE_DRIVER=array");
putenv("LOG_CHANNEL=stderr");

// Forward to public index.php
if (file_exists(__DIR__ . '/../laravel-app/public/index.php')) {
    require __DIR__ . '/../laravel-app/public/index.php';
} elseif (file_exists(__DIR__ . '/../public/index.php')) {
    require __DIR__ . '/../public/index.php';
} else {
    require __DIR__ . '/public/index.php';
}
