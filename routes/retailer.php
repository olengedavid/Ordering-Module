<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\InventoryController;
use App\Http\Controllers\RegionController;

Route::get('/retailers/products', [InventoryController::class, 'retailersIndex'])
        ->name('retailer.products.index');
Route::get('/retailers/product-search', [InventoryController::class, 'getRetailerProducts'])
        ->name('retailer.products.search');
Route::get('retailer/regions/search', [RegionController::class, 'searchRegions']) ->name('retailer.regions.search');
