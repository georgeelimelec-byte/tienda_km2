<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Models\PermisoRol;
use Modules\Auth\Models\PermisoUsuario;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Usuario;
use Modules\Storefront\Models\StorefrontSetting;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $storefront = StorefrontSetting::current();
        $roles = Role::query()->withCount('usuarios')->orderBy('nivel_acceso')->get();

        return view('auth::admin.settings.index', [
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
            'whatsapp_number' => ['required', 'string', 'max:24'],
            'contact_phone' => ['nullable', 'string', 'max:24'],
            'contact_email' => ['nullable', 'email', 'max:120'],
            'currency' => ['required', 'string', 'max:10'],
            'included_tax_percent' => ['required', 'numeric', 'min:0', 'max:99.99'],
            'business_hours' => ['nullable', 'string', 'max:160'],
            'operational_message' => ['nullable', 'string'],
            'control_stock_habilitado' => ['nullable', 'boolean'],
        ], [], [
            'whatsapp_number' => 'numero de WhatsApp de atencion',
            'contact_phone' => 'telefono publico',
        ]);

        $data['control_stock_habilitado'] = $request->boolean('control_stock_habilitado');

        StorefrontSetting::current()->fill($data)->save();

        return back()->with('success', 'Configuracion operativa actualizada correctamente.');
    }
}
