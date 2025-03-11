<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\CompanyController;

Route::middleware('auth')->group(function() {
    Route::get('suppliers/create', [CompanyController::class, 'api_create_supplier'])
    ->name('suppliers.create');
    
    Route::post('suppliers/create', [CompanyController::class, 'api_store_company']);


});