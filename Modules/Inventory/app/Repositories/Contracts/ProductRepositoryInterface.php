<?php

namespace Modules\Inventory\Repositories\Contracts;

use Illuminate\Support\Collection;
use Modules\Inventory\Models\ProductoPresentacion;

/**
 * Contrato para el repositorio de productos del catalogo web.
 */
interface ProductRepositoryInterface
{
    public function findByBarcode(string $barcode): ?ProductoPresentacion;

    public function findPresentacionById(int $id): ?ProductoPresentacion;

    public function searchByName(string $query): Collection;

    public function deductStock(int $presentacionId, int $cantidad): bool;

    public function getLowStockProducts(): Collection;
}
