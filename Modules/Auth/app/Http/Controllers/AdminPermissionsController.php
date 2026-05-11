<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Auth\Models\Modulo;
use Modules\Auth\Models\PermisoRol;
use Modules\Auth\Models\PermisoUsuario;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Usuario;

class AdminPermissionsController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::with(['permisos'])->orderBy('nivel_acceso')->orderBy('nombre_rol')->get();
        $modules = Modulo::where('estado', 'Activo')->orderBy('id_modulo')->get();
        $users = Usuario::with('role')->where('estado', 'Activo')->orderBy('nombres')->get();
        $selectedUser = null;

        if ($users->isNotEmpty()) {
            $selectedUserId = $request->integer('usuario_id') ?: $users->first()->id_usuario;
            $selectedUser = Usuario::with(['role', 'permisosUsuario'])
                ->where('id_usuario', $selectedUserId)
                ->first();
        }

        return view('auth::admin.permissions.index', compact('roles', 'modules', 'users', 'selectedUser'));
    }

    public function updateRole(Request $request, int $roleId)
    {
        $role = Role::findOrFail($roleId);
        $modules = Modulo::where('estado', 'Activo')->get();

        foreach ($modules as $module) {
            $modulePermissions = $request->input("permissions.{$module->id_modulo}", []);

            DB::table('permisos_por_rol')->updateOrInsert(
                ['id_rol' => $role->id_rol, 'id_modulo' => $module->id_modulo],
                [
                    'leer' => !empty($modulePermissions['leer']),
                    'crear' => !empty($modulePermissions['crear']),
                    'editar' => !empty($modulePermissions['editar']),
                    'eliminar' => !empty($modulePermissions['eliminar']),
                ]
            );
        }

        return back()->with('success', 'Permisos por rol actualizados correctamente.');
    }

    public function updateUser(Request $request, int $userId)
    {
        $user = Usuario::findOrFail($userId);
        $modules = Modulo::where('estado', 'Activo')->get();
        $actions = ['leer', 'crear', 'editar', 'eliminar'];

        foreach ($modules as $module) {
            $moduleOverrides = $request->input("overrides.{$module->id_modulo}", []);
            $payload = [];

            foreach ($actions as $action) {
                $value = $moduleOverrides[$action] ?? 'inherit';
                $payload[$action] = match ($value) {
                    'allow' => true,
                    'deny' => false,
                    default => null,
                };
            }

            if (collect($payload)->filter(fn ($value) => $value !== null)->isEmpty()) {
                DB::table('permisos_por_usuario')
                    ->where('id_usuario', $user->id_usuario)
                    ->where('id_modulo', $module->id_modulo)
                    ->delete();
                continue;
            }

            DB::table('permisos_por_usuario')->updateOrInsert(
                ['id_usuario' => $user->id_usuario, 'id_modulo' => $module->id_modulo],
                $payload
            );
        }

        return redirect()->route('admin.permisos.index', ['usuario_id' => $user->id_usuario])
            ->with('success', 'Permisos por usuario actualizados correctamente.');
    }
}
