<?php

namespace Modules\Storefront\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EmpresaConfiguracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Categoria;
use Modules\Inventory\Models\Producto;
use Modules\Inventory\Models\ProductoPresentacion;
use Modules\Storefront\Models\BannerWeb;
use Modules\Storefront\Models\PedidoWhatsapp;
use Modules\Storefront\Models\PedidoWhatsappDetalle;
use Modules\Storefront\Models\ZonaDelivery;

class StorefrontController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');
        $categoriaId = $request->get('categoria_id');
        $filtro = $request->get('filtro');
        $categoryIds = $categoriaId ? $this->categoryAndChildrenIds((int) $categoriaId) : [];

        $categoriasTree = Categoria::whereNull('id_categoria_padre')
            ->where('estado', 'Activo')
            ->with(['hijos' => function ($q) {
                $q->where('estado', 'Activo')
                    ->with(['hijos' => fn ($subQuery) => $subQuery->where('estado', 'Activo')->orderBy('nombre')])
                    ->orderBy('nombre');
            }])
            ->orderBy('nombre')
            ->get();

        $categorias = Categoria::where('estado', 'Activo')->orderBy('nombre')->get();
        $igv = $this->igvPercent();
        $bannersCarrusel = BannerWeb::where('estado', 'Activo')
            ->where('posicion', 'Carrusel')
            ->orderBy('id_banner')
            ->get();
        $bannersPromocionales = BannerWeb::where('estado', 'Activo')
            ->whereIn('posicion', ['Lateral', 'Pop_up'])
            ->orderBy('id_banner')
            ->get();
        $promociones = ProductoPresentacion::with([
                'producto.categoria.padre',
                'producto.imagenes',
                'imagenes',
            ])
            ->where('estado', 'Activo')
            ->whereNotNull('precio_oferta')
            ->whereColumn('precio_oferta', '<', 'precio')
            ->whereHas('producto', fn ($q) => $q->where('estado', 'Activo'))
            ->orderByRaw('(precio - precio_oferta) DESC')
            ->take(4)
            ->get();

        $productos = Producto::query()
            ->where('estado', 'Activo')
            ->whereHas('presentaciones', fn ($q) => $q->where('estado', 'Activo'))
            ->when($query, function ($q, $query) {
                return $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('nombre_base', 'LIKE', "%{$query}%")
                        ->orWhere('descripcion', 'LIKE', "%{$query}%")
                        ->orWhereHas('presentaciones', function ($presentationQuery) use ($query) {
                            $presentationQuery->where('nombre_variante', 'LIKE', "%{$query}%")
                                ->orWhere('codigo_barras', 'LIKE', "%{$query}%");
                        });
                });
            })
            ->when(!empty($categoryIds), fn ($q) => $q->whereIn('id_categoria', $categoryIds))
            ->when($filtro === 'promociones', function ($q) {
                return $q->whereHas('presentaciones', function ($presentationQuery) {
                    $presentationQuery->where('estado', 'Activo')
                        ->whereNotNull('precio_oferta')
                        ->whereColumn('precio_oferta', '<', 'precio');
                });
            })
            ->when($filtro === 'combos', function ($q) {
                return $q->where(function ($subQuery) {
                    $subQuery->where('nombre_base', 'LIKE', '%combo%')
                        ->orWhere('descripcion', 'LIKE', '%combo%')
                        ->orWhereHas('presentaciones', function ($presentationQuery) {
                            $presentationQuery->where('nombre_variante', 'LIKE', '%combo%')
                                ->orWhere('nombre_variante', 'LIKE', '%pack%');
                        });
                });
            })
            ->with($this->catalogRelations())
            ->withAvg(['resenas as valoracion_promedio' => fn ($q) => $q->where('estado', 'Aprobado')], 'calificacion')
            ->withCount(['resenas as total_resenas' => fn ($q) => $q->where('estado', 'Aprobado')])
            ->orderBy('nombre_base')
            ->get();

        return view('storefront::index', compact(
            'productos',
            'categorias',
            'categoriasTree',
            'igv',
            'bannersCarrusel',
            'bannersPromocionales',
            'promociones'
        ));
    }

    public function show($id)
    {
        $producto = Producto::query()
            ->where('estado', 'Activo')
            ->whereHas('presentaciones', fn ($q) => $q->where('estado', 'Activo'))
            ->with($this->catalogRelations())
            ->withAvg(['resenas as valoracion_promedio' => fn ($q) => $q->where('estado', 'Aprobado')], 'calificacion')
            ->withCount(['resenas as total_resenas' => fn ($q) => $q->where('estado', 'Aprobado')])
            ->findOrFail($id);

        $igv = $this->igvPercent();

        $relacionados = Producto::where('estado', 'Activo')
            ->where('id_producto', '!=', $id)
            ->where('id_categoria', $producto->id_categoria)
            ->whereHas('presentaciones', fn ($q) => $q->where('estado', 'Activo'))
            ->with($this->catalogRelations())
            ->withAvg(['resenas as valoracion_promedio' => fn ($q) => $q->where('estado', 'Aprobado')], 'calificacion')
            ->withCount(['resenas as total_resenas' => fn ($q) => $q->where('estado', 'Aprobado')])
            ->inRandomOrder()
            ->take(4)
            ->get();

        if ($relacionados->isEmpty()) {
            $relacionados = Producto::where('estado', 'Activo')
                ->where('id_producto', '!=', $id)
                ->whereHas('presentaciones', fn ($q) => $q->where('estado', 'Activo'))
                ->with($this->catalogRelations())
                ->inRandomOrder()
                ->take(4)
                ->get();
        }

        return view('storefront::producto', compact('producto', 'relacionados', 'igv'));
    }

    public function checkout()
    {
        $zonas = ZonaDelivery::where('estado', 'Activo')
            ->orderBy('tarifa')
            ->orderBy('nombre')
            ->get();
        $igv = $this->igvPercent();

        return view('storefront::checkout', compact('zonas', 'igv'));
    }

    public function storePedido(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'whatsapp' => 'required|string|max:20',
            'direccion' => 'required|string',
            'referencia' => 'nullable|string',
            'id_zona' => 'required|integer|exists:zonas_delivery,id_zona',
            'cart' => 'required|json',
        ]);

        $cart = json_decode($data['cart'], true);
        if (!is_array($cart) || empty($cart)) {
            return back()->with('error', 'El carrito esta vacio');
        }

        $items = $this->normalizeCartItems($cart);
        if (empty($items)) {
            return back()->with('error', 'No se pudieron validar los productos del carrito');
        }

        foreach ($items as $item) {
            if ($item['quantity'] > $item['presentation']->stock) {
                return back()->with('error', "Stock insuficiente para {$item['name']}");
            }
        }

        $zona = ZonaDelivery::where('estado', 'Activo')->findOrFail($data['id_zona']);
        $costoDelivery = (float) $zona->tarifa;
        $totalProductos = collect($items)->sum(fn ($item) => $item['subtotal']);

        do {
            $codigo = '#WA-' . now()->format('ymd') . '-' . str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (PedidoWhatsapp::where('codigo_pedido', $codigo)->exists());

        $numeroEmpresa = preg_replace('/\D+/', '', env('WHATSAPP_EMPRESA', '51999999999')) ?: '51999999999';
        $mensaje = "Hola, mi nombre es {$data['nombre']}. Quiero confirmar mi pedido *{$codigo}*.\n";

        foreach ($items as $item) {
            $mensaje .= "- {$item['quantity']} x {$item['name']}: S/ " . number_format($item['subtotal'], 2) . "\n";
        }

        $mensaje .= "\nTotal productos: S/ " . number_format($totalProductos, 2) . "\n";
        $mensaje .= "Delivery {$zona->nombre}: S/ " . number_format($costoDelivery, 2) . "\n";
        $mensaje .= "Total a pagar: S/ " . number_format($totalProductos + $costoDelivery, 2) . "\n\n";
        $mensaje .= "Mi direccion es: {$data['direccion']}";

        if (!empty($data['referencia'])) {
            $mensaje .= "\nReferencia: {$data['referencia']}";
        }

        $whatsappUrl = "https://wa.me/{$numeroEmpresa}?text=" . urlencode($mensaje);

        DB::transaction(function () use ($codigo, $data, $zona, $totalProductos, $costoDelivery, $items, $whatsappUrl) {
            $pedido = PedidoWhatsapp::create([
                'codigo_pedido' => $codigo,
                'cliente_nombre' => $data['nombre'],
                'cliente_whatsapp' => $data['whatsapp'],
                'cliente_direccion' => $data['direccion'],
                'cliente_referencia' => $data['referencia'] ?? '',
                'id_zona_delivery' => $zona->id_zona,
                'total_productos' => $totalProductos,
                'costo_delivery' => $costoDelivery,
                'total_pedido' => $totalProductos + $costoDelivery,
                'estado' => 'Pendiente',
                'whatsapp_url' => $whatsappUrl,
            ]);

            foreach ($items as $item) {
                PedidoWhatsappDetalle::create([
                    'id_pedido_whatsapp' => $pedido->id_pedido_whatsapp,
                    'id_producto' => $item['presentation']->id_producto,
                    'id_presentacion' => $item['presentation']->id_presentacion,
                    'nombre_producto' => $item['name'],
                    'precio_unitario' => $item['price'],
                    'cantidad' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }
        });

        return redirect()->away($whatsappUrl);
    }

    private function catalogRelations(): array
    {
        return [
            'categoria.padre',
            'imagenes',
            'presentaciones' => function ($q) {
                $q->where('estado', 'Activo')
                    ->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END')
                    ->orderByRaw('COALESCE(precio_oferta, precio)')
                    ->orderBy('id_presentacion');
            },
            'presentaciones.unidadMedida',
            'presentaciones.imagenes',
            'resenasAprobadas.cliente',
        ];
    }

    private function categoryAndChildrenIds(int $categoryId): array
    {
        $ids = [$categoryId];
        $pending = [$categoryId];

        while (!empty($pending)) {
            $children = Categoria::whereIn('id_categoria_padre', $pending)
                ->where('estado', 'Activo')
                ->pluck('id_categoria')
                ->all();

            $children = array_values(array_diff($children, $ids));
            $ids = array_merge($ids, $children);
            $pending = $children;
        }

        return $ids;
    }

    private function igvPercent(): float
    {
        return (float) optional(
            EmpresaConfiguracion::where('estado', 'Activo')->first()
        )->porcentaje_igv ?: 18.0;
    }

    private function normalizeCartItems(array $cart): array
    {
        $items = [];

        foreach ($cart as $rawItem) {
            $quantity = max(1, (int) ($rawItem['quantity'] ?? 1));
            $presentationId = $rawItem['presentation_id'] ?? null;
            $productId = $rawItem['product_id'] ?? (!$presentationId ? ($rawItem['id'] ?? null) : null);

            $presentation = null;
            if ($presentationId) {
                $presentation = ProductoPresentacion::with('producto')
                    ->where('estado', 'Activo')
                    ->whereHas('producto', fn ($q) => $q->where('estado', 'Activo'))
                    ->find($presentationId);
            }

            if (!$presentation && $productId) {
                $presentation = ProductoPresentacion::with('producto')
                    ->where('estado', 'Activo')
                    ->where('id_producto', $productId)
                    ->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END')
                    ->orderByRaw('COALESCE(precio_oferta, precio)')
                    ->first();
            }

            if (!$presentation && !empty($rawItem['id'])) {
                $presentation = ProductoPresentacion::with('producto')
                    ->where('estado', 'Activo')
                    ->where('id_producto', $rawItem['id'])
                    ->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END')
                    ->orderByRaw('COALESCE(precio_oferta, precio)')
                    ->first();
            }

            if (!$presentation || !$presentation->producto) {
                continue;
            }

            $variant = trim((string) $presentation->nombre_variante);
            $name = $presentation->producto->nombre_base;
            if ($variant !== '' && strtolower($variant) !== 'unidad') {
                $name .= ' - ' . $variant;
            }

            $price = (float) $presentation->precio_efectivo;
            $key = $presentation->id_presentacion;

            if (isset($items[$key])) {
                $items[$key]['quantity'] += $quantity;
                $items[$key]['subtotal'] = $items[$key]['quantity'] * $price;
                continue;
            }

            $items[$key] = [
                'presentation' => $presentation,
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'subtotal' => $price * $quantity,
            ];
        }

        return array_values($items);
    }
}
