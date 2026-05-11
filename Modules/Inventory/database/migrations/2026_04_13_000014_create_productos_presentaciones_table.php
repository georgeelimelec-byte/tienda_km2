<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migracion para la tabla 'presentaciones_producto'.
 * Es la entidad central del inventario del sistema: cada variante de un producto
 * tiene precio, precio referencial, stock y codigo de barras.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presentaciones_producto', function (Blueprint $table) {
            $table->increments('id_presentacion');
            $table->unsignedInteger('id_producto');
            $table->unsignedInteger('id_unidad')->default(1);
            $table->string('nombre_variante', 100);
            $table->string('codigo_barras', 100)->unique()->nullable();
            $table->decimal('costo_reposicion', 10, 2)->default(0);
            $table->decimal('precio', 10, 2);
            $table->decimal('precio_referencial', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');

            $table->foreign('id_producto')
                ->references('id_producto')->on('productos')
                ->onDelete('restrict');
            $table->foreign('id_unidad')
                ->references('id_unidad')->on('unidades_medida');

            $table->index('codigo_barras', 'idx_barras');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presentaciones_producto');
    }
};
