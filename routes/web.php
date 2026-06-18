<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\FocusSessionController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\PrayerController;

// Dashboard
Route::get('/', function () {
    return view('dashboard');
});

// API Routes (prefixed with /api)
Route::prefix('api')->group(function () {
    // Tasks
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::patch('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

    // Focus Sessions
    Route::post('/focus-sessions', [FocusSessionController::class, 'store']);
    Route::get('/focus-sessions/today', [FocusSessionController::class, 'todayStats']);

    // Gamification
    Route::get('/gamification', [GamificationController::class, 'show']);

    // Weather
    Route::get('/weather', [WeatherController::class, 'show']);

    // Quotes
    Route::get('/quote', [QuoteController::class, 'random']);

    // Prayer Times
    Route::get('/prayer-times', [PrayerController::class, 'show']);
});
