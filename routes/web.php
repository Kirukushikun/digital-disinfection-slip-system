<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view("/", "landing");

Route::get("/login", [SessionController::class,'create'])->name('login');
Route::post('/login', [SessionController::class,'store'])->name('login.store');
Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

Route::middleware(['auth', 'user.type:0'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'user'])->name('dashboard');

    Route::view('/incoming-trucks', 'user.incoming-trucks')->name('incoming-trucks');
    Route::view('/outgoing-trucks', 'user.outgoing-trucks')->name('outgoing-trucks');
    Route::view('/completed-trucks', 'user.completed-trucks')->name('completed-trucks');
});

Route::middleware(['auth', 'user.type:1'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
});

Route::middleware(['auth', 'user.type:2'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'superadmin'])->name('dashboard');
});

