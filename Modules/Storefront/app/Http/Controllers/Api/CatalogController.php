<?php

namespace Modules\Storefront\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\Models\Categoria;
use Modules\Inventory\Models\Producto;
use Modules\Storefront\Models\BannerWeb;

class CatalogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
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
            ->with($this->relations())
            ->withAvg(['resenas as valoracion_promedio' => fn ($q) => $q->where('estado', 'Aprobado')], 'calificacion')
            ->withCount(['resenas as total_resenas' => fn ($q) => $q->where('estado', 'Aprobado')])
            ->orderBy('nombre_base')
            ->paginate($request->integer('per_page', 20));

        return response()->json($products->through(fn ($product) => $this->productPayload($product)));
    }

    public function show(int $id): JsonResponse
    {
        $product = Producto::query()
            ->where('estado', 'Activo')
            ->whereHas('presentaciones', fn ($q) => $q->where('estado', 'Activo'))
            ->with($this->relations())
            ->withAvg(['resenas as valoracion_promedio' => fn ($q) => $q->where('estado', 'Aprobado')], 'calificacion')
            ->withCount(['resenas as total_resenas' => fn ($q) => $q->where('estado', 'Aprobado')])
            ->findOrFail($id);

        return response()->json($this->productPayload($product));
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

    private function relations(): array
    {
        return [
            'categoria',
            'imagenes',
            'presentaciones' => fn ($q) => $q->where('estado', 'Activo')
                ->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END')
                ->orderByRaw('COALESCE(precio_oferta, precio)'),
            'presentaciones.unidadMedida',
            'presentaciones.imagenes',
        ];
    }

    private function productPayload(Producto $product): array
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
            'presentaciones' => $product->presentaciones->map(fn ($presentation) => [
                'id_presentacion' => $presentation->id_presentacion,
                'nombre_variante' => $presentation->nombre_variante,
                'codigo_barras' => $presentation->codigo_barras,
                'precio' => (float) $presentation->precio,
                'precio_oferta' => $presentation->precio_oferta !== null ? (float) $presentation->precio_oferta : null,
                'precio_efectivo' => (float) $presentation->precio_efectivo,
                'stock' => (int) $presentation->stock,
                'unidad' => $presentation->unidadMedida,
                'imagen_principal_url' => optional($presentation->imagenes->first())->url ?: $product->imagen_principal_url,
                'imagenes' => $presentation->imagenes->map(fn ($image) => [
                    'id_imagen' => $image->id_imagen,
                    'url' => $image->url,
                    'orden' => $image->orden,
                ])->values(),
            ])->values(),
            'stock_total' => $product->stock_total,
            'valoracion_promedio' => $product->valoracion_promedio ? round((float) $product->valoracion_promedio, 2) : null,
            'total_resenas' => (int) $product->total_resenas,
        ];
    }
}
