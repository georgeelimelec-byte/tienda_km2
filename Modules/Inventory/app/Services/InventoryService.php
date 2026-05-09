<?php

namespace Modules\Inventory\Services;

use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\ProductoPresentacion;
use Modules\Inventory\Repositories\Contracts\ProductRepositoryInterface;

class InventoryService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function deductStock(
        int $presentacionId,
        int $cantidad,
        string $motivo,
        int $usuarioId,
        ?string $reference = null
    ): void {
        if ($cantidad <= 0) {
            throw new \RuntimeException('La cantidad a descontar debe ser mayor a cero.');
        }

        DB::transaction(function () use ($presentacionId, $cantidad) {
            $presentation = $this->findLockedPresentation($presentacionId);
            $currentStock = (int) $presentation->stock_web;

            if ($currentStock < $cantidad) {
                throw new \RuntimeException(
                    "Stock insuficiente para la presentacion #{$presentacionId}. Se requieren {$cantidad} unidades."
                );
            }

            $presentation->update(['stock_web' => $currentStock - $cantidad]);
        });
    }

    public function addStock(
        int $presentacionId,
        int $cantidad,
        string $motivo,
        int $usuarioId,
        ?string $reference = null
    ): void {
        $this->adjustStock($presentacionId, $cantidad);
    }

    private function adjustStock(int $presentacionId, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new \RuntimeException('La cantidad debe ser mayor a cero.');
        }

        DB::transaction(function () use ($presentacionId, $quantity) {
            $presentation = $this->findLockedPresentation($presentacionId);
            $presentation->update(['stock_web' => (int) $presentation->stock_web + $quantity]);
        });
    }

    private function findLockedPresentation(int $presentacionId): ProductoPresentacion
    {
        return ProductoPresentacion::query()
            ->lockForUpdate()
            ->findOrFail($presentacionId);
    }
}
