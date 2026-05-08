<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AdminBusinessController;
use Modules\Auth\Http\Controllers\AdminPermissionsController;
use Modules\Auth\Http\Controllers\AdminReportsController;
use Modules\Auth\Http\Controllers\AdminRolesController;
use Modules\Auth\Http\Controllers\AdminSettingsController;
use Modules\Auth\Http\Controllers\AdminUsersController;
use Modules\Auth\Http\Controllers\LoginController;
use Modules\Auth\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Rutas Web — Módulo Auth
|--------------------------------------------------------------------------
| Prefijo: ninguno (rutas raíz)
| Login, logout, dashboard
*/

// ── Rutas públicas (guest) ──
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [LoginController::class, 'login'])->name('auth.login.submit');
});

// ── Rutas protegidas (authenticated) ──
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('auth.logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::prefix('admin/usuarios')->group(function () {
        Route::get('/', [AdminUsersController::class, 'index'])
            ->middleware('check.permission:Usuarios,leer')
            ->name('admin.usuarios.index');
        Route::get('/crear', [AdminUsersController::class, 'create'])
            ->middleware('check.permission:Usuarios,crear')
            ->name('admin.usuarios.create');
        Route::post('/', [AdminUsersController::class, 'store'])
            ->middleware('check.permission:Usuarios,crear')
            ->name('admin.usuarios.store');
        Route::get('/{id}/editar', [AdminUsersController::class, 'edit'])
            ->middleware('check.permission:Usuarios,editar')
            ->name('admin.usuarios.edit');
        Route::post('/{id}/update', [AdminUsersController::class, 'update'])
            ->middleware('check.permission:Usuarios,editar')
            ->name('admin.usuarios.update');
        Route::post('/{id}/delete', [AdminUsersController::class, 'destroy'])
            ->middleware('check.permission:Usuarios,eliminar')
            ->name('admin.usuarios.delete');
    });

    Route::prefix('admin/roles')->group(function () {
        Route::get('/', [AdminRolesController::class, 'index'])
            ->middleware('check.permission:Usuarios,leer')
            ->name('admin.roles.index');
        Route::get('/crear', [AdminRolesController::class, 'create'])
            ->middleware('check.permission:Usuarios,crear')
            ->name('admin.roles.create');
        Route::post('/', [AdminRolesController::class, 'store'])
            ->middleware('check.permission:Usuarios,crear')
            ->name('admin.roles.store');
        Route::get('/{id}/editar', [AdminRolesController::class, 'edit'])
            ->middleware('check.permission:Usuarios,editar')
            ->name('admin.roles.edit');
        Route::post('/{id}/update', [AdminRolesController::class, 'update'])
            ->middleware('check.permission:Usuarios,editar')
            ->name('admin.roles.update');
        Route::post('/asignar', [AdminRolesController::class, 'assign'])
            ->middleware('check.permission:Usuarios,editar')
            ->name('admin.roles.assign');
        Route::post('/{id}/delete', [AdminRolesController::class, 'destroy'])
            ->middleware('check.permission:Usuarios,eliminar')
            ->name('admin.roles.delete');
    });

    Route::prefix('admin/permisos')->group(function () {
        Route::get('/', [AdminPermissionsController::class, 'index'])
            ->middleware('check.permission:Usuarios,leer')
            ->name('admin.permisos.index');
        Route::post('/rol/{roleId}', [AdminPermissionsController::class, 'updateRole'])
            ->middleware('check.permission:Usuarios,editar')
            ->name('admin.permisos.roles.update');
        Route::post('/usuario/{userId}', [AdminPermissionsController::class, 'updateUser'])
            ->middleware('check.permission:Usuarios,editar')
            ->name('admin.permisos.usuarios.update');
    });

    Route::prefix('admin/configuracion')->group(function () {
        Route::get('/', [AdminSettingsController::class, 'index'])
            ->middleware('check.permission:Configuracion,leer')
            ->name('admin.configuracion.index');
        Route::post('/', [AdminSettingsController::class, 'update'])
            ->middleware('check.permission:Configuracion,editar')
            ->name('admin.configuracion.update');
    });

    Route::prefix('admin/reportes')->group(function () {
        Route::get('/', [AdminReportsController::class, 'index'])
            ->middleware('check.permission:Reportes,leer')
            ->name('admin.reportes.index');
        Route::get('/pedidos-whatsapp.csv', [AdminReportsController::class, 'exportOrdersCsv'])
            ->middleware('check.permission:Reportes,leer')
            ->name('admin.reportes.export.pedidos');
    });

    Route::get('/admin/business-data', [AdminBusinessController::class, 'index'])
        ->middleware('check.permission:Reportes,leer')
        ->name('admin.business.index');
});

// ── Redirect root to dashboard ──
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});
