<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zonas_delivery', function (Blueprint $table) {
            $table->increments('id_zona');
            $table->string('nombre', 100);
            $table->decimal('tarifa', 10, 2)->default(0);
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zonas_delivery');
    }
};
