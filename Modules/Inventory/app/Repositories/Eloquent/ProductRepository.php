<?php

namespace Modules\Inventory\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\ProductoPresentacion;
use Modules\Inventory\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Implementación Eloquent del repositorio de productos.
 * Optimizado para busquedas rapidas del catalogo y tienda virtual.
 */
class ProductRepository implements ProductRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByBarcode(string $barcode): ?ProductoPresentacion
    {
        return ProductoPresentacion::with('producto')
            ->where('codigo_barras', $barcode)
            ->where('estado', 'Activo')
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findPresentacionById(int $id): ?ProductoPresentacion
    {
        return ProductoPresentacion::with('producto')->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function searchByName(string $query): Collection
    {
        return ProductoPresentacion::with('producto')
            ->where('estado', 'Activo')
            ->where(function ($q) use ($query) {
                $q->whereHas('producto', fn($p) => $p->where('nombre_base', 'LIKE', "%{$query}%"))
                  ->orWhere('nombre_variante', 'LIKE', "%{$query}%");
            })
            ->limit(20)
            ->get();
    }

    /**
     * {@inheritdoc}
     * Usa UPDATE atómico con WHERE stock >= cantidad para prevenir
     * race conditions en ventas concurrentes al mismo producto.
     */
    public function deductStock(int $presentacionId, int $cantidad): bool
    {
        return DB::transaction(function () use ($presentacionId, $cantidad) {
            $presentacion = ProductoPresentacion::query()
                ->lockForUpdate()
                ->find($presentacionId);

            if (!$presentacion || (int) $presentacion->stock < $cantidad) {
                return false;
            }

            return ProductoPresentacion::where('id_presentacion', $presentacionId)
                ->where('stock', '>=', $cantidad)
                ->decrement('stock', $cantidad) > 0;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getLowStockProducts(): Collection
    {
        return ProductoPresentacion::with('producto')
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->where('estado', 'Activo')
            ->get();
    }
}
