<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles_sistema')->updateOrInsert(
            ['id_rol' => 4],
            [
                'nombre_rol' => 'Operador WhatsApp',
                'nivel_acceso' => 4,
                'estado' => 'Activo',
            ]
        );

        $existingModuleIds = DB::table('modulos_sistema')
            ->whereIn('id_modulo', [1, 2, 3, 4, 5, 6])
            ->pluck('id_modulo')
            ->all();

        foreach ([
            1 => ['leer' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 0],
            2 => ['leer' => 1, 'crear' => 0, 'editar' => 0, 'eliminar' => 0],
            3 => ['leer' => 1, 'crear' => 0, 'editar' => 1, 'eliminar' => 0],
            4 => ['leer' => 1, 'crear' => 0, 'editar' => 0, 'eliminar' => 0],
            5 => ['leer' => 0, 'crear' => 0, 'editar' => 0, 'eliminar' => 0],
            6 => ['leer' => 0, 'crear' => 0, 'editar' => 0, 'eliminar' => 0],
        ] as $moduleId => $permissions) {
            if (!in_array($moduleId, $existingModuleIds, true)) {
                continue;
            }

            DB::table('permisos_por_rol')->updateOrInsert(
                ['id_rol' => 4, 'id_modulo' => $moduleId],
                $permissions
            );
        }
    }

    public function down(): void
    {
        DB::table('permisos_por_rol')->where('id_rol', 4)->delete();
        DB::table('roles_sistema')->where('id_rol', 4)->delete();
    }
};
