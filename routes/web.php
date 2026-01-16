<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view("/", "landing");

Route::get("/login", [SessionController::class,'create'])->name('login');
Route::post('/login', [SessionController::class,'store'])->middleware('custom.throttle:5,15')->name('login.store');

// Location-based login routes
Route::get("/location/{location}/login", [SessionController::class,'create'])->name('location.login');
Route::post('/location/{location}/login', [SessionController::class,'store'])->middleware('custom.throttle:5,15')->name('location.login.store');

Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

// Password change routes (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/password/change', [App\Http\Controllers\PasswordController::class, 'show'])->name('password.change');
    Route::post('/password/verify', [App\Http\Controllers\PasswordController::class, 'verify'])->name('password.verify');
    Route::put('/password', [App\Http\Controllers\PasswordController::class, 'update'])->name('password.update');
});

Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/incoming-slips', [UserController::class, 'incomingSlips'])->name('incoming-slips');
    Route::get('/outgoing-slips', [UserController::class, 'outgoingSlips'])->name('outgoing-slips');
    Route::get('/completed-slips', [UserController::class, 'completedSlips'])->name('completed-slips');
    Route::get('/issues', [UserController::class, 'issues'])->name('issues');
    Route::get('/issue', [UserController::class, 'issue'])->name('issue');
    
    // Super Guard Data Management Routes (accessible to super guards and super admins)
    Route::middleware('super.guard')->group(function () {
        Route::get('/data/guards', [UserController::class, 'dataGuards'])->name('data.guards');
        Route::get('/data/drivers', [UserController::class, 'dataDrivers'])->name('data.drivers');
        Route::get('/data/locations', [UserController::class, 'dataLocations'])->name('data.locations');
        Route::get('/data/vehicles', [UserController::class, 'dataVehicles'])->name('data.vehicles');
    });
    
    // Print routes for super guards (outside nested group to inherit name prefix correctly)
    Route::middleware('super.guard')->group(function () {
        Route::get('/print/guards', [UserController::class, 'printGuards'])->name('print.guards');
        Route::get('/print/drivers', [UserController::class, 'printDrivers'])->name('print.drivers');
        Route::get('/print/locations', [UserController::class, 'printLocations'])->name('print.locations');
        Route::get('/print/vehicles', [UserController::class, 'printVehicles'])->name('print.vehicles');
    });
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/guards', [AdminController::class, 'guards'])->name('guards');
    Route::get('/drivers', [AdminController::class, 'drivers'])->name('drivers');
    Route::get('/locations', [AdminController::class, 'locations'])->name('locations');
    Route::get('/vehicles', [AdminController::class, 'vehicles'])->name('vehicles');
    Route::get('/slips', [AdminController::class, 'slips'])->name('slips');
    Route::get('/issues', [AdminController::class, 'issues'])->name('issues');
    Route::get('/audit-trail', [AdminController::class, 'auditTrail'])->name('audit-trail');
    Route::get('/print/guards', [AdminController::class, 'printGuards'])->name('print.guards');
    Route::get('/print/drivers', [AdminController::class, 'printDrivers'])->name('print.drivers');
    Route::get('/print/locations', [AdminController::class, 'printLocations'])->name('print.locations');
    Route::get('/print/vehicles', [AdminController::class, 'printVehicles'])->name('print.vehicles');
    Route::get('/print/slips', [AdminController::class, 'printSlips'])->name('print.slips');
    Route::get('/print/slip', [AdminController::class, 'printSlip'])->name('print.slip');
    Route::get('/print/audit-trail', [AdminController::class, 'printAuditTrail'])->name('print.audit-trail');
});

Route::middleware(['auth'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/guards', [SuperAdminController::class, 'guards'])->name('guards');
    Route::get('/admins', [SuperAdminController::class, 'admins'])->name('admins');
    Route::get('/drivers', [SuperAdminController::class, 'drivers'])->name('drivers');
    Route::get('/locations', [SuperAdminController::class, 'locations'])->name('locations');
    Route::get('/vehicles', [SuperAdminController::class, 'vehicles'])->name('vehicles');
    Route::get('/slips', [SuperAdminController::class, 'slips'])->name('slips');
    Route::get('/issues', [SuperAdminController::class, 'issues'])->name('issues');
    Route::get('/audit-trail', [SuperAdminController::class, 'auditTrail'])->name('audit-trail');
    Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings');
    Route::get('/print/guards', [SuperAdminController::class, 'printGuards'])->name('print.guards');
    Route::get('/print/admins', [SuperAdminController::class, 'printAdmins'])->name('print.admins');
    Route::get('/print/drivers', [SuperAdminController::class, 'printDrivers'])->name('print.drivers');
    Route::get('/print/locations', [SuperAdminController::class, 'printLocations'])->name('print.locations');
    Route::get('/print/vehicles', [SuperAdminController::class, 'printVehicles'])->name('print.vehicles');
    Route::get('/print/slips', [SuperAdminController::class, 'printSlips'])->name('print.slips');
    Route::get('/print/slip', [SuperAdminController::class, 'printSlip'])->name('print.slip');
    Route::get('/print/audit-trail', [SuperAdminController::class, 'printAuditTrail'])->name('print.audit-trail');
});

