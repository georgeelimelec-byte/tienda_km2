<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Usuario;

class AdminUsersController extends Controller
{
    public function index(Request $request)
    {
        $users = Usuario::with('role')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->q);

                $query->where(function ($subQuery) use ($term) {
                    $subQuery->where('nombres', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('rol'), fn ($query) => $query->where('id_rol', $request->integer('rol')))
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->estado))
            ->orderByDesc('id_usuario')
            ->paginate(12)
            ->withQueryString();

        return view('auth::admin.users.index', [
            'users' => $users,
            'roles' => Role::withCount('usuarios')->orderBy('nivel_acceso')->orderBy('nombre_rol')->get(),
            'activeUsers' => Usuario::where('estado', 'Activo')->count(),
        ]);
    }

    public function create()
    {
        return view('auth::admin.users.form', [
            'user' => new Usuario(),
            'roles' => Role::where('estado', 'Activo')->orderBy('nivel_acceso')->orderBy('nombre_rol')->get(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        Usuario::create([
            'id_rol' => $data['id_rol'],
            'nombres' => $data['nombres'],
            'email' => $data['email'] ?: null,
            'password_hash' => Hash::make($data['password']),
            'foto_url' => $data['foto_url'] ?: 'default-user.png',
            'estado' => $data['estado'],
        ]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(int $id)
    {
        return view('auth::admin.users.form', [
            'user' => Usuario::findOrFail($id),
            'roles' => Role::where('estado', 'Activo')->orderBy('nivel_acceso')->orderBy('nombre_rol')->get(),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $user = Usuario::findOrFail($id);
        $data = $this->validatedData($request, $user->id_usuario);

        $payload = [
            'id_rol' => $data['id_rol'],
            'nombres' => $data['nombres'],
            'email' => $data['email'] ?: null,
            'foto_url' => $data['foto_url'] ?: 'default-user.png',
            'estado' => $data['estado'],
        ];

        if (!empty($data['password'])) {
            $payload['password_hash'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        $user = Usuario::with('role')->findOrFail($id);

        if ((int) Auth::id() === $user->id_usuario) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        if ((int) $user->id_rol === 1 && Usuario::where('id_rol', 1)->count() <= 1) {
            return back()->with('error', 'Debe existir al menos un Admin General activo en el sistema.');
        }

        try {
            $user->delete();
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo eliminar el usuario porque tiene pedidos WhatsApp asociados.');
        }

        return back()->with('success', 'Usuario eliminado correctamente.');
    }

    private function validatedData(Request $request, ?int $userId = null): array
    {
        $passwordRules = $userId
            ? ['nullable', 'string', 'min:6', 'confirmed']
            : ['required', 'string', 'min:6', 'confirmed'];

        return $request->validate([
            'id_rol' => ['required', 'integer', Rule::exists('roles_sistema', 'id_rol')],
            'nombres' => ['required', 'string', 'max:100'],
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('usuarios_internos', 'email')->ignore($userId, 'id_usuario'),
            ],
            'password' => $passwordRules,
            'foto_url' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
        ]);
    }
}
