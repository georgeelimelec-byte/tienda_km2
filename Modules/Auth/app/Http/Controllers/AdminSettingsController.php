<?php

namespace Modules\Auth\Http\Controllers;

use App\Models\EmpresaConfiguracion;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\Auth\Models\PermisoRol;
use Modules\Auth\Models\PermisoUsuario;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Usuario;
use Modules\Storefront\Models\StorefrontSetting;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $company = EmpresaConfiguracion::query()->first() ?: new EmpresaConfiguracion([
            'ruc' => '',
            'razon_social' => '',
            'nombre_comercial' => '',
            'logo_url' => '',
            'direccion_fiscal' => '',
            'telefono_contacto' => '',
            'correo_contacto' => '',
            'ubigeo' => '',
            'porcentaje_igv' => 18.00,
            'moneda' => 'PEN',
            'horario_atencion' => '',
            'mensaje_operativo' => '',
            'estado' => 'Activo',
        ]);

        $storefront = StorefrontSetting::current();
        $roles = Role::query()->withCount('usuarios')->orderBy('nivel_acceso')->get();

        return view('auth::admin.settings.index', [
            'company' => $company,
            'storefront' => $storefront,
            'settingsSummary' => [
                'users_total' => Usuario::count(),
                'users_active' => Usuario::where('estado', 'Activo')->count(),
                'roles_total' => $roles->count(),
                'role_permissions' => PermisoRol::count(),
                'user_overrides' => PermisoUsuario::count(),
            ],
            'roles' => $roles,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'ruc' => ['required', 'string', 'max:11'],
            'razon_social' => ['required', 'string', 'max:150'],
            'nombre_comercial' => ['required', 'string', 'max:150'],
            'direccion_fiscal' => ['required', 'string'],
            'telefono_contacto' => ['nullable', 'string', 'max:20'],
            'correo_contacto' => ['nullable', 'email', 'max:100'],
            'ubigeo' => ['nullable', 'string', 'max:6'],
            'porcentaje_igv' => ['required', 'numeric', 'min:0', 'max:99.99'],
            'moneda' => ['required', 'string', 'max:10'],
            'horario_atencion' => ['nullable', 'string', 'max:160'],
            'mensaje_operativo' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
        ]);

        $company = EmpresaConfiguracion::query()->first() ?: new EmpresaConfiguracion();
        $company->fill($data);
        $company->save();

        return back()->with('success', 'Configuracion general actualizada correctamente.');
    }
}
