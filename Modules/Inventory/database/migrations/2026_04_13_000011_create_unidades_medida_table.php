<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la tabla 'unidades_medida'.
 * Define las unidades de medida (UND, LT, KG, PK, GR).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unidades_medida', function (Blueprint $table) {
            $table->increments('id_unidad');
            $table->string('nombre', 50);
            $table->string('abreviatura', 10);
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unidades_medida');
    }
};
