<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->modules() as $module) {
            DB::table('modulos_sistema')->updateOrInsert(
                ['id_modulo' => $module['id_modulo']],
                [
                    'nombre' => $module['nombre'],
                    'descripcion' => $module['descripcion'],
                    'estado' => 'Activo',
                ]
            );
        }

        foreach ($this->roles() as $role) {
            DB::table('roles_sistema')->updateOrInsert(
                ['id_rol' => $role['id_rol']],
                [
                    'nombre_rol' => $role['nombre_rol'],
                    'nivel_acceso' => $role['nivel_acceso'],
                    'estado' => 'Activo',
                ]
            );
        }

        foreach ($this->permissions() as $roleId => $modules) {
            foreach ($modules as $moduleId => $permissions) {
                DB::table('permisos_por_rol')->updateOrInsert(
                    ['id_rol' => $roleId, 'id_modulo' => $moduleId],
                    $permissions
                );
            }
        }

        DB::table('usuarios_internos')->where('id_rol', 4)->update(['id_rol' => 3]);
        DB::table('permisos_por_rol')->where('id_rol', 4)->delete();
        DB::table('roles_sistema')->where('id_rol', 4)->delete();
    }

    public function down(): void
    {
        // Access catalog remains valid for the refactored application.
    }

    private function modules(): array
    {
        return [
            ['id_modulo' => 1, 'nombre' => 'Pedidos', 'descripcion' => 'Bandeja de pedidos WhatsApp y cambios de estado'],
            ['id_modulo' => 2, 'nombre' => 'Catalogo', 'descripcion' => 'Productos, presentaciones, precios, fotos y stock'],
            ['id_modulo' => 3, 'nombre' => 'Tienda Virtual', 'descripcion' => 'Banners, zonas de delivery, promociones y vitrina web'],
            ['id_modulo' => 4, 'nombre' => 'Reportes', 'descripcion' => 'Metricas y exportaciones de pedidos WhatsApp'],
            ['id_modulo' => 5, 'nombre' => 'Configuracion', 'descripcion' => 'Datos comerciales, apariencia y ajustes del sistema'],
            ['id_modulo' => 6, 'nombre' => 'Usuarios', 'descripcion' => 'Usuarios, roles y permisos internos'],
        ];
    }

    private function roles(): array
    {
        return [
            ['id_rol' => 1, 'nombre_rol' => 'Superadministrador', 'nivel_acceso' => 1],
            ['id_rol' => 2, 'nombre_rol' => 'Administrador', 'nivel_acceso' => 2],
            ['id_rol' => 3, 'nombre_rol' => 'Operador', 'nivel_acceso' => 3],
        ];
    }

    private function permissions(): array
    {
        $all = ['leer' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 1];
        $readOnly = ['leer' => 1, 'crear' => 0, 'editar' => 0, 'eliminar' => 0];
        $none = ['leer' => 0, 'crear' => 0, 'editar' => 0, 'eliminar' => 0];

        return [
            1 => array_fill(1, 6, $all),
            2 => array_fill(1, 6, $all),
            3 => [
                1 => ['leer' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 0],
                2 => $readOnly,
                3 => ['leer' => 1, 'crear' => 0, 'editar' => 1, 'eliminar' => 0],
                4 => $readOnly,
                5 => $none,
                6 => $none,
            ],
        ];
    }
};
