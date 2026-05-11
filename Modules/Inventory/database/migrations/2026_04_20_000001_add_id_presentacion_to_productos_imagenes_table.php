<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('imagenes_producto', 'id_presentacion')) {
            Schema::table('imagenes_producto', function (Blueprint $table) {
                $table->unsignedInteger('id_presentacion')->nullable()->after('id_producto');
                $table->index('id_presentacion', 'imagenes_producto_presentacion_idx');
                $table->foreign('id_presentacion', 'imagenes_producto_presentacion_fk')
                    ->references('id_presentacion')
                    ->on('presentaciones_producto')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('imagenes_producto', 'id_presentacion')) {
            Schema::table('imagenes_producto', function (Blueprint $table) {
                $table->dropForeign('imagenes_producto_presentacion_fk');
                $table->dropIndex('imagenes_producto_presentacion_idx');
                $table->dropColumn('id_presentacion');
            });
        }
    }
};
