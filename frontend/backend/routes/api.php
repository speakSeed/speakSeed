<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WordController;
use App\Http\Controllers\Api\UserWordController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\QuizController;

// Word routes
Route::prefix('words')->group(function () {
    Route::get('/level/{level}', [WordController::class, 'getByLevel']);
    Route::get('/random', [WordController::class, 'random']);
    Route::get('/{id}', [WordController::class, 'show']);
    Route::post('/fetch', [WordController::class, 'fetchAndStore']);
});

// User words routes
Route::prefix('user-words')->group(function () {
    Route::get('/', [UserWordController::class, 'index']);
    Route::post('/', [UserWordController::class, 'store']);
    Route::delete('/{id}', [UserWordController::class, 'destroy']);
    Route::put('/{id}/review', [UserWordController::class, 'updateReview']);
    Route::get('/review', [UserWordController::class, 'getForReview']);
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
