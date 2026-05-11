<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pedidos originados desde la tienda virtual y confirmados por WhatsApp.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos_tienda', function (Blueprint $table) {
            $table->increments('id_pedido_whatsapp');
            $table->string('codigo_pedido', 24)->unique();
            $table->string('cliente_nombre', 120);
            $table->string('cliente_whatsapp', 24);
            $table->text('cliente_direccion')->nullable();
            $table->text('cliente_referencia')->nullable();
            $table->unsignedInteger('id_zona_delivery')->nullable();
            $table->decimal('total_productos', 12, 2)->default(0);
            $table->decimal('costo_delivery', 12, 2)->default(0);
            $table->decimal('total_pedido', 12, 2)->default(0);
            $table->string('estado', 30)->default('Pendiente');
            $table->string('whatsapp_url', 500)->nullable();
            $table->string('referencia_atencion', 80)->nullable();
            $table->text('nota_interna')->nullable();
            $table->unsignedInteger('id_operador')->nullable();
            $table->timestamps();

            $table->foreign('id_zona_delivery')
                ->references('id_zona')->on('zonas_entrega')
                ->nullOnDelete();

            $table->foreign('id_operador')
                ->references('id_usuario')->on('usuarios_internos')
                ->nullOnDelete();
        });

        Schema::create('detalle_pedidos_tienda', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->unsignedInteger('id_pedido_whatsapp');
            $table->unsignedInteger('id_producto');
            $table->unsignedInteger('id_presentacion')->nullable();
            $table->string('nombre_producto', 180);
            $table->decimal('precio_unitario', 12, 2);
            $table->integer('cantidad_solicitada')->default(0);
            $table->integer('cantidad_confirmada')->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->text('motivo_ajuste')->nullable();
            $table->string('estado_item', 30)->default('Solicitado');
            $table->timestamps();

            $table->foreign('id_pedido_whatsapp')
                ->references('id_pedido_whatsapp')->on('pedidos_tienda')
                ->cascadeOnDelete();

            $table->foreign('id_producto')
                ->references('id_producto')->on('productos')
                ->restrictOnDelete();

            $table->foreign('id_presentacion')
                ->references('id_presentacion')->on('presentaciones_producto')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_pedidos_tienda');
        Schema::dropIfExists('pedidos_tienda');
    }
};
