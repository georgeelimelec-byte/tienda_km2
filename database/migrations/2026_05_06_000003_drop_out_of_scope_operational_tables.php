<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ($this->legacyTables() as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Legacy modules were intentionally removed from the application.
    }

    private function legacyTables(): array
    {
        return [
            'pagos_pedido',
            'detalles_pedido',
            'pedidos',
            'cierres_caja',
            'movimientos_caja',
            'cajas_registradoras',
            'series_comprobante_pos',
            'reserva_detalles',
            'reservas',
            'movimientos_inventario',
            'lotes_inventario',
            'presentaciones_stock_almacen',
            'compras',
            'proveedores',
            'almacenes',
        ];
    }
};
