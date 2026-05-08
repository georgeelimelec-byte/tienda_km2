<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar rutas para tu aplicación / Módulo
|
*/

Route::middleware(['auth'])->prefix('admin/inventory')->group(function () {
    Route::view('products', 'inventory::index')->name('inventory.products');
    Route::redirect('categories', '/admin/categorias')->name('inventory.categories');
});
