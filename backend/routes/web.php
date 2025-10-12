<?php

use Illuminate\Support\Facades\Route;

// Health check endpoint that doesn't require database
Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'SpeakSeed API is running',
        'version' => '1.0.0',
        'timestamp' => now()->toISOString()
    ]);
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'api' => 'SpeakSeed Backend',
        'laravel_version' => app()->version()
    ]);
});

