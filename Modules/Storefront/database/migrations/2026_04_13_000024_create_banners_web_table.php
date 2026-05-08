<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la tabla 'banners_web'.
 * Banners promocionales para la tienda virtual (carrusel, lateral, pop-up).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners_web', function (Blueprint $table) {
            $table->increments('id_banner');
            $table->string('titulo', 100)->nullable();
            $table->string('imagen_url', 255);
            $table->string('link_destino', 255)->nullable();
            $table->enum('posicion', ['Carrusel', 'Lateral', 'Pop_up'])->default('Carrusel');
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners_web');
    }
};
