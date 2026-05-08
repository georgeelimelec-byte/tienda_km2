<?php

namespace Modules\Inventory\Repositories\Contracts;

use Modules\Inventory\Models\ProductoPresentacion;
use Illuminate\Support\Collection;

/**
 * Contrato para el repositorio de productos.
 * Define las operaciones de persistencia del módulo Inventory.
 */
interface ProductRepositoryInterface
{
    /**
     * Busca presentacion por codigo de barras.
     */
    public function findByBarcode(string $barcode): ?ProductoPresentacion;

    /**
     * Busca presentación por ID.
     */
    public function findPresentacionById(int $id): ?ProductoPresentacion;

    /**
     * Busqueda por nombre del producto para catalogo y tienda virtual.
     */
    public function searchByName(string $query): Collection;

    /**
     * Descuenta stock de forma atómica (previene race conditions).
     */
    public function deductStock(int $presentacionId, int $cantidad): bool;

    /**
     * Obtiene productos con stock bajo.
     */
    public function getLowStockProducts(): Collection;
}
