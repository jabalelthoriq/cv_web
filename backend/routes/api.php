<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\authController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\Auth;

// Route::post('/login', [authController::class, 'login']);

Route::apiResource('posts', PostController::class);

// PUBLIC
Route::post('/register', [Auth::class, 'register']);
Route::post('/login', [Auth::class, 'login']);

// PROTECTED
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [Auth::class, 'me']);
    Route::post('/logout', [Auth::class, 'logout']);
});

// DASHBOAARD   
Route::middleware('auth:sanctum')->get('/dashboard', [DashboardController::class, 'index']);