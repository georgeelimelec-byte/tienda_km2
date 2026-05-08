<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la tabla 'permisos_rol'.
 * Define qué puede hacer cada rol en cada módulo (CRUD granular).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos_rol', function (Blueprint $table) {
            $table->unsignedInteger('id_rol');
            $table->unsignedInteger('id_modulo');
            $table->boolean('leer')->default(false);
            $table->boolean('crear')->default(false);
            $table->boolean('editar')->default(false);
            $table->boolean('eliminar')->default(false);

            $table->primary(['id_rol', 'id_modulo']);
            $table->foreign('id_rol')->references('id_rol')->on('roles')->onDelete('cascade');
            $table->foreign('id_modulo')->references('id_modulo')->on('modulos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos_rol');
    }
};
