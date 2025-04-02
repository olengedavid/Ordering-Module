<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\InventoryController;


Route::get('/retailers/products', [InventoryController::class, 'retailersIndex'])
        ->name('retailer.products.index');
Route::get('/retailers/product-search', [InventoryController::class, 'getRetailerProducts'])
        ->name('retailer.products.search');
