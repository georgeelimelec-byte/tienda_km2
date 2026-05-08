<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresa_configuracion', function (Blueprint $table) {
            if (!Schema::hasColumn('empresa_configuracion', 'moneda')) {
                $table->string('moneda', 10)->default('PEN')->after('porcentaje_igv');
            }

            if (!Schema::hasColumn('empresa_configuracion', 'horario_atencion')) {
                $table->string('horario_atencion', 160)->nullable()->after('moneda');
            }

            if (!Schema::hasColumn('empresa_configuracion', 'mensaje_operativo')) {
                $table->text('mensaje_operativo')->nullable()->after('horario_atencion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('empresa_configuracion', function (Blueprint $table) {
            if (Schema::hasColumn('empresa_configuracion', 'mensaje_operativo')) {
                $table->dropColumn('mensaje_operativo');
            }

            if (Schema::hasColumn('empresa_configuracion', 'horario_atencion')) {
                $table->dropColumn('horario_atencion');
            }

            if (Schema::hasColumn('empresa_configuracion', 'moneda')) {
                $table->dropColumn('moneda');
            }
        });
    }
};
