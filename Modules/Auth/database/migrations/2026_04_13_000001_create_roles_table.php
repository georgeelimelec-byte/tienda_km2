<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la tabla 'roles_sistema'.
 * Define los roles del sistema administrativo de la tienda WhatsApp.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles_sistema', function (Blueprint $table) {
            $table->increments('id_rol');
            $table->string('nombre_rol', 50);
            $table->integer('nivel_acceso');
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles_sistema');
    }
};
