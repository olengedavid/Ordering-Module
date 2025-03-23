<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserPermissionController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Supplier Users Routes
    Route::get('/supplier/users', [UserPermissionController::class, 'index'])->name('supplier.users.index');
    Route::post('/supplier/users', [UserPermissionController::class, 'store'])->name('supplier.users.store');
    Route::put('/supplier/users/{uuid}', [UserPermissionController::class, 'update'])->name('supplier.users.update');
    Route::get('/supplier/users/list', [UserPermissionController::class, 'getSupplierUsers'])->name('supplier.users.list');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/company.php';
require __DIR__ . '/warehouse.php';
