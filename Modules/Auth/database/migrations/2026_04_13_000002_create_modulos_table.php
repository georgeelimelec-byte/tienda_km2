<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la tabla 'modulos'.
 * Define los módulos del sistema para control de permisos granular.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modulos', function (Blueprint $table) {
            $table->increments('id_modulo');
            $table->string('nombre', 50);
            $table->string('descripcion', 150)->nullable();
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modulos');
    }
};
