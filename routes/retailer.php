<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\InventoryController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\Company\OrderController;
use Inertia\Inertia;
use App\Http\Controllers\Company\CartController;

Route::get('/retailers/products', [InventoryController::class, 'retailersIndex'])
        ->name('retailer.products.index');
Route::get('/retailers/product-search', [InventoryController::class, 'getRetailerProducts'])
        ->name('retailer.products.search');
Route::get('retailer/regions/search', [RegionController::class, 'searchRegions']) ->name('retailer.regions.search');

Route::post('retailers/create-order', [OrderController::class, 'store'])->name('retailer.orders.create');     
Route::put('retailers/update-order/{uuid}', [OrderController::class, 'update'])->name('retailer.orders.update');

// Cart and Order Routes
Route::get('/retailers/cart', function () {
    return Inertia::render('Retail/ShoppingCart');
})->name('retailer.cart');

Route::get('/retailers/orders', function () {
    return Inertia::render('Retail/RetailerOrders');
})->name('retailer.orders');

Route::get('/retailers/orders/{id}', function ($id) {
    return Inertia::render('Retail/Components/OrderDetails', [
        'id' => $id
    ]);
})->name('retailer.orders.show');

# Cart actions
Route::post('retailers/cart/add', [CartController::class, 'addToCart'])->name('retailer.cart.add');
Route::delete('retailers/cart/remove/{uuid}', [CartController::class, 'removeFromCart'])->name('retailer.cart.remove');
Route::get('retailers/cart/items', [CartController::class, 'getCartItems'])->name('retailer.cart.items');
Route::delete('retailers/cart/clear', [CartController::class, 'clearCart'])->name('retailer.cart.clear');
 