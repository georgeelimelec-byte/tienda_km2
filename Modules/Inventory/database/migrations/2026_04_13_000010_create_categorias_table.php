<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la tabla 'categorias_producto'.
 * Soporta categorías jerárquicas (padre-hijo) mediante auto-referencia.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_producto', function (Blueprint $table) {
            $table->increments('id_categoria');
            $table->unsignedInteger('id_categoria_padre')->nullable();
            $table->string('nombre', 100);
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');

            $table->foreign('id_categoria_padre')
                ->references('id_categoria')->on('categorias_producto')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_producto');
    }
};
