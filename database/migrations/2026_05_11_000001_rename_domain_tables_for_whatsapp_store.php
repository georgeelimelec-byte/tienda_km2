<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ($this->tableRenames() as $oldName => $newName) {
            $this->renameIfNeeded($oldName, $newName);
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach (array_reverse($this->tableRenames(), true) as $oldName => $newName) {
            $this->renameIfNeeded($newName, $oldName);
        }

        Schema::enableForeignKeyConstraints();
    }

    private function renameIfNeeded(string $oldName, string $newName): void
    {
        if (! Schema::hasTable($oldName) || Schema::hasTable($newName)) {
            return;
        }

        Schema::rename($oldName, $newName);
    }

    private function tableRenames(): array
    {
        return [
            'roles' => 'roles_sistema',
            'modulos' => 'modulos_sistema',
            'usuarios' => 'usuarios_internos',
            'permisos_rol' => 'permisos_por_rol',
            'permisos_usuario' => 'permisos_por_usuario',
            'clientes' => 'clientes_web',
            'categorias' => 'categorias_producto',
            'productos_presentaciones' => 'presentaciones_producto',
            'productos_imagenes' => 'imagenes_producto',
            'carritos_web' => 'carrito_items',
            'pedidos_whatsapp' => 'pedidos_tienda',
            'pedidos_whatsapp_detalles' => 'detalle_pedidos_tienda',
            'promocion_productos' => 'promociones_productos',
            'promocion_categorias' => 'promociones_categorias',
            'stock_web_movimientos' => 'movimientos_stock_web',
            'auditoria_operativa' => 'auditoria_sistema',
            'banners_web' => 'banners_tienda',
            'zonas_delivery' => 'zonas_entrega',
            'storefront_settings' => 'configuracion_tienda',
        ];
    }
};
