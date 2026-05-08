<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas API - Tienda Virtual (Storefront) + Auth
|--------------------------------------------------------------------------
| Prefijo: /api/v1/storefront
| Protegidas con Laravel Sanctum
*/

// === Rutas públicas (sin autenticación) ===
Route::prefix('v1/storefront')->group(function () {

    // Auth
    Route::post('/login', [\Modules\Auth\Http\Controllers\Api\AuthApiController::class, 'login']);
    Route::post('/register', [\Modules\Auth\Http\Controllers\Api\AuthApiController::class, 'register']);

    // Catálogo público
    Route::get('/products', [\Modules\Storefront\Http\Controllers\Api\CatalogController::class, 'index']);
    Route::get('/products/{id}', [\Modules\Storefront\Http\Controllers\Api\CatalogController::class, 'show']);
    Route::get('/categories', [\Modules\Storefront\Http\Controllers\Api\CatalogController::class, 'categories']);
    Route::get('/banners', [\Modules\Storefront\Http\Controllers\Api\CatalogController::class, 'banners']);
});

// === Rutas protegidas (requieren token Sanctum) ===
Route::prefix('v1/storefront')->middleware('auth:sanctum')->group(function () {

    // Perfil
    Route::get('/me', [\Modules\Auth\Http\Controllers\Api\AuthApiController::class, 'me']);
    Route::post('/logout', [\Modules\Auth\Http\Controllers\Api\AuthApiController::class, 'logout']);

    // Carrito
    Route::get('/cart', [\Modules\Storefront\Http\Controllers\Api\CartController::class, 'index']);
    Route::post('/cart', [\Modules\Storefront\Http\Controllers\Api\CartController::class, 'addItem']);
    Route::put('/cart/{id}', [\Modules\Storefront\Http\Controllers\Api\CartController::class, 'updateItem']);
    Route::delete('/cart/{id}', [\Modules\Storefront\Http\Controllers\Api\CartController::class, 'removeItem']);

    // Checkout
    Route::post('/checkout', [\Modules\Storefront\Http\Controllers\Api\CheckoutController::class, 'process']);
    Route::get('/orders', [\Modules\Storefront\Http\Controllers\Api\CheckoutController::class, 'myOrders']);
    Route::get('/orders/{id}', [\Modules\Storefront\Http\Controllers\Api\CheckoutController::class, 'showOrder']);
});
