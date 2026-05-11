<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('configuracion_tienda')
            || Schema::hasColumn('configuracion_tienda', 'control_stock_habilitado')) {
            return;
        }

        Schema::table('configuracion_tienda', function (Blueprint $table) {
            $table->boolean('control_stock_habilitado')
                ->default(true)
                ->after('operational_message');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('configuracion_tienda')
            || ! Schema::hasColumn('configuracion_tienda', 'control_stock_habilitado')) {
            return;
        }

        Schema::table('configuracion_tienda', function (Blueprint $table) {
            $table->dropColumn('control_stock_habilitado');
        });
    }
};
