<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Landing Page
Route::get('/', function () {
    return view('landingpage');
})->name('home');

// Login Routes
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Register Routes
Route::get('/register', function () {
    return view('register');
})->name('register');

Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::middleware(['auth'])->group(function () {
    // Gunakan DashboardController untuk route dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
// Logout Web
Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

