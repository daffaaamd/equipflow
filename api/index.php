<?php

// Vercel Serverless Function Bridge for Laravel

// On Vercel, ensure SQLite database exists in writable /tmp directory if using SQLite
if (!file_exists('/tmp/database.sqlite') && file_exists(__DIR__ . '/../database/database.sqlite')) {
    @copy(__DIR__ . '/../database/database.sqlite', '/tmp/database.sqlite');
}

// Forward to normal Laravel public/index.php
require __DIR__ . '/../public/index.php';
