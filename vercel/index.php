<?php

// Ensure writable directories exist in /tmp
$dirs = [
    '/tmp/views',
    '/tmp/sessions',
    '/tmp/cache/data',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Forward Vercel requests to Laravel entry point
require __DIR__ . '/../public/index.php';
