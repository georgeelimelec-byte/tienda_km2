<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::disableForeignKeyConstraints();

        foreach ($this->foreignKeys() as $foreignKey) {
            $this->normalizeForeignKey($foreignKey);
        }

        foreach ($this->indexes() as $index) {
            $this->renameIndexIfNeeded($index['table'], $index['old'], $index['new']);
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Constraint names are metadata only; keeping the normalized names is intentional.
    }

    private function normalizeForeignKey(array $foreignKey): void
    {
        if (! Schema::hasTable($foreignKey['table'])) {
            return;
        }

        $oldExists = $this->foreignKeyExists($foreignKey['table'], $foreignKey['old']);
        $newExists = $this->foreignKeyExists($foreignKey['table'], $foreignKey['new']);

        if ($oldExists) {
            DB::statement(sprintf(
                'ALTER TABLE %s DROP FOREIGN KEY %s',
                $this->quoteIdentifier($foreignKey['table']),
                $this->quoteIdentifier($foreignKey['old'])
            ));
        }

        $this->renameIndexIfNeeded(
            $foreignKey['table'],
            $foreignKey['old_index'] ?? $foreignKey['old'],
            $foreignKey['new_index'] ?? $foreignKey['new']
        );

        if (! $newExists) {
            Schema::table($foreignKey['table'], function (Blueprint $table) use ($foreignKey) {
                $constraint = $table->foreign($foreignKey['column'], $foreignKey['new'])
                    ->references($foreignKey['references'])
                    ->on($foreignKey['on']);

                match ($foreignKey['on_delete'] ?? null) {
                    'cascade' => $constraint->cascadeOnDelete(),
                    'set null' => $constraint->nullOnDelete(),
                    'restrict' => $constraint->restrictOnDelete(),
                    default => null,
                };

                match ($foreignKey['on_update'] ?? null) {
                    'cascade' => $constraint->cascadeOnUpdate(),
                    'set null' => $constraint->nullOnUpdate(),
                    'restrict' => $constraint->restrictOnUpdate(),
                    default => null,
                };
            });
        }
    }

    private function renameIndexIfNeeded(string $table, string $oldName, string $newName): void
    {
        if (! Schema::hasTable($table)
            || ! $this->indexExists($table, $oldName)
            || $this->indexExists($table, $newName)) {
            return;
        }

        DB::statement(sprintf(
            'ALTER TABLE %s RENAME INDEX %s TO %s',
            $this->quoteIdentifier($table),
            $this->quoteIdentifier($oldName),
            $this->quoteIdentifier($newName)
        ));
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->whereRaw('CONSTRAINT_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();
    }

    private function indexExists(string $table, string $index): bool
    {
        return DB::table('information_schema.STATISTICS')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    private function foreignKeys(): array
    {
        return [
            ['table' => 'auditoria_sistema', 'old' => 'auditoria_operativa_id_usuario_foreign', 'new' => 'auditoria_sistema_id_usuario_foreign', 'column' => 'id_usuario', 'references' => 'id_usuario', 'on' => 'usuarios_internos', 'on_delete' => 'set null'],
            ['table' => 'carrito_items', 'old' => 'carritos_web_id_cliente_foreign', 'new' => 'carrito_items_id_cliente_foreign', 'column' => 'id_cliente', 'references' => 'id_cliente', 'on' => 'clientes_web', 'on_delete' => 'cascade', 'on_update' => 'cascade'],
            ['table' => 'carrito_items', 'old' => 'carritos_web_id_presentacion_foreign', 'new' => 'carrito_items_id_presentacion_foreign', 'column' => 'id_presentacion', 'references' => 'id_presentacion', 'on' => 'presentaciones_producto', 'on_delete' => 'cascade', 'on_update' => 'cascade'],
            ['table' => 'categorias_producto', 'old' => 'categorias_id_categoria_padre_foreign', 'new' => 'categorias_producto_id_categoria_padre_foreign', 'column' => 'id_categoria_padre', 'references' => 'id_categoria', 'on' => 'categorias_producto', 'on_delete' => 'set null'],
            ['table' => 'detalle_pedidos_tienda', 'old' => 'pedidos_whatsapp_detalles_id_pedido_whatsapp_foreign', 'new' => 'detalle_pedidos_tienda_id_pedido_whatsapp_foreign', 'column' => 'id_pedido_whatsapp', 'references' => 'id_pedido_whatsapp', 'on' => 'pedidos_tienda', 'on_delete' => 'cascade'],
            ['table' => 'detalle_pedidos_tienda', 'old' => 'pedidos_whatsapp_detalles_id_producto_foreign', 'new' => 'detalle_pedidos_tienda_id_producto_foreign', 'column' => 'id_producto', 'references' => 'id_producto', 'on' => 'productos', 'on_delete' => 'restrict'],
            ['table' => 'detalle_pedidos_tienda', 'old' => 'pedidos_whatsapp_detalles_id_presentacion_foreign', 'new' => 'detalle_pedidos_tienda_id_presentacion_foreign', 'column' => 'id_presentacion', 'references' => 'id_presentacion', 'on' => 'presentaciones_producto', 'on_delete' => 'set null'],
            ['table' => 'imagenes_producto', 'old' => 'productos_imagenes_id_producto_foreign', 'new' => 'imagenes_producto_id_producto_foreign', 'column' => 'id_producto', 'references' => 'id_producto', 'on' => 'productos', 'on_delete' => 'cascade'],
            ['table' => 'imagenes_producto', 'old' => 'productos_imagenes_presentacion_fk', 'new' => 'imagenes_producto_presentacion_fk', 'old_index' => 'productos_imagenes_presentacion_idx', 'new_index' => 'imagenes_producto_presentacion_idx', 'column' => 'id_presentacion', 'references' => 'id_presentacion', 'on' => 'presentaciones_producto', 'on_delete' => 'cascade'],
            ['table' => 'movimientos_stock_web', 'old' => 'stock_web_movimientos_id_presentacion_foreign', 'new' => 'movimientos_stock_web_id_presentacion_foreign', 'column' => 'id_presentacion', 'references' => 'id_presentacion', 'on' => 'presentaciones_producto', 'on_delete' => 'cascade'],
            ['table' => 'movimientos_stock_web', 'old' => 'stock_web_movimientos_id_pedido_whatsapp_foreign', 'new' => 'movimientos_stock_web_id_pedido_whatsapp_foreign', 'column' => 'id_pedido_whatsapp', 'references' => 'id_pedido_whatsapp', 'on' => 'pedidos_tienda', 'on_delete' => 'set null'],
            ['table' => 'movimientos_stock_web', 'old' => 'stock_web_movimientos_id_usuario_foreign', 'new' => 'movimientos_stock_web_id_usuario_foreign', 'column' => 'id_usuario', 'references' => 'id_usuario', 'on' => 'usuarios_internos', 'on_delete' => 'set null'],
            ['table' => 'pedidos_tienda', 'old' => 'pedidos_whatsapp_id_operador_foreign', 'new' => 'pedidos_tienda_id_operador_foreign', 'column' => 'id_operador', 'references' => 'id_usuario', 'on' => 'usuarios_internos', 'on_delete' => 'set null'],
            ['table' => 'pedidos_tienda', 'old' => 'pedidos_whatsapp_id_zona_delivery_foreign', 'new' => 'pedidos_tienda_id_zona_delivery_foreign', 'column' => 'id_zona_delivery', 'references' => 'id_zona', 'on' => 'zonas_entrega', 'on_delete' => 'set null'],
            ['table' => 'permisos_por_rol', 'old' => 'permisos_rol_id_modulo_foreign', 'new' => 'permisos_por_rol_id_modulo_foreign', 'column' => 'id_modulo', 'references' => 'id_modulo', 'on' => 'modulos_sistema', 'on_delete' => 'cascade'],
            ['table' => 'permisos_por_rol', 'old' => 'permisos_rol_id_rol_foreign', 'new' => 'permisos_por_rol_id_rol_foreign', 'column' => 'id_rol', 'references' => 'id_rol', 'on' => 'roles_sistema', 'on_delete' => 'cascade'],
            ['table' => 'permisos_por_usuario', 'old' => 'permisos_usuario_id_modulo_foreign', 'new' => 'permisos_por_usuario_id_modulo_foreign', 'column' => 'id_modulo', 'references' => 'id_modulo', 'on' => 'modulos_sistema', 'on_delete' => 'cascade'],
            ['table' => 'permisos_por_usuario', 'old' => 'permisos_usuario_id_usuario_foreign', 'new' => 'permisos_por_usuario_id_usuario_foreign', 'column' => 'id_usuario', 'references' => 'id_usuario', 'on' => 'usuarios_internos', 'on_delete' => 'cascade'],
            ['table' => 'presentaciones_producto', 'old' => 'productos_presentaciones_id_producto_foreign', 'new' => 'presentaciones_producto_id_producto_foreign', 'column' => 'id_producto', 'references' => 'id_producto', 'on' => 'productos', 'on_delete' => 'restrict'],
            ['table' => 'presentaciones_producto', 'old' => 'productos_presentaciones_id_unidad_foreign', 'new' => 'presentaciones_producto_id_unidad_foreign', 'column' => 'id_unidad', 'references' => 'id_unidad', 'on' => 'unidades_medida'],
            ['table' => 'promociones_categorias', 'old' => 'promocion_categorias_id_categoria_foreign', 'new' => 'promociones_categorias_id_categoria_foreign', 'column' => 'id_categoria', 'references' => 'id_categoria', 'on' => 'categorias_producto', 'on_delete' => 'cascade'],
            ['table' => 'promociones_categorias', 'old' => 'promocion_categorias_id_promocion_foreign', 'new' => 'promociones_categorias_id_promocion_foreign', 'column' => 'id_promocion', 'references' => 'id_promocion', 'on' => 'promociones', 'on_delete' => 'cascade'],
            ['table' => 'promociones_productos', 'old' => 'promocion_productos_id_producto_foreign', 'new' => 'promociones_productos_id_producto_foreign', 'column' => 'id_producto', 'references' => 'id_producto', 'on' => 'productos', 'on_delete' => 'cascade'],
            ['table' => 'promociones_productos', 'old' => 'promocion_productos_id_promocion_foreign', 'new' => 'promociones_productos_id_promocion_foreign', 'column' => 'id_promocion', 'references' => 'id_promocion', 'on' => 'promociones', 'on_delete' => 'cascade'],
            ['table' => 'usuarios_internos', 'old' => 'usuarios_id_rol_foreign', 'new' => 'usuarios_internos_id_rol_foreign', 'column' => 'id_rol', 'references' => 'id_rol', 'on' => 'roles_sistema', 'on_update' => 'cascade'],
        ];
    }

    private function indexes(): array
    {
        return [
            ['table' => 'clientes_web', 'old' => 'clientes_celular_unique', 'new' => 'clientes_web_celular_unique'],
            ['table' => 'pedidos_tienda', 'old' => 'pedidos_whatsapp_codigo_pedido_unique', 'new' => 'pedidos_tienda_codigo_pedido_unique'],
            ['table' => 'presentaciones_producto', 'old' => 'productos_presentaciones_codigo_barras_unique', 'new' => 'presentaciones_producto_codigo_barras_unique'],
            ['table' => 'usuarios_internos', 'old' => 'usuarios_email_unique', 'new' => 'usuarios_internos_email_unique'],
        ];
    }
};
