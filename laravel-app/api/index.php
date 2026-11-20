<?php
// Vercel Serverless Bridge for Laravel 10 (Direct Subfolder Deploy)

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

putenv("APP_STORAGE={$storagePath}");
putenv("VIEW_COMPILED_PATH={$storagePath}/framework/views");
putenv("SESSION_DRIVER=cookie");
putenv("CACHE_DRIVER=array");
putenv("LOG_CHANNEL=stderr");

require __DIR__ . '/../public/index.php';
