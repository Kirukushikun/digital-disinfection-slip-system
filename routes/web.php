<?php

use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view("/", "landing");

Route::get("/login", [SessionController::class,'create'])->name('login');
Route::post('/login', [SessionController::class,'store'])->name('login.store');

Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        $user = Auth::user();

        return redirect()->route($user->dashboardRoute());
    })->name('home');

    Route::view('/user/home', 'user.home')
        ->middleware('user.type:0')
        ->name('user.home');

    Route::view('/admin/home', 'admin.home')
        ->middleware('user.type:1')
        ->name('admin.home');

    Route::view('/superadmin/home', 'superadmin.home')
        ->middleware('user.type:2')
        ->name('superadmin.home');
});