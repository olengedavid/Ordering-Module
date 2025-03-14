<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\CompanyController;

Route::middleware('auth')->group(function() {
    Route::get('suppliers', [CompanyController::class, 'index'])->name('suppliers.index');
    Route::get('suppliers/create', [CompanyController::class, 'create'])->name('suppliers.create');
    Route::post('suppliers/create', [CompanyController::class, 'api_store_company']);

});