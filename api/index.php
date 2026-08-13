<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Vercel Serverless Function Bridge for Laravel 11/12

// 1. Ensure /tmp writable directories exist for Laravel
$storageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/app/public',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// 2. Set serverless environment variables
putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';

putenv('APP_STORAGE=/tmp/storage');
$_ENV['APP_STORAGE'] = '/tmp/storage';

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

putenv('SESSION_DRIVER=cookie');
$_ENV['SESSION_DRIVER'] = 'cookie';

putenv('CACHE_STORE=array');
$_ENV['CACHE_STORE'] = 'array';

putenv('LOG_CHANNEL=stderr');
$_ENV['LOG_CHANNEL'] = 'stderr';

// 3. Ensure pre-seeded SQLite database is copied to /tmp
$sqliteDest = '/tmp/database.sqlite';
$possibleSources = [
    __DIR__ . '/../database/database.sqlite',
    __DIR__ . '/../dist/database.sqlite',
    __DIR__ . '/../public/database.sqlite',
    dirname(__DIR__) . '/database/database.sqlite',
    ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/../database/database.sqlite',
    ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/database/database.sqlite',
];

if (!file_exists($sqliteDest) || filesize($sqliteDest) === 0) {
    foreach ($possibleSources as $src) {
        if (!empty($src) && file_exists($src) && filesize($src) > 0) {
            @copy($src, $sqliteDest);
            break;
        }
    }
}

// If still missing, create an empty sqlite database so it doesn't crash
if (!file_exists($sqliteDest)) {
    @touch($sqliteDest);
}

putenv('DB_CONNECTION=sqlite');
$_ENV['DB_CONNECTION'] = 'sqlite';

putenv('DB_DATABASE=' . $sqliteDest);
$_ENV['DB_DATABASE'] = $sqliteDest;

// 4. Forward request to Laravel standard public/index.php
require __DIR__ . '/../public/index.php';
