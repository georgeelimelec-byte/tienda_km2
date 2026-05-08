<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carrito_compras_web', function (Blueprint $table) {
            $table->increments('id_carrito');
            $table->unsignedInteger('id_cliente');
            $table->unsignedInteger('id_presentacion');
            $table->unsignedInteger('cantidad')->default(1);

            $table->unique(['id_cliente', 'id_presentacion'], 'carrito_cliente_presentacion_unique');

            $table->foreign('id_cliente')
                ->references('id_cliente')
                ->on('clientes')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('id_presentacion')
                ->references('id_presentacion')
                ->on('productos_presentaciones')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrito_compras_web');
    }
};
