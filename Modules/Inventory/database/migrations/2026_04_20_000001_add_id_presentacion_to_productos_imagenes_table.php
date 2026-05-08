<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('productos_imagenes', 'id_presentacion')) {
            Schema::table('productos_imagenes', function (Blueprint $table) {
                $table->unsignedInteger('id_presentacion')->nullable()->after('id_producto');
                $table->index('id_presentacion', 'productos_imagenes_presentacion_idx');
                $table->foreign('id_presentacion', 'productos_imagenes_presentacion_fk')
                    ->references('id_presentacion')
                    ->on('productos_presentaciones')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('productos_imagenes', 'id_presentacion')) {
            Schema::table('productos_imagenes', function (Blueprint $table) {
                $table->dropForeign('productos_imagenes_presentacion_fk');
                $table->dropIndex('productos_imagenes_presentacion_idx');
                $table->dropColumn('id_presentacion');
            });
        }
    }
};
