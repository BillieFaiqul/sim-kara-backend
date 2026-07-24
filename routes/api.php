<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaryaController;
use App\Http\Middleware\JwtMiddleware;

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware(JwtMiddleware::class);
Route::post('/refresh', [AuthController::class, 'refresh'])->middleware(JwtMiddleware::class);
Route::get('/user', [AuthController::class, 'me'])->middleware(JwtMiddleware::class);

// Karya Routes - Public
Route::get('/karya', [KaryaController::class, 'index']);
Route::get('/karya/{id}', [KaryaController::class, 'show']);
Route::get('/karya-stats', [KaryaController::class, 'getStats']);

// Karya Routes - Protected
Route::middleware(JwtMiddleware::class)->group(function () {
    Route::post('/karya', [KaryaController::class, 'store']);
    Route::put('/karya/{id}', [KaryaController::class, 'update']);
    Route::delete('/karya/{id}', [KaryaController::class, 'delete']);
    Route::post('/karya/{id}/submit', [KaryaController::class, 'submit']);
    Route::get('/karya-saya', [KaryaController::class, 'getByUser']);
    Route::get('/karya/{id}/download', [KaryaController::class, 'download']);
});
