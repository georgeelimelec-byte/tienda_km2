<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->alignPresentationColumns();
        $this->alignOrderColumns();
        $this->createPromotionTables();
        $this->createAuditTables();
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_stock_web');
        Schema::dropIfExists('auditoria_sistema');
        Schema::dropIfExists('promociones_categorias');
        Schema::dropIfExists('promociones_productos');
        Schema::dropIfExists('promociones');
    }

    private function alignPresentationColumns(): void
    {
        if (! Schema::hasTable('presentaciones_producto')) {
            return;
        }

        if (Schema::hasColumn('presentaciones_producto', 'precio_oferta') && ! Schema::hasColumn('presentaciones_producto', 'precio_referencial')) {
            Schema::table('presentaciones_producto', function (Blueprint $table) {
                $table->decimal('precio_referencial', 10, 2)->nullable()->after('precio');
            });

            DB::table('presentaciones_producto')
                ->whereNotNull('precio_oferta')
                ->whereColumn('precio_oferta', '<', 'precio')
                ->update([
                    'precio_referencial' => DB::raw('precio'),
                    'precio' => DB::raw('precio_oferta'),
                ]);

            DB::table('presentaciones_producto')
                ->whereNotNull('precio_oferta')
                ->whereColumn('precio_oferta', '>=', 'precio')
                ->update(['precio_referencial' => DB::raw('precio_oferta')]);

            Schema::table('presentaciones_producto', function (Blueprint $table) {
                $table->dropColumn('precio_oferta');
            });
        }

        if (Schema::hasColumn('presentaciones_producto', 'stock') && ! Schema::hasColumn('presentaciones_producto', 'stock_web')) {
            Schema::table('presentaciones_producto', function (Blueprint $table) {
                $table->renameColumn('stock', 'stock_web');
            });
        }

        if (Schema::hasColumn('presentaciones_producto', 'stock_minimo') && ! Schema::hasColumn('presentaciones_producto', 'stock_web_minimo')) {
            Schema::table('presentaciones_producto', function (Blueprint $table) {
                $table->renameColumn('stock_minimo', 'stock_web_minimo');
            });
        }
    }

    private function alignOrderColumns(): void
    {
        if (! Schema::hasTable('pedidos_tienda')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pedidos_tienda MODIFY estado VARCHAR(30) NOT NULL DEFAULT 'Pendiente'");
        }

        DB::table('pedidos_tienda')
            ->where('estado', 'En Reparto')
            ->update(['estado' => 'En Delivery']);

        if (Schema::hasTable('detalle_pedidos_tienda')) {
            Schema::table('detalle_pedidos_tienda', function (Blueprint $table) {
                if (! Schema::hasColumn('detalle_pedidos_tienda', 'cantidad_solicitada')) {
                    $table->integer('cantidad_solicitada')->default(0)->after('precio_unitario');
                }

                if (! Schema::hasColumn('detalle_pedidos_tienda', 'cantidad_confirmada')) {
                    $table->integer('cantidad_confirmada')->default(0)->after('cantidad_solicitada');
                }

                if (! Schema::hasColumn('detalle_pedidos_tienda', 'motivo_ajuste')) {
                    $table->text('motivo_ajuste')->nullable()->after('subtotal');
                }

                if (! Schema::hasColumn('detalle_pedidos_tienda', 'estado_item')) {
                    $table->string('estado_item', 30)->default('Solicitado')->after('motivo_ajuste');
                }
            });

            if (Schema::hasColumn('detalle_pedidos_tienda', 'cantidad')) {
                DB::table('detalle_pedidos_tienda')
                    ->where('cantidad_solicitada', 0)
                    ->update([
                        'cantidad_solicitada' => DB::raw('cantidad'),
                        'cantidad_confirmada' => DB::raw('cantidad'),
                    ]);
            }
        }
    }

    private function createPromotionTables(): void
    {
        if (! Schema::hasTable('promociones')) {
            Schema::create('promociones', function (Blueprint $table) {
                $table->increments('id_promocion');
                $table->string('nombre', 120);
                $table->text('descripcion')->nullable();
                $table->enum('tipo_descuento', ['Porcentaje', 'Monto'])->default('Porcentaje');
                $table->decimal('valor_descuento', 10, 2);
                $table->date('fecha_inicio')->nullable();
                $table->date('fecha_fin')->nullable();
                $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('promociones_productos')) {
            Schema::create('promociones_productos', function (Blueprint $table) {
                $table->id('id_promocion_producto');
                $table->unsignedInteger('id_promocion');
                $table->unsignedInteger('id_producto');
                $table->unique(['id_promocion', 'id_producto'], 'promo_producto_unique');
                $table->foreign('id_promocion')->references('id_promocion')->on('promociones')->cascadeOnDelete();
                $table->foreign('id_producto')->references('id_producto')->on('productos')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('promociones_categorias')) {
            Schema::create('promociones_categorias', function (Blueprint $table) {
                $table->id('id_promocion_categoria');
                $table->unsignedInteger('id_promocion');
                $table->unsignedInteger('id_categoria');
                $table->unique(['id_promocion', 'id_categoria'], 'promo_categoria_unique');
                $table->foreign('id_promocion')->references('id_promocion')->on('promociones')->cascadeOnDelete();
                $table->foreign('id_categoria')->references('id_categoria')->on('categorias_producto')->cascadeOnDelete();
            });
        }
    }

    private function createAuditTables(): void
    {
        if (! Schema::hasTable('auditoria_sistema')) {
            Schema::create('auditoria_sistema', function (Blueprint $table) {
                $table->id('id_auditoria');
                $table->unsignedInteger('id_usuario')->nullable();
                $table->string('rol', 80)->nullable();
                $table->string('accion', 80);
                $table->string('entidad', 80);
                $table->unsignedBigInteger('entidad_id')->nullable();
                $table->text('descripcion');
                $table->text('valor_anterior')->nullable();
                $table->text('valor_nuevo')->nullable();
                $table->string('ip', 45)->nullable();
                $table->string('dispositivo', 255)->nullable();
                $table->timestamps();

                $table->foreign('id_usuario')->references('id_usuario')->on('usuarios_internos')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('movimientos_stock_web')) {
            Schema::create('movimientos_stock_web', function (Blueprint $table) {
                $table->id('id_movimiento');
                $table->unsignedInteger('id_presentacion');
                $table->unsignedInteger('id_pedido_whatsapp')->nullable();
                $table->string('tipo_movimiento', 40);
                $table->integer('cantidad');
                $table->integer('stock_anterior');
                $table->integer('stock_nuevo');
                $table->text('motivo')->nullable();
                $table->unsignedInteger('id_usuario')->nullable();
                $table->timestamps();

                $table->foreign('id_presentacion')->references('id_presentacion')->on('presentaciones_producto')->cascadeOnDelete();
                $table->foreign('id_pedido_whatsapp')->references('id_pedido_whatsapp')->on('pedidos_tienda')->nullOnDelete();
                $table->foreign('id_usuario')->references('id_usuario')->on('usuarios_internos')->nullOnDelete();
            });
        }
    }
};
