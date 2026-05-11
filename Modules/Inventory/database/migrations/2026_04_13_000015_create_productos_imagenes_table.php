<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la tabla 'imagenes_producto'.
 * Galería de imágenes para la tienda virtual (múltiples por producto).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imagenes_producto', function (Blueprint $table) {
            $table->increments('id_imagen');
            $table->unsignedInteger('id_producto');
            $table->string('imagen_url', 255);
            $table->integer('orden')->default(0);

            $table->foreign('id_producto')
                ->references('id_producto')->on('productos')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imagenes_producto');
    }
};
