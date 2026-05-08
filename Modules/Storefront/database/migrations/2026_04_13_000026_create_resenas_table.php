<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la tabla 'resenas'.
 * Reseñas de productos por clientes con moderación.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resenas', function (Blueprint $table) {
            $table->increments('id_resena');
            $table->unsignedInteger('id_producto');
            $table->unsignedInteger('id_cliente');
            $table->tinyInteger('calificacion');
            $table->text('comentario')->nullable();
            $table->enum('estado', ['Pendiente', 'Aprobado', 'Oculto'])->default('Aprobado');
            $table->timestamp('fecha_creacion')->useCurrent();

            $table->foreign('id_producto')
                ->references('id_producto')->on('productos')
                ->onDelete('cascade');
            $table->foreign('id_cliente')
                ->references('id_cliente')->on('clientes')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};
