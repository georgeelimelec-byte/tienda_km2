<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pedidos_whatsapp')
            && Schema::hasColumn('pedidos_whatsapp', 'comprobante_referencia')
            && ! Schema::hasColumn('pedidos_whatsapp', 'referencia_atencion')) {
            Schema::table('pedidos_whatsapp', function (Blueprint $table) {
                $table->renameColumn('comprobante_referencia', 'referencia_atencion');
            });
        }

        if (! Schema::hasTable('pedidos_whatsapp_detalles')
            || ! Schema::hasColumn('pedidos_whatsapp_detalles', 'cantidad')) {
            return;
        }

        if (Schema::hasColumn('pedidos_whatsapp_detalles', 'cantidad_solicitada')) {
            DB::table('pedidos_whatsapp_detalles')
                ->where('cantidad_solicitada', 0)
                ->update(['cantidad_solicitada' => DB::raw('cantidad')]);
        }

        if (Schema::hasColumn('pedidos_whatsapp_detalles', 'cantidad_confirmada')) {
            DB::table('pedidos_whatsapp_detalles')
                ->where('cantidad_confirmada', 0)
                ->update(['cantidad_confirmada' => DB::raw('cantidad')]);
        }

        Schema::table('pedidos_whatsapp_detalles', function (Blueprint $table) {
            $table->dropColumn('cantidad');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('pedidos_whatsapp')
            && Schema::hasColumn('pedidos_whatsapp', 'referencia_atencion')
            && ! Schema::hasColumn('pedidos_whatsapp', 'comprobante_referencia')) {
            Schema::table('pedidos_whatsapp', function (Blueprint $table) {
                $table->renameColumn('referencia_atencion', 'comprobante_referencia');
            });
        }

        if (! Schema::hasTable('pedidos_whatsapp_detalles')
            || Schema::hasColumn('pedidos_whatsapp_detalles', 'cantidad')) {
            return;
        }

        Schema::table('pedidos_whatsapp_detalles', function (Blueprint $table) {
            $table->integer('cantidad')->default(0)->after('cantidad_confirmada');
        });

        DB::table('pedidos_whatsapp_detalles')
            ->update(['cantidad' => DB::raw('cantidad_confirmada')]);
    }
};
