<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Company\CompanyBankAccountController;
use App\Http\Controllers\Company\DeliveryRegionController;

Route::middleware('auth')->group(function () {
    Route::get('suppliers', [CompanyController::class, 'index'])->name('suppliers.index');
    Route::get('suppliers/create', [CompanyController::class, 'create'])->name('suppliers.create');
    Route::post('suppliers/create', [CompanyController::class, 'api_store_company']);
    Route::get('suppliers/show', [CompanyController::class, 'show'])->name('suppliers.show');
    Route::post('supplier/bank-accounts', [CompanyBankAccountController::class, 'store'])->name('supplier.bank-accounts.store');
    Route::put('supplier/bank-accounts', [CompanyBankAccountController::class, 'update'])->name('supplier.bank-accounts.update');
    Route::delete('supplier/bank-accounts', [CompanyBankAccountController::class, 'destroy'])->name('supplier.bank-accounts.destroy');

    #Delivery Regions Routes
    Route::get('supplier/delivery-regions', [DeliveryRegionController::class, 'getDeliveryRegionsByCompany'])->name('supplier.delivery-regions.index');
    Route::post('supplier/delivery-regions', [DeliveryRegionController::class, 'store'])->name('supplier.delivery-regions.store');

    #Product Routes
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products/create', [ProductController::class, 'store'])->name('products.store');
    
});
