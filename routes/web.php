<?php

use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::view("/", "landing");

Route::get("/login", [SessionController::class,'create'])->name('login');
Route::post('/login', [SessionController::class,'store'])->name('login.store');

Route::get('/home', function () {
    return view('home');
})->middleware('auth');

