<?php

namespace Modules\Storefront\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\Models\Categoria;
use Modules\Inventory\Models\Producto;
use Modules\Storefront\Models\BannerWeb;
use Modules\Storefront\Models\StorefrontSetting;

class CatalogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $stockControlEnabled = StorefrontSetting::current()->stockControlEnabled();

        $products = Producto::query()
            ->where('estado', 'Activo')
            ->whereHas('presentaciones', fn ($q) => $q->where('estado', 'Activo'))
            ->when($request->filled('q'), function ($q) use ($request) {
                $search = $request->string('q');
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('nombre_base', 'LIKE', "%{$search}%")
                        ->orWhere('descripcion', 'LIKE', "%{$search}%")
                        ->orWhereHas('presentaciones', fn ($presentationQuery) => $presentationQuery->where('nombre_variante', 'LIKE', "%{$search}%"));
                });
            })
            ->when($request->filled('categoria_id'), fn ($q) => $q->where('id_categoria', $request->integer('categoria_id')))
            ->with($this->relations($stockControlEnabled))
            ->orderBy('nombre_base')
            ->paginate($request->integer('per_page', 20));

        return response()->json($products->through(fn ($product) => $this->productPayload($product, $stockControlEnabled)));
    }

    public function show(int $id): JsonResponse
    {
        $stockControlEnabled = StorefrontSetting::current()->stockControlEnabled();

        $product = Producto::query()
            ->where('estado', 'Activo')
            ->whereHas('presentaciones', fn ($q) => $q->where('estado', 'Activo'))
            ->with($this->relations($stockControlEnabled))
            ->findOrFail($id);

        return response()->json($this->productPayload($product, $stockControlEnabled));
    }

    public function categories(): JsonResponse
    {
        $categories = Categoria::whereNull('id_categoria_padre')
            ->where('estado', 'Activo')
            ->with(['hijos' => function ($q) {
                $q->where('estado', 'Activo')
                    ->with(['hijos' => fn ($subQuery) => $subQuery->where('estado', 'Activo')->orderBy('nombre')])
                    ->orderBy('nombre');
            }])
            ->orderBy('nombre')
            ->get();

        return response()->json($categories);
    }

    public function banners(): JsonResponse
    {
        return response()->json(
            BannerWeb::where('estado', 'Activo')
                ->orderBy('id_banner', 'desc')
                ->get()
        );
    }

    private function relations(bool $stockControlEnabled): array
    {
        return [
            'categoria',
            'imagenes',
            'presentaciones' => function ($q) use ($stockControlEnabled) {
                $q->where('estado', 'Activo');

                if ($stockControlEnabled) {
                    $q->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END');
                }

                $q->orderBy('precio');
            },
            'presentaciones.unidadMedida',
            'presentaciones.imagenes',
        ];
    }

    private function productPayload(Producto $product, bool $stockControlEnabled): array
    {
        return [
            'id_producto' => $product->id_producto,
            'nombre' => $product->nombre_base,
            'descripcion' => $product->descripcion,
            'categoria' => $product->categoria,
            'imagen_principal_url' => $product->imagen_principal_url,
            'imagenes' => $product->imagenes->map(fn ($image) => [
                'id_imagen' => $image->id_imagen,
                'url' => $image->url,
                'orden' => $image->orden,
            ])->values(),
            'stock_control_enabled' => $stockControlEnabled,
            'presentaciones' => $product->presentaciones->map(fn ($presentation) => [
                'id_presentacion' => $presentation->id_presentacion,
                'nombre_variante' => $presentation->nombre_variante,
                'codigo_barras' => $presentation->codigo_barras,
                'precio' => (float) $presentation->precio,
                'precio_referencial' => $presentation->precio_referencial !== null ? (float) $presentation->precio_referencial : null,
                'precio_efectivo' => (float) $presentation->precio_efectivo,
                'stock' => (int) $presentation->stock,
                'max_stock' => $stockControlEnabled ? (int) $presentation->stock : null,
                'stock_control_enabled' => $stockControlEnabled,
                'tiene_promocion' => (bool) $presentation->tiene_promocion,
                'promocion_activa' => optional($presentation->promocion_activa)->nombre,
                'unidad' => $presentation->unidadMedida,
                'imagen_principal_url' => optional($presentation->imagenes->first())->url ?: $product->imagen_principal_url,
                'imagenes' => $presentation->imagenes->map(fn ($image) => [
                    'id_imagen' => $image->id_imagen,
                    'url' => $image->url,
                    'orden' => $image->orden,
                ])->values(),
            ])->values(),
            'stock_total' => $product->stock_total,
        ];
    }
}
