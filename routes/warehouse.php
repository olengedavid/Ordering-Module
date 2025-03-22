<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\WarehouseController;

Route::middleware('auth')->group(function() {
    Route::get('supplier/warehouses', [WarehouseController::class, 'index'])->name('supplierwarehouse.index');
    // Route::get('suppliers/create', [WarehouseController::class, 'create'])->name('supplierwarehouse.create');
    Route::post('supplier/warehouses-create', [WarehouseController::class, 'api_store_warehouse']) ->name('supplierwarehouse.create');
    Route::put('supplier/warehouses', [WarehouseController::class, 'update'])->name('supplierwarehouse.update');
    Route::get('supplier/warehouses/list', [WarehouseController::class, 'getWarehousesBySupplier'])->name('supplier.warehouses.list');
    Route::get('supplier/warehouses/paginate', [WarehouseController::class, 'getPaginatedSupplierWarehouses'])->name('supplier.warehouses.paginate');
});