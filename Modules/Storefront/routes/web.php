<?php

use Illuminate\Support\Facades\Route;
use Modules\Storefront\Http\Controllers\AdminPedidoWhatsappController;
use Modules\Storefront\Http\Controllers\AdminStorefrontController;
use Modules\Storefront\Http\Controllers\StorefrontController;

Route::middleware(['web'])->group(function () {
    Route::get('/', [StorefrontController::class, 'index'])->name('storefront.index');
    Route::get('/producto/{id}', [StorefrontController::class, 'show'])->name('storefront.producto');
    Route::get('/checkout', [StorefrontController::class, 'checkout'])->name('storefront.checkout');
    Route::post('/checkout', [StorefrontController::class, 'storePedido'])->name('storefront.store_pedido');
});

Route::prefix('admin/pedidos')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', [AdminPedidoWhatsappController::class, 'index'])->name('admin.pedidos.index');
    Route::post('/{id}/status', [AdminPedidoWhatsappController::class, 'updateStatus'])->name('admin.pedidos.status');
    Route::get('/{id}/ticket', [AdminPedidoWhatsappController::class, 'ticket'])->name('admin.pedidos.ticket');
});

Route::prefix('admin/zonas-delivery')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', [\Modules\Storefront\Http\Controllers\ZonaDeliveryController::class, 'index'])->name('admin.zonas.index');
    Route::post('/', [\Modules\Storefront\Http\Controllers\ZonaDeliveryController::class, 'store'])->name('admin.zonas.store');
    Route::post('/{id}/update', [\Modules\Storefront\Http\Controllers\ZonaDeliveryController::class, 'update'])->name('admin.zonas.update');
    Route::post('/{id}/delete', [\Modules\Storefront\Http\Controllers\ZonaDeliveryController::class, 'destroy'])->name('admin.zonas.delete');
});

Route::prefix('admin/productos')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', [\Modules\Storefront\Http\Controllers\AdminProductoController::class, 'index'])->name('admin.productos.index');
    Route::post('/', [\Modules\Storefront\Http\Controllers\AdminProductoController::class, 'store'])->name('admin.productos.store');
    Route::post('/{id}/update', [\Modules\Storefront\Http\Controllers\AdminProductoController::class, 'update'])->name('admin.productos.update');
    Route::post('/{id}/delete', [\Modules\Storefront\Http\Controllers\AdminProductoController::class, 'destroy'])->name('admin.productos.delete');
});

Route::prefix('admin/categorias')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', [\Modules\Storefront\Http\Controllers\AdminCategoriaController::class, 'index'])->name('admin.categorias.index');
    Route::post('/', [\Modules\Storefront\Http\Controllers\AdminCategoriaController::class, 'store'])->name('admin.categorias.store');
    Route::post('/{id}/update', [\Modules\Storefront\Http\Controllers\AdminCategoriaController::class, 'update'])->name('admin.categorias.update');
    Route::post('/{id}/delete', [\Modules\Storefront\Http\Controllers\AdminCategoriaController::class, 'destroy'])->name('admin.categorias.delete');
});

Route::prefix('admin/banners')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', [\Modules\Storefront\Http\Controllers\AdminBannerController::class, 'index'])->name('admin.banners.index');
    Route::post('/', [\Modules\Storefront\Http\Controllers\AdminBannerController::class, 'store'])->name('admin.banners.store');
    Route::post('/{id}/update', [\Modules\Storefront\Http\Controllers\AdminBannerController::class, 'update'])->name('admin.banners.update');
    Route::post('/{id}/delete', [\Modules\Storefront\Http\Controllers\AdminBannerController::class, 'destroy'])->name('admin.banners.delete');
});

Route::get('/admin', function () {
    return view('storefront::admin.dashboard');
})->middleware(['web', 'auth'])->name('admin.dashboard.main');

Route::prefix('admin/storefront')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', [AdminStorefrontController::class, 'index'])->name('admin.storefront.index');
    Route::post('/', [AdminStorefrontController::class, 'update'])->name('admin.storefront.update');
});
