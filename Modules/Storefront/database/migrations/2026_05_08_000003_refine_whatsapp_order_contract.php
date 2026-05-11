<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pedidos_tienda')
            && Schema::hasColumn('pedidos_tienda', 'comprobante_referencia')
            && ! Schema::hasColumn('pedidos_tienda', 'referencia_atencion')) {
            Schema::table('pedidos_tienda', function (Blueprint $table) {
                $table->renameColumn('comprobante_referencia', 'referencia_atencion');
            });
        }

        if (! Schema::hasTable('detalle_pedidos_tienda')
            || ! Schema::hasColumn('detalle_pedidos_tienda', 'cantidad')) {
            return;
        }

        if (Schema::hasColumn('detalle_pedidos_tienda', 'cantidad_solicitada')) {
            DB::table('detalle_pedidos_tienda')
                ->where('cantidad_solicitada', 0)
                ->update(['cantidad_solicitada' => DB::raw('cantidad')]);
        }

        if (Schema::hasColumn('detalle_pedidos_tienda', 'cantidad_confirmada')) {
            DB::table('detalle_pedidos_tienda')
                ->where('cantidad_confirmada', 0)
                ->update(['cantidad_confirmada' => DB::raw('cantidad')]);
        }

        Schema::table('detalle_pedidos_tienda', function (Blueprint $table) {
            $table->dropColumn('cantidad');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('pedidos_tienda')
            && Schema::hasColumn('pedidos_tienda', 'referencia_atencion')
            && ! Schema::hasColumn('pedidos_tienda', 'comprobante_referencia')) {
            Schema::table('pedidos_tienda', function (Blueprint $table) {
                $table->renameColumn('referencia_atencion', 'comprobante_referencia');
            });
        }

        if (! Schema::hasTable('detalle_pedidos_tienda')
            || Schema::hasColumn('detalle_pedidos_tienda', 'cantidad')) {
            return;
        }

        Schema::table('detalle_pedidos_tienda', function (Blueprint $table) {
            $table->integer('cantidad')->default(0)->after('cantidad_confirmada');
        });

        DB::table('detalle_pedidos_tienda')
            ->update(['cantidad' => DB::raw('cantidad_confirmada')]);
    }
};
