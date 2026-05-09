<?php

namespace Modules\Storefront\Services;

use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\ProductoPresentacion;
use Modules\Storefront\Models\StockWebMovimiento;

class StockWebService
{
    public function reserve(ProductoPresentacion $presentation, int $quantity, int $pedidoId, string $motivo): bool
    {
        if ($quantity <= 0) {
            return true;
        }

        $updated = ProductoPresentacion::where('id_presentacion', $presentation->id_presentacion)
            ->where('stock_web', '>=', $quantity)
            ->decrement('stock_web', $quantity);

        if (! $updated) {
            return false;
        }

        $presentation->refresh();
        $this->record($presentation, $pedidoId, 'reserva_pedido', -$quantity, $presentation->stock_web + $quantity, $presentation->stock_web, $motivo);

        return true;
    }

    public function restore(ProductoPresentacion $presentation, int $quantity, ?int $pedidoId, string $motivo): void
    {
        if ($quantity <= 0) {
            return;
        }

        $before = (int) $presentation->stock_web;
        $presentation->increment('stock_web', $quantity);
        $presentation->refresh();

        $this->record($presentation, $pedidoId, 'devolucion_stock', $quantity, $before, (int) $presentation->stock_web, $motivo);
    }

    public function adjustManual(ProductoPresentacion $presentation, int $newStock, string $motivo): void
    {
        $before = (int) $presentation->stock_web;
        $presentation->update(['stock_web' => max(0, $newStock)]);
        $presentation->refresh();

        $this->record($presentation, null, 'ajuste_manual', (int) $presentation->stock_web - $before, $before, (int) $presentation->stock_web, $motivo);
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
        StockWebMovimiento::create([
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
}
