<?php


use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;   
use App\Http\Controllers\CoffeeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\OrderController;



// user authentication  
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
                                                                
Route::prefix("/auth")->group(function () {
    Route::post("/register", [AuthController::class, "register"]);
    Route::post("/login", [AuthController::class, "login"]); 
});


// displaying coffees 
Route::get('/coffees', [CoffeeController::class, 'getAllCoffees']);
Route::get('/coffees/{category}', [CoffeeController::class, 'coffeeCategory']);

Route::get('/coffee/{id}', [CoffeeController::class, 'getCoffee']);


// adding new coffee 
Route::post('/coffees', [CoffeeController::class, 'createCoffee']);

Route::delete('coffee/{coffee}', [CoffeeController::class, "destroy"]);

// Cart
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'addToCart']);
    Route::put('/cart/{cart}', [CartController::class, 'updateCart']);
    Route::delete('/cart/{cart}', [CartController::class, 'deleteCart']);
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});

//Orders
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('orders', OrderController::class);
    // Route::get('allOrders', OrderController::class, 'getAllOrders');
});

//Order items
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('order-items', OrderItemController::class)->middleware('auth');

});
