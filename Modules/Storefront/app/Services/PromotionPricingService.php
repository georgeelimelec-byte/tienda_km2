<?php

namespace Modules\Storefront\Services;

use Modules\Inventory\Models\Categoria;
use Modules\Inventory\Models\ProductoPresentacion;
use Modules\Storefront\Models\Promocion;

class PromotionPricingService
{
    public function priceFor(ProductoPresentacion $presentation): array
    {
        $basePrice = (float) $presentation->precio;
        $bestPromotion = null;
        $bestFinal = $basePrice;

        foreach ($this->activePromotionsFor($presentation) as $promotion) {
            $discount = $this->discountAmount($basePrice, $promotion->tipo_descuento, (float) $promotion->valor_descuento);
            $final = max(0, $basePrice - $discount);

            if ($final < $bestFinal) {
                $bestFinal = $final;
                $bestPromotion = $promotion;
            }
        }

        return [
            'base_price' => $basePrice,
            'final_price' => $bestFinal,
            'reference_price' => $presentation->precio_referencial !== null ? (float) $presentation->precio_referencial : null,
            'promotion' => $bestPromotion,
            'has_promotion' => $bestPromotion !== null,
        ];
    }

    public function activePromotionsFor(ProductoPresentacion $presentation)
    {
        $product = $presentation->relationLoaded('producto')
            ? $presentation->producto
            : $presentation->producto()->with('categoria')->first();

        if (! $product) {
            return collect();
        }

        $categoryIds = $this->categoryAndParentIds((int) $product->id_categoria);

        return Promocion::activas()
            ->where(function ($query) use ($product, $categoryIds) {
                $query->whereHas('productos', fn ($productQuery) => $productQuery->where('productos.id_producto', $product->id_producto))
                    ->orWhereHas('categorias', fn ($categoryQuery) => $categoryQuery->whereIn('categorias.id_categoria', $categoryIds));
            })
            ->get();
    }

    private function discountAmount(float $basePrice, string $type, float $value): float
    {
        if ($type === 'Monto') {
            return min($basePrice, max(0, $value));
        }

        return min($basePrice, $basePrice * min(max($value, 0), 100) / 100);
    }

    private function categoryAndParentIds(int $categoryId): array
    {
        $ids = [];
        $currentId = $categoryId;

        while ($currentId && !in_array($currentId, $ids, true)) {
            $ids[] = $currentId;
            $currentId = (int) (Categoria::where('id_categoria', $currentId)->value('id_categoria_padre') ?? 0);
        }

        return $ids;
    }
}
