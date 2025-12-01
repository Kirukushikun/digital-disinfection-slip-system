<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view("/", "landing");

Route::get("/login", [SessionController::class,'create'])->name('login');
Route::post('/login', [SessionController::class,'store'])->name('login.store');

// Location-based login routes
Route::get("/location/{location}/login", [SessionController::class,'create'])->name('location.login');
Route::post('/location/{location}/login', [SessionController::class,'store'])->name('location.login.store');

Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

Route::middleware(['auth', 'user.type:0'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/incoming-trucks', [UserController::class, 'incomingTrucks'])->name('incoming-trucks');
    Route::get('/outgoing-trucks', [UserController::class, 'outgoingTrucks'])->name('outgoing-trucks');
    Route::get('/completed-trucks', [UserController::class, 'completedTrucks'])->name('completed-trucks');
});

Route::middleware(['auth', 'user.type:1'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
});

Route::middleware(['auth', 'user.type:2'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'superadmin'])->name('dashboard');
});

