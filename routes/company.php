<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Product\ProductController;

Route::middleware('auth')->group(function() {
    Route::get('suppliers', [CompanyController::class, 'index'])->name('suppliers.index');
    Route::get('suppliers/create', [CompanyController::class, 'create'])->name('suppliers.create');
    Route::post('suppliers/create', [CompanyController::class, 'api_store_company']);

#Product Routes
Route::middleware('auth')->group(function() {
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products/create', [ProductController::class, 'store']) ->name('products.store');
});

});