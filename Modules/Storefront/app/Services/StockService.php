<?php

namespace Modules\Storefront\Services;

use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\ProductoPresentacion;
use Modules\Storefront\Models\StockMovimiento;
use Modules\Storefront\Models\StorefrontSetting;

class StockService
{
    public function reserve(ProductoPresentacion $presentation, int $quantity, int $pedidoId, string $motivo): bool
    {
        if ($quantity <= 0 || ! $this->stockControlEnabled()) {
            return true;
        }

        $updated = ProductoPresentacion::where('id_presentacion', $presentation->id_presentacion)
            ->where('stock', '>=', $quantity)
            ->decrement('stock', $quantity);

        if (! $updated) {
            return false;
        }

        $presentation->refresh();
        $this->record($presentation, $pedidoId, 'reserva_pedido', -$quantity, $presentation->stock + $quantity, $presentation->stock, $motivo);

        return true;
    }

    public function restore(ProductoPresentacion $presentation, int $quantity, ?int $pedidoId, string $motivo): void
    {
        if ($quantity <= 0 || ! $this->stockControlEnabled()) {
            return;
        }

        $before = (int) $presentation->stock;
        $presentation->increment('stock', $quantity);
        $presentation->refresh();

        $this->record($presentation, $pedidoId, 'devolucion_stock', $quantity, $before, (int) $presentation->stock, $motivo);
    }

    public function adjustManual(ProductoPresentacion $presentation, int $newStock, string $motivo): void
    {
        $before = (int) $presentation->stock;
        $presentation->update(['stock' => max(0, $newStock)]);
        $presentation->refresh();

        $this->record($presentation, null, 'ajuste_manual', (int) $presentation->stock - $before, $before, (int) $presentation->stock, $motivo);
    }

    private function record(
        ProductoPresentacion $presentation,
        ?int $pedidoId,
        string $type,
        int $quantity,
        int $before,
        int $after,
        string $motivo
    ): void {
        StockMovimiento::create([
            'id_presentacion' => $presentation->id_presentacion,
            'id_pedido_whatsapp' => $pedidoId,
            'tipo_movimiento' => $type,
            'cantidad' => $quantity,
            'stock_anterior' => $before,
            'stock_nuevo' => $after,
            'motivo' => $motivo,
            'id_usuario' => Auth::id(),
        ]);
    }

    private function stockControlEnabled(): bool
    {
        return StorefrontSetting::current()->stockControlEnabled();
    }
}
