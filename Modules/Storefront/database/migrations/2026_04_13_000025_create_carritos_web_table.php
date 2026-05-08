<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('carritos_web')) {
            return;
        }

        if (Schema::hasTable('carrito_compras_web')) {
            Schema::rename('carrito_compras_web', 'carritos_web');
            return;
        }

        Schema::create('carritos_web', function (Blueprint $table) {
            $table->increments('id_carrito');
            $table->unsignedInteger('id_cliente');
            $table->unsignedInteger('id_presentacion');
            $table->unsignedInteger('cantidad')->default(1);

            $table->unique(['id_cliente', 'id_presentacion'], 'carrito_cliente_presentacion_unique');

            $table->foreign('id_cliente', 'carritos_web_id_cliente_foreign')
                ->references('id_cliente')
                ->on('clientes')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('id_presentacion', 'carritos_web_id_presentacion_foreign')
                ->references('id_presentacion')
                ->on('productos_presentaciones')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carritos_web');
    }
};
