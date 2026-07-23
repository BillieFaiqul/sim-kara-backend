<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\CheckRole;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware(JwtMiddleware::class);
Route::post('/refresh', [AuthController::class, 'refresh'])->middleware(JwtMiddleware::class);
Route::get('/user', [AuthController::class, 'me'])->middleware(JwtMiddleware::class);
