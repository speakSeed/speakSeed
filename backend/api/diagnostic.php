<?php

header('Content-Type: application/json');

$diagnostics = [
    'status' => 'running',
    'php_version' => phpversion(),
    'vendor_exists' => file_exists(__DIR__ . '/../vendor/autoload.php'),
    'bootstrap_exists' => file_exists(__DIR__ . '/../bootstrap/app.php'),
    'env_vars' => [
        'APP_KEY' => env('APP_KEY') ? 'Set' : 'Not set',
        'DB_HOST' => env('DB_HOST') ? env('DB_HOST') : 'Not set',
        'DATABASE_URL' => env('DATABASE_URL') ? 'Set' : 'Not set',
    ],
];

// Try to load autoloader
try {
    require __DIR__ . '/../vendor/autoload.php';
    $diagnostics['autoloader'] = 'Loaded successfully';
} catch (Exception $e) {
    $diagnostics['autoloader_error'] = $e->getMessage();
}

// Try to bootstrap Laravel
try {
    $app = require __DIR__ . '/../bootstrap/app.php';
    $diagnostics['laravel_app'] = 'Bootstrap successful';
} catch (Exception $e) {
    $diagnostics['laravel_error'] = $e->getMessage();
    $diagnostics['laravel_trace'] = $e->getTraceAsString();
}

echo json_encode($diagnostics, JSON_PRETTY_PRINT);

