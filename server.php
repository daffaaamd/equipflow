<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 * Smart Router for PHP Built-in Web Server
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

$publicFile = __DIR__ . '/public' . $uri;

if ($uri !== '/' && file_exists($publicFile) && !is_dir($publicFile)) {
    // If docroot is public, let PHP handle it natively
    if (file_exists(getcwd() . $uri) && !is_dir(getcwd() . $uri)) {
        return false;
    }
    
    // Otherwise serve file directly from public directory
    $ext = pathinfo($publicFile, PATHINFO_EXTENSION);
    $mimes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'svg' => 'image/svg+xml',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
    ];
    $mime = $mimes[strtolower($ext)] ?? mime_content_type($publicFile) ?: 'application/octet-stream';
    header("Content-Type: $mime");
    header("Content-Length: " . filesize($publicFile));
    readfile($publicFile);
    exit;
}

require_once __DIR__ . '/public/index.php';
