<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la tabla 'permisos_por_usuario'.
 * Permite sobreescribir permisos del rol a nivel de usuario individual.
 * Los valores NULL significan "hereda del rol".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos_por_usuario', function (Blueprint $table) {
            $table->unsignedInteger('id_usuario');
            $table->unsignedInteger('id_modulo');
            $table->boolean('leer')->nullable();
            $table->boolean('crear')->nullable();
            $table->boolean('editar')->nullable();
            $table->boolean('eliminar')->nullable();

            $table->primary(['id_usuario', 'id_modulo']);
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios_internos')->onDelete('cascade');
            $table->foreign('id_modulo')->references('id_modulo')->on('modulos_sistema')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos_por_usuario');
    }
};
