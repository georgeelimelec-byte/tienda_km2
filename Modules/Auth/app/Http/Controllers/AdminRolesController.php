<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Usuario;

class AdminRolesController extends Controller
{
    public function index()
    {
        return view('auth::admin.roles.index', [
            'roles' => Role::withCount('usuarios')->orderBy('nivel_acceso')->orderBy('nombre_rol')->get(),
        ]);
    }

    public function create()
    {
        return view('auth::admin.roles.form', [
            'role' => new Role(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        Role::create($data);

        return redirect()->route('admin.roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function edit(int $id)
    {
        return view('auth::admin.roles.form', [
            'role' => Role::findOrFail($id),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $role = Role::findOrFail($id);
        $data = $this->validatedData($request, $role->id_rol);

        if ((int) $role->id_rol === 1) {
            $data['nivel_acceso'] = 1;
            $data['estado'] = 'Activo';
        }

        $role->update($data);

        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function assign(Request $request)
    {
        $data = $request->validate([
            'id_usuario' => ['required', 'integer', Rule::exists('usuarios_internos', 'id_usuario')],
            'id_rol' => ['required', 'integer', Rule::exists('roles_sistema', 'id_rol')],
        ]);

        $user = Usuario::findOrFail($data['id_usuario']);
        $user->update(['id_rol' => $data['id_rol']]);

        return back()->with('success', 'Rol asignado correctamente.');
    }

    public function destroy(int $id)
    {
        $role = Role::withCount('usuarios')->findOrFail($id);

        if ((int) $role->id_rol === 1) {
            return back()->with('error', 'El rol Superadministrador no se puede eliminar.');
        }

        if ($role->usuarios_count > 0) {
            return back()->with('error', 'No se puede eliminar un rol que todavia tiene usuarios asignados.');
        }

        $role->delete();

        return back()->with('success', 'Rol eliminado correctamente.');
    }

    private function validatedData(Request $request, ?int $roleId = null): array
    {
        return $request->validate([
            'nombre_rol' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles_sistema', 'nombre_rol')->ignore($roleId, 'id_rol'),
            ],
            'nivel_acceso' => ['required', 'integer', 'min:1', 'max:99'],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
        ]);
    }
}
