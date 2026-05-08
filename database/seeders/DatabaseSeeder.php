<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRoles();
        $this->seedModules();
        $this->seedPermissions();
        $this->seedUnits();
        $this->seedAdminUser();

        $this->call([
            \Modules\Inventory\Database\Seeders\InventoryDatabaseSeeder::class,
            \Modules\Storefront\Database\Seeders\StorefrontDatabaseSeeder::class,
        ]);
    }

    private function seedRoles(): void
    {
        foreach ([
            ['id_rol' => 1, 'nombre_rol' => 'Admin General', 'nivel_acceso' => 1, 'estado' => 'Activo'],
            ['id_rol' => 2, 'nombre_rol' => 'Administrador', 'nivel_acceso' => 2, 'estado' => 'Activo'],
            ['id_rol' => 3, 'nombre_rol' => 'Atencion WhatsApp', 'nivel_acceso' => 3, 'estado' => 'Activo'],
            ['id_rol' => 4, 'nombre_rol' => 'Operador WhatsApp', 'nivel_acceso' => 4, 'estado' => 'Activo'],
        ] as $role) {
            DB::table('roles')->updateOrInsert(['id_rol' => $role['id_rol']], $role);
        }
    }

    private function seedModules(): void
    {
        foreach ([
            ['id_modulo' => 1, 'nombre' => 'Pedidos', 'descripcion' => 'Bandeja de pedidos WhatsApp y cambios de estado', 'estado' => 'Activo'],
            ['id_modulo' => 2, 'nombre' => 'Catalogo', 'descripcion' => 'Productos, presentaciones, precios, fotos y stock directo', 'estado' => 'Activo'],
            ['id_modulo' => 3, 'nombre' => 'Tienda Virtual', 'descripcion' => 'Banners, zonas de delivery, promociones y vitrina web', 'estado' => 'Activo'],
            ['id_modulo' => 4, 'nombre' => 'Reportes', 'descripcion' => 'Metricas y exportaciones de pedidos WhatsApp', 'estado' => 'Activo'],
            ['id_modulo' => 5, 'nombre' => 'Configuracion', 'descripcion' => 'Datos comerciales, apariencia y ajustes del sistema', 'estado' => 'Activo'],
            ['id_modulo' => 6, 'nombre' => 'Usuarios', 'descripcion' => 'Usuarios, roles y permisos internos', 'estado' => 'Activo'],
        ] as $module) {
            DB::table('modulos')->updateOrInsert(['id_modulo' => $module['id_modulo']], $module);
        }
    }

    private function seedPermissions(): void
    {
        for ($moduleId = 1; $moduleId <= 6; $moduleId++) {
            DB::table('permisos_rol')->updateOrInsert(
                ['id_rol' => 1, 'id_modulo' => $moduleId],
                ['leer' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 1]
            );
        }

        foreach ([
            ['id_rol' => 2, 'id_modulo' => 1, 'leer' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 0],
            ['id_rol' => 2, 'id_modulo' => 2, 'leer' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 0],
            ['id_rol' => 2, 'id_modulo' => 3, 'leer' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 0],
            ['id_rol' => 2, 'id_modulo' => 4, 'leer' => 1, 'crear' => 0, 'editar' => 0, 'eliminar' => 0],
            ['id_rol' => 2, 'id_modulo' => 6, 'leer' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 0],
            ['id_rol' => 3, 'id_modulo' => 1, 'leer' => 1, 'crear' => 1, 'editar' => 0, 'eliminar' => 0],
            ['id_rol' => 3, 'id_modulo' => 2, 'leer' => 1, 'crear' => 1, 'editar' => 0, 'eliminar' => 0],
            ['id_rol' => 4, 'id_modulo' => 1, 'leer' => 1, 'crear' => 0, 'editar' => 1, 'eliminar' => 0],
            ['id_rol' => 4, 'id_modulo' => 4, 'leer' => 1, 'crear' => 0, 'editar' => 0, 'eliminar' => 0],
        ] as $permission) {
            DB::table('permisos_rol')->updateOrInsert(
                ['id_rol' => $permission['id_rol'], 'id_modulo' => $permission['id_modulo']],
                $permission
            );
        }
    }

    private function seedUnits(): void
    {
        foreach ([
            ['id_unidad' => 1, 'nombre' => 'Unidad', 'abreviatura' => 'UND', 'estado' => 'Activo'],
            ['id_unidad' => 2, 'nombre' => 'Litro', 'abreviatura' => 'LT', 'estado' => 'Activo'],
            ['id_unidad' => 3, 'nombre' => 'Kilogramo', 'abreviatura' => 'KG', 'estado' => 'Activo'],
            ['id_unidad' => 4, 'nombre' => 'Pack', 'abreviatura' => 'PK', 'estado' => 'Activo'],
            ['id_unidad' => 5, 'nombre' => 'Gramos', 'abreviatura' => 'GR', 'estado' => 'Activo'],
        ] as $unit) {
            DB::table('unidades_medida')->updateOrInsert(['id_unidad' => $unit['id_unidad']], $unit);
        }
    }

    private function seedAdminUser(): void
    {
        DB::table('usuarios')->updateOrInsert(
            ['id_usuario' => 1],
            [
                'id_rol' => 1,
                'nombres' => 'Super Admin',
                'email' => 'admin@ponteready.com',
                'password_hash' => bcrypt('admin123'),
                'foto_url' => 'default-user.png',
                'estado' => 'Activo',
            ]
        );
    }
}
