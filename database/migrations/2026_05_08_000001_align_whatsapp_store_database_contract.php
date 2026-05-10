<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('carrito_compras_web') && ! Schema::hasTable('carritos_web')) {
            Schema::rename('carrito_compras_web', 'carritos_web');
        }

        if (
            Schema::hasTable('productos_presentaciones')
            && Schema::hasColumn('productos_presentaciones', 'costo_compra')
            && ! Schema::hasColumn('productos_presentaciones', 'costo_reposicion')
        ) {
            Schema::table('productos_presentaciones', function (Blueprint $table) {
                $table->renameColumn('costo_compra', 'costo_reposicion');
            });
        }

        $this->normalizeCartForeignKeys();
        $this->renameMigrationRows();
        $this->deleteOutOfScopeMigrationRows();
    }

    public function down(): void
    {
        // This migration makes the technical ledger match the current application scope.
    }

    private function renameMigrationRows(): void
    {
        foreach ([
            '2026_04_13_000025_create_carrito_compras_web_table' => '2026_04_13_000025_create_carritos_web_table',
            '2026_04_26_000002_add_almacenero_role' => '2026_04_26_000002_add_whatsapp_operator_role',
            '2026_05_06_000002_remove_pin_caja_from_usuarios' => '2026_05_06_000002_remove_legacy_user_access_field',
            '2026_05_06_000003_drop_legacy_pos_inventory_purchase_reservation_tables' => '2026_05_06_000003_drop_out_of_scope_operational_tables',
        ] as $oldName => $newName) {
            $oldExists = DB::table('migrations')->where('migration', $oldName)->exists();
            if (! $oldExists) {
                continue;
            }

            $newExists = DB::table('migrations')->where('migration', $newName)->exists();
            if ($newExists) {
                DB::table('migrations')->where('migration', $oldName)->delete();
                continue;
            }

            DB::table('migrations')
                ->where('migration', $oldName)
                ->update(['migration' => $newName]);
        }
    }

    private function normalizeCartForeignKeys(): void
    {
        if (! Schema::hasTable('carritos_web') || DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        foreach ([
            'carrito_compras_web_id_cliente_foreign',
            'carrito_compras_web_id_presentacion_foreign',
        ] as $legacyKey) {
            if ($this->foreignKeyExists('carritos_web', $legacyKey)) {
                Schema::table('carritos_web', function (Blueprint $table) use ($legacyKey) {
                    $table->dropForeign($legacyKey);
                });
            }
        }

        if (! $this->foreignKeyExists('carritos_web', 'carritos_web_id_cliente_foreign')) {
            Schema::table('carritos_web', function (Blueprint $table) {
                $table->foreign('id_cliente', 'carritos_web_id_cliente_foreign')
                    ->references('id_cliente')
                    ->on('clientes')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
            });
        }

        if (! $this->foreignKeyExists('carritos_web', 'carritos_web_id_presentacion_foreign')) {
            Schema::table('carritos_web', function (Blueprint $table) {
                $table->foreign('id_presentacion', 'carritos_web_id_presentacion_foreign')
                    ->references('id_presentacion')
                    ->on('productos_presentaciones')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
            });
        }
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

    private function deleteOutOfScopeMigrationRows(): void
    {
        DB::table('migrations')
            ->whereIn('migration', [
                '2026_04_13_000012_create_proveedores_table',
                '2026_04_13_000016_create_compras_table',
                '2026_04_13_000017_create_lotes_inventario_table',
                '2026_04_13_000018_create_movimientos_inventario_table',
                '2026_04_13_000019_create_recetas_combos_table',
                '2026_04_13_000021_create_direcciones_cliente_table',
                '2026_04_13_000022_create_cupones_descuento_table',
                '2026_04_13_000023_create_repartidores_table',
                '2026_04_13_000030_create_pos_tables',
                '2026_04_13_000040_create_reservas_table',
                '2026_04_13_000041_create_reserva_detalles_table',
                '2026_04_18_000001_add_id_presentacion_to_reserva_detalles_table',
                '2026_04_26_000003_create_warehouse_stock_tables',
                '2026_04_26_000004_rename_default_warehouses_and_extend_kardex',
                '2026_04_28_000001_add_pos_reference_to_notificaciones_admin',
                '2026_04_28_000002_extend_pos_for_advanced_sales',
                '2026_04_28_000003_add_cancellation_user_to_pos_sales',
                '2026_04_28_000004_create_pos_document_series',
                '2026_05_06_000001_refactor_notifications_to_whatsapp_orders',
            ])
            ->delete();
    }
};
