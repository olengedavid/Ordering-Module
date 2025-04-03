<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaticDataController;
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

    // Supplier Details Route
    Route::get('/supplier/details', function() { 
        return Inertia::render('Supplier/SupplierDetails', [
            'supplier' => auth()->user()->company
        ]); 
    })->name('supplier.details');

    // Supplier Users Routes
    Route::get('/supplier/users', [UserPermissionController::class, 'index'])->name('supplier.users.index');
    Route::post('/supplier/users', [UserPermissionController::class, 'store'])->name('supplier.users.store');
    Route::put('/supplier/users/{uuid}', [UserPermissionController::class, 'update'])->name('supplier.users.update');
    Route::get('/supplier/users/list', [UserPermissionController::class, 'getSupplierUsers'])->name('supplier.users.list');

    // Admin Users Routes
    Route::get('/admin/users', function() { return Inertia::render('Admin/AdminUsers'); })->name('admin.users.index');
    Route::post('/admin/users', [UserPermissionController::class, 'storeAdminUser'])->name('admin.users.store');
    Route::put('/admin/users/{id}', [UserPermissionController::class, 'updateAdminUser'])->name('admin.users.update');
    Route::get('/admin/users/list', [UserPermissionController::class, 'getAdminUsers'])->name('admin.users.list');

    // Static Data Routes
    Route::get('static/units', [StaticDataController::class, 'getUnits'])->name('static.units');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/company.php';
require __DIR__ . '/warehouse.php';
require __DIR__. '/retailer.php';
