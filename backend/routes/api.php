<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WordController;
use App\Http\Controllers\UserWordController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\QuizController;

// Word routes
Route::prefix('words')->group(function () {
    Route::get('/level/{level}', [WordController::class, 'getByLevel']);
    Route::get('/random', [WordController::class, 'getRandom']);
    Route::get('/fetch-random/{level}', [WordController::class, 'fetchRandomForLevel']);
    Route::get('/{id}', [WordController::class, 'show']);
    Route::post('/fetch', [WordController::class, 'fetchWord']);
});

// User words routes
Route::prefix('user-words')->group(function () {
    Route::get('/', [UserWordController::class, 'index']);
    Route::post('/', [UserWordController::class, 'store']);
    Route::put('/{id}', [UserWordController::class, 'update']);
    Route::delete('/{id}', [UserWordController::class, 'destroy']);
    Route::get('/review', [UserWordController::class, 'getDueForReview']);
});

// Progress routes
Route::prefix('progress')->group(function () {
    Route::get('/', [ProgressController::class, 'show']);
    Route::post('/', [ProgressController::class, 'update']);
    Route::get('/statistics', [ProgressController::class, 'statistics']);
});

// Quiz routes
Route::prefix('quiz')->group(function () {
    Route::post('/generate', [QuizController::class, 'generate']);
    Route::post('/submit', [QuizController::class, 'submit']);
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});

