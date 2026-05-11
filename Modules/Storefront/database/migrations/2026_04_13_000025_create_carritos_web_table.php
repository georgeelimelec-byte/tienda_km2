<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('carrito_items')) {
            return;
        }

        if (Schema::hasTable('carrito_compras_web')) {
            Schema::rename('carrito_compras_web', 'carrito_items');
            return;
        }

        Schema::create('carrito_items', function (Blueprint $table) {
            $table->increments('id_carrito');
            $table->unsignedInteger('id_cliente');
            $table->unsignedInteger('id_presentacion');
            $table->unsignedInteger('cantidad')->default(1);

            $table->unique(['id_cliente', 'id_presentacion'], 'carrito_cliente_presentacion_unique');

            $table->foreign('id_cliente', 'carrito_items_id_cliente_foreign')
                ->references('id_cliente')
                ->on('clientes_web')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('id_presentacion', 'carrito_items_id_presentacion_foreign')
                ->references('id_presentacion')
                ->on('presentaciones_producto')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrito_items');
    }
};
