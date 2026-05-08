<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Models\PermisoRol;
use Modules\Auth\Models\PermisoUsuario;

/**
 * Middleware de verificación de permisos granulares.
 *
 * Uso en rutas:
 *   ->middleware('check.permission:Inventario,editar')
 *   ->middleware('check.permission:Pedidos,editar')
 *
 * Lógica: Primero chequea permisos a nivel usuario (override).
 * Si es NULL, hereda del rol.
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $modulo, string $accion)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('auth.login');
        }

        // Nivel de acceso 1 = Admin General → acceso total
        if ($user->role && $user->role->nivel_acceso === 1) {
            return $next($request);
        }

        // Buscar id_modulo por nombre
        $moduloModel = \Modules\Auth\Models\Modulo::where('nombre', $modulo)->first();

        if (!$moduloModel) {
            abort(403, "Módulo '{$modulo}' no registrado en el sistema.");
        }

        // 1. Verificar permiso a nivel usuario (override)
        $permisoUsuario = PermisoUsuario::where('id_usuario', $user->id_usuario)
            ->where('id_modulo', $moduloModel->id_modulo)
            ->first();

        if ($permisoUsuario && $permisoUsuario->{$accion} !== null) {
            if ($permisoUsuario->{$accion}) {
                return $next($request);
            }
            abort(403, 'No tienes permiso para esta acción.');
        }

        // 2. Heredar del rol
        $permisoRol = PermisoRol::where('id_rol', $user->id_rol)
            ->where('id_modulo', $moduloModel->id_modulo)
            ->first();

        if ($permisoRol && $permisoRol->{$accion}) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para esta acción.');
    }
}
