<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Company\CompanyBankAccountController;
use App\Http\Controllers\Company\DeliveryRegionController;
use App\Http\Controllers\Company\InventoryController;

Route::middleware('auth')->group(function () {
    Route::get('admin/suppliers', [CompanyController::class, 'index'])->name('admin.suppliers.index');
    Route::get('suppliers/create', [CompanyController::class, 'create'])->name('suppliers.create');
    Route::post('suppliers/create', [CompanyController::class, 'api_store_company']);
    Route::get('admin/suppliers/show', [CompanyController::class, 'show'])->name('suppliers.show');
    Route::post('admin/suppliers/update', [CompanyController::class, 'update'])->name('admin.suppliers.update');
    Route::post('admin/suppliers/activate', [CompanyController::class, 'activate'])->name('admin.suppliers.activate');
    Route::post('supplier/bank-accounts', [CompanyBankAccountController::class, 'store'])->name('supplier.bank-accounts.store');
    Route::put('supplier/bank-accounts', [CompanyBankAccountController::class, 'update'])->name('supplier.bank-accounts.update');
    Route::delete('supplier/bank-accounts', [CompanyBankAccountController::class, 'destroy'])->name('supplier.bank-accounts.destroy');

    #Delivery Regions Routes
    Route::get('supplier/delivery-regions', [DeliveryRegionController::class, 'getDeliveryRegionsByCompany'])->name('supplier.delivery-regions.index');
    Route::post('supplier/delivery-regions', [DeliveryRegionController::class, 'store'])->name('supplier.delivery-regions.store');

    #Product Routes
    Route::get('supplier/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('supplier/products/paginate', [ProductController::class, 'getPaginatedProductsBySupplier'])->name('supplier.products.paginate');
    Route::get('supplier/products/list', [ProductController::class, 'getProductsBySupplier'])->name('supplier.products.list');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products/create', [ProductController::class, 'store'])->name('products.store');
    
    #Inventory Routes
    // Route::get('supplier/inventories', [InventoryController::class, 'index'])->name('supplier.inventories.index');
    Route::get('supplier/inventories', [InventoryController::class, 'index'])->name('supplier.inventories.index');
    Route::get('api/supplier/inventories', [InventoryController::class, 'getInventoriesBySupplier'])->name('supplier.inventories.list');
    Route::post('supplier/inventories', [InventoryController::class, 'store'])->name('supplier.inventories.store');
    Route::put('supplier/inventories/{inventory}', [InventoryController::class, 'update'])->name('supplier.inventories.update');
    Route::delete('supplier/inventories/{inventory}', [InventoryController::class, 'destroy'])->name('supplier.inventories.destroy');
});
