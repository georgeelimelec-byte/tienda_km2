<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la tabla 'productos'.
 * Producto base con nombre y categoría. Las variantes/presentaciones se manejan aparte.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->increments('id_producto');
            $table->unsignedInteger('id_categoria');
            $table->string('nombre_base', 150);
            $table->text('descripcion')->nullable();
            $table->string('imagen_url', 255)->nullable();
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');

            $table->foreign('id_categoria')
                ->references('id_categoria')->on('categorias');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
