<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles_sistema')) {
            return;
        }

        if (Schema::hasTable('usuarios_internos')) {
            DB::table('usuarios_internos')->where('id_rol', 4)->update(['id_rol' => 3]);
            DB::table('usuarios_internos')
                ->whereIn('email', ['admin@km2.com', 'vendedora@localmarket.com'])
                ->delete();
        }

        if (Schema::hasTable('permisos_por_rol')) {
            DB::table('permisos_por_rol')->where('id_rol', 4)->delete();
        }

        DB::table('roles_sistema')->where('id_rol', 4)->delete();
        DB::table('roles_sistema')->updateOrInsert(
            ['id_rol' => 1],
            ['nombre_rol' => 'Superadministrador', 'nivel_acceso' => 1, 'estado' => 'Activo']
        );
        DB::table('roles_sistema')->updateOrInsert(
            ['id_rol' => 2],
            ['nombre_rol' => 'Administrador', 'nivel_acceso' => 2, 'estado' => 'Activo']
        );
        DB::table('roles_sistema')->updateOrInsert(
            ['id_rol' => 3],
            ['nombre_rol' => 'Operador', 'nivel_acceso' => 3, 'estado' => 'Activo']
        );
    }

    public function down(): void
    {
        // The pilot scope keeps only Superadministrador, Administrador and Operador.
    }
};
