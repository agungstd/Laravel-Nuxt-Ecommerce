<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Group route with prefix "admin"
Route::prefix('admin')->group(function () {

    // Route login
    Route::post('/login', [App\Http\Controllers\Api\Admin\LoginController::class, 'index', ['as' => 'admin']]);

    // Group route with middleware "auth:api_admin"
    Route::group(['middleware' => 'auth:api_admin'], function() {

        // Data user
        Route::get('/user', [App\Http\Controllers\Api\Admin\LoginController::class, 'getUser', ['as' => 'admin']]);

        // Refresh token JWT
        Route::get('/refresh', [App\Http\Controllers\Api\Admin\LoginController::class, 'refreshToken', ['as' => 'admin']]);

        // Logout
        Route::post('/logout', [App\Http\Controllers\Api\Admin\LoginController::class, 'logout', ['as' => 'admin']]);

        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Api\Admin\DashboardController::class, 'index', ['as' => 'admin']]);

        // Categories resource
        Route::apiResource('/categories', App\Http\Controllers\Api\Admin\CategoryController::class, ['except' => ['create', 'edit'], 'as' => 'admin']);

        // Products resource
        Route::apiResource('/products', App\Http\Controllers\Api\Admin\ProductController::class, ['except' => ['create', 'edit'], 'as' => 'admin']);

        // Invoices resource
        Route::apiResource('/invoices', App\Http\Controllers\Api\Admin\InvoiceController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy'], 'as' => 'admin']);

        // Customer
        Route::get('/customers', [App\Http\Controllers\Api\Admin\CustomerController::class, 'index', ['as' => 'admin']]);

        // Sliders resource
        Route::apiResource('/sliders', App\Http\Controllers\Api\Admin\SliderController::class, ['except' => ['create', 'show', 'edit', 'update'], 'as' => 'admin']);

        // Users resource
        Route::apiResource('/users', App\Http\Controllers\Api\Admin\UserController::class, ['except' => ['create', 'edit'], 'as' => 'admin']);
    });
});

// Group route with prefix "customer"
Route::prefix('customer')->group(function () {

    // Route register
    Route::post('/register', [App\Http\Controllers\Api\Customer\RegisterController::class, 'store'], ['as' => 'customer']);

    // Route login
    Route::post('/login', [App\Http\Controllers\Api\Customer\LoginController::class, 'index'], ['as' => 'customer']);

    // Group route with middleware "auth:api_customer"
    Route::group(['middleware' => 'auth:api_customer'], function() {

        // Data user
        Route::get('/user', [App\Http\Controllers\Api\Customer\LoginController::class, 'getUser'], ['as' => 'customer']);

        // Refresh token JWT
        Route::get('/refresh', [App\Http\Controllers\Api\Customer\LoginController::class, 'refreshToken'], ['as' => 'customer']]);

        // Logout
        Route::post('/logout', [App\Http\Controllers\Api\Customer\LoginController::class, 'logout'], ['as' => 'customer']]);

        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Api\Customer\DashboardController::class, 'index'], ['as' => 'customer']]);

        // Invoices resource
        Route::apiResource('/invoices', App\Http\Controllers\Api\Customer\InvoiceController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy'], 'as' => 'customer']);

        // Review
        Route::post('/reviews', [App\Http\Controllers\Api\Customer\ReviewController::class, 'store'], ['as' => 'customer']);
    });
});

// Group route with prefix "web"
Route::prefix('web')->group(function () {

    // Categories resource
    Route::apiResource('/categories', App\Http\Controllers\Api\Web\CategoryController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy'], 'as' => 'web']);

    // Products resource
    Route::apiResource('/products', App\Http\Controllers\Api\Web\ProductController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy'], 'as' => 'web']);

    // Sliders route
    Route::get('/sliders', [App\Http\Controllers\Api\Web\SliderController::class, 'index'], ['as' => 'web']);

    // Rajaongkir
    Route::get('/rajaongkir/provinces', [App\Http\Controllers\Api\Web\RajaOngkirController::class, 'getProvinces'], ['as' => 'web']);
    Route::post('/rajaongkir/cities', [App\Http\Controllers\Api\Web\RajaOngkirController::class, 'getCities'], ['as' => 'web']);
    Route::post('/rajaongkir/checkOngkir', [App\Http\Controllers\Api\Web\RajaOngkirController::class, 'checkOngkir'], ['as' => 'web']);

    // Get cart
    Route::get('/carts', [App\Http\Controllers\Api\Web\CartController::class, 'index'], ['as' => 'web']);

    // Store cart
    Route::post('/carts', [App\Http\Controllers\Api\Web\CartController::class, 'store'], ['as' => 'web']);

    // Get cart price
    Route::get('/carts/total_price', [App\Http\Controllers\Api\Web\CartController::class, 'getCartPrice'], ['as' => 'web']);

    // Get cart weight
    Route::get('/carts/total_weight', [App\Http\Controllers\Api\Web\CartController::class, 'getCartWeight'], ['as' => 'web']);

    // Remove cart
    Route::post('/carts/remove', [App\Http\Controllers\Api\Web\CartController::class, 'removeCart'], ['as' => 'web']);

    // Checkout route
    Route::post('/checkout', [App\Http\Controllers\Api\Web\CheckoutController::class, 'store'], ['as' => 'web']);

    // Notification handler route
    Route::post('/notification', [App\Http\Controllers\Api\Web\NotificationHandlerController::class, 'index'], ['as' => 'web']);
});
    