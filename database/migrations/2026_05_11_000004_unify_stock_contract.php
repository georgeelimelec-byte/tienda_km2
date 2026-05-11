<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->normalizePresentationStockColumns();
        $this->normalizeStockMovementTable();
    }

    public function down(): void
    {
        $legacyStockColumn = $this->legacyStockColumn();
        $legacyMinimumColumn = $legacyStockColumn . '_minimo';

        if (Schema::hasTable('presentaciones_producto')) {
            if (Schema::hasColumn('presentaciones_producto', 'stock') && ! Schema::hasColumn('presentaciones_producto', $legacyStockColumn)) {
                Schema::table('presentaciones_producto', function (Blueprint $table) use ($legacyStockColumn) {
                    $table->renameColumn('stock', $legacyStockColumn);
                });
            }

            if (Schema::hasColumn('presentaciones_producto', 'stock_minimo') && ! Schema::hasColumn('presentaciones_producto', $legacyMinimumColumn)) {
                Schema::table('presentaciones_producto', function (Blueprint $table) use ($legacyMinimumColumn) {
                    $table->renameColumn('stock_minimo', $legacyMinimumColumn);
                });
            }
        }

        $legacyMovementTable = 'movimientos_stock_' . 'web';
        if (Schema::hasTable('movimientos_stock') && ! Schema::hasTable($legacyMovementTable)) {
            Schema::rename('movimientos_stock', $legacyMovementTable);
        }
    }

    private function normalizePresentationStockColumns(): void
    {
        if (! Schema::hasTable('presentaciones_producto')) {
            return;
        }

        $legacyStockColumn = $this->legacyStockColumn();
        $legacyMinimumColumn = $legacyStockColumn . '_minimo';

        if (Schema::hasColumn('presentaciones_producto', $legacyStockColumn) && ! Schema::hasColumn('presentaciones_producto', 'stock')) {
            Schema::table('presentaciones_producto', function (Blueprint $table) use ($legacyStockColumn) {
                $table->renameColumn($legacyStockColumn, 'stock');
            });
        } elseif (Schema::hasColumn('presentaciones_producto', $legacyStockColumn)) {
            DB::table('presentaciones_producto')
                ->where('stock', 0)
                ->where($legacyStockColumn, '>', 0)
                ->update(['stock' => DB::raw($legacyStockColumn)]);

            Schema::table('presentaciones_producto', function (Blueprint $table) use ($legacyStockColumn) {
                $table->dropColumn($legacyStockColumn);
            });
        }

        if (Schema::hasColumn('presentaciones_producto', $legacyMinimumColumn) && ! Schema::hasColumn('presentaciones_producto', 'stock_minimo')) {
            Schema::table('presentaciones_producto', function (Blueprint $table) use ($legacyMinimumColumn) {
                $table->renameColumn($legacyMinimumColumn, 'stock_minimo');
            });
        } elseif (Schema::hasColumn('presentaciones_producto', $legacyMinimumColumn)) {
            DB::table('presentaciones_producto')
                ->where('stock_minimo', 0)
                ->where($legacyMinimumColumn, '>', 0)
                ->update(['stock_minimo' => DB::raw($legacyMinimumColumn)]);

            Schema::table('presentaciones_producto', function (Blueprint $table) use ($legacyMinimumColumn) {
                $table->dropColumn($legacyMinimumColumn);
            });
        }
    }

    private function normalizeStockMovementTable(): void
    {
        foreach ($this->legacyStockMovementTables() as $legacyTable) {
            if (Schema::hasTable($legacyTable) && ! Schema::hasTable('movimientos_stock')) {
                Schema::rename($legacyTable, 'movimientos_stock');
            }
        }
    }

    private function legacyStockColumn(): string
    {
        return 'stock_' . 'web';
    }

    private function legacyStockMovementTables(): array
    {
        return [
            'stock_' . 'web_movimientos',
            'movimientos_stock_' . 'web',
            'stock_movimientos',
        ];
    }
};
