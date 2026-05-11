<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para la tabla 'clientes_web'.
 * Clientes de tienda virtual y pedidos por WhatsApp.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes_web', function (Blueprint $table) {
            $table->increments('id_cliente');
            $table->enum('tipo_documento', ['DNI', 'RUC', 'CE', 'Sin Documento'])->default('Sin Documento');
            $table->string('numero_documento', 15)->nullable();
            $table->string('nombre_o_razon_social', 150);
            $table->text('direccion')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('celular', 20)->unique()->nullable();
            $table->string('password')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();

            $table->index('numero_documento', 'idx_documento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes_web');
    }
};
