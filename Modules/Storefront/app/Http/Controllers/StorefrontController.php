<?php

namespace Modules\Storefront\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EmpresaConfiguracion;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Inventory\Models\Categoria;
use Modules\Inventory\Models\Producto;
use Modules\Inventory\Models\ProductoPresentacion;
use Modules\Storefront\Models\BannerWeb;
use Modules\Storefront\Models\Cliente;
use Modules\Storefront\Models\PedidoWhatsapp;
use Modules\Storefront\Models\PedidoWhatsappDetalle;
use Modules\Storefront\Models\Promocion;
use Modules\Storefront\Models\ZonaDelivery;
use Modules\Storefront\Services\OperationalAudit;
use Modules\Storefront\Services\StockWebService;
use RuntimeException;

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
        $promociones = Promocion::activas()
            ->with(['productos.imagenes', 'categorias'])
            ->orderByDesc('id_promocion')
            ->take(6)
            ->get();
        $promotedProductIds = $this->promotedProductIds($promociones);

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
            ->when($filtro === 'promociones', function ($q) use ($promotedProductIds) {
                if (empty($promotedProductIds)) {
                    return $q->whereRaw('1 = 0');
                }

                return $q->whereIn('id_producto', $promotedProductIds);
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
            ->findOrFail($id);

        $igv = $this->igvPercent();

        $relacionados = Producto::where('estado', 'Activo')
            ->where('id_producto', '!=', $id)
            ->where('id_categoria', $producto->id_categoria)
            ->whereHas('presentaciones', fn ($q) => $q->where('estado', 'Activo'))
            ->with($this->catalogRelations())
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
        $cliente = $this->sessionCliente(request());
        if (! $cliente) {
            return redirect()
                ->route('storefront.cliente.login')
                ->with('error', 'Inicia sesion o registrate para completar el pedido.');
        }

        $zonas = ZonaDelivery::where('estado', 'Activo')
            ->orderBy('tarifa')
            ->orderBy('nombre')
            ->get();
        $igv = $this->igvPercent();

        return view('storefront::checkout', compact('zonas', 'igv', 'cliente'));
    }

    public function storePedido(Request $request, StockWebService $stockWeb, OperationalAudit $audit)
    {
        $cliente = $this->sessionCliente($request);
        if (! $cliente) {
            return redirect()
                ->route('storefront.cliente.login')
                ->with('error', 'Inicia sesion o registrate para completar el pedido.');
        }

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
            if ($item['quantity'] > (int) $item['presentation']->stock_web) {
                return back()->with('error', "Stock web insuficiente para {$item['name']}. Disponible: {$item['presentation']->stock_web}");
            }
        }

        $zona = ZonaDelivery::where('estado', 'Activo')->findOrFail($data['id_zona']);
        $costoDelivery = (float) $zona->tarifa;
        $totalProductos = collect($items)->sum(fn ($item) => $item['subtotal']);

        $cliente->update([
            'nombre_o_razon_social' => $data['nombre'],
            'celular' => $data['whatsapp'],
            'direccion' => $data['direccion'],
        ]);

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
        $mensaje .= "Total referencial del pedido: S/ " . number_format($totalProductos + $costoDelivery, 2) . "\n\n";
        $mensaje .= "Mi direccion es: {$data['direccion']}";

        if (!empty($data['referencia'])) {
            $mensaje .= "\nReferencia: {$data['referencia']}";
        }

        $whatsappUrl = "https://wa.me/{$numeroEmpresa}?text=" . urlencode($mensaje);

        try {
            DB::transaction(function () use ($codigo, $data, $zona, $totalProductos, $costoDelivery, $items, $whatsappUrl, $stockWeb, $audit, $request) {
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
                    if (! $stockWeb->reserve($item['presentation'], $item['quantity'], $pedido->id_pedido_whatsapp, 'Reserva automatica al crear pedido web')) {
                        throw new RuntimeException("Stock web insuficiente para {$item['name']}. El pedido no fue registrado.");
                    }

                    PedidoWhatsappDetalle::create([
                        'id_pedido_whatsapp' => $pedido->id_pedido_whatsapp,
                        'id_producto' => $item['presentation']->id_producto,
                        'id_presentacion' => $item['presentation']->id_presentacion,
                        'nombre_producto' => $item['name'],
                        'precio_unitario' => $item['price'],
                        'cantidad_solicitada' => $item['quantity'],
                        'cantidad_confirmada' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                        'estado_item' => 'Solicitado',
                    ]);
                }

                $audit->log(
                    'crear_pedido_web',
                    'pedidos_whatsapp',
                    $pedido->id_pedido_whatsapp,
                    "Cliente {$pedido->cliente_nombre} creo el pedido {$pedido->codigo_pedido} desde la tienda virtual",
                    null,
                    [
                        'codigo' => $pedido->codigo_pedido,
                        'total' => $pedido->total_pedido,
                        'items' => collect($items)->map(fn ($item) => [
                            'producto' => $item['name'],
                            'cantidad_solicitada' => $item['quantity'],
                            'cantidad_confirmada' => $item['quantity'],
                        ])->values()->all(),
                    ],
                    $request
                );
            });
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->away($whatsappUrl);
    }

    public function loginForm()
    {
        return view('storefront::cliente-login');
    }

    public function registerForm()
    {
        return view('storefront::cliente-register');
    }

    public function loginCliente(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $cliente = Cliente::where('email', $data['email'])->first();
        if (! $cliente || ! Hash::check($data['password'], (string) $cliente->password)) {
            return back()->withErrors(['email' => 'Credenciales incorrectas.'])->withInput();
        }

        $request->session()->put('cliente_id', $cliente->id_cliente);

        return redirect()->route('storefront.checkout');
    }

    public function registerCliente(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'email' => 'required|email|unique:clientes,email',
            'whatsapp' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $cliente = Cliente::create([
            'nombre_o_razon_social' => $data['nombre'],
            'email' => $data['email'],
            'celular' => $data['whatsapp'],
            'direccion' => $data['direccion'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        $request->session()->put('cliente_id', $cliente->id_cliente);

        return redirect()->route('storefront.checkout');
    }

    public function logoutCliente(Request $request)
    {
        $request->session()->forget('cliente_id');

        return redirect()->route('storefront.index');
    }

    private function catalogRelations(): array
    {
        return [
            'categoria.padre',
            'imagenes',
            'presentaciones' => function ($q) {
                $q->where('estado', 'Activo')
                    ->orderByRaw('CASE WHEN stock_web > 0 THEN 0 ELSE 1 END')
                    ->orderBy('precio')
                    ->orderBy('id_presentacion');
            },
            'presentaciones.unidadMedida',
            'presentaciones.imagenes',
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

    private function promotedProductIds(Collection $promociones): array
    {
        if ($promociones->isEmpty()) {
            return [];
        }

        $directProductIds = $promociones
            ->flatMap(fn (Promocion $promocion) => $promocion->productos->pluck('id_producto'))
            ->filter()
            ->values()
            ->all();

        $categoryIds = $promociones
            ->flatMap(fn (Promocion $promocion) => $promocion->categorias->pluck('id_categoria'))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $expandedCategoryIds = [];
        foreach ($categoryIds as $categoryId) {
            $expandedCategoryIds = array_merge($expandedCategoryIds, $this->categoryAndChildrenIds((int) $categoryId));
        }

        $categoryProductIds = empty($expandedCategoryIds)
            ? []
            : Producto::whereIn('id_categoria', array_unique($expandedCategoryIds))->pluck('id_producto')->all();

        return array_values(array_unique(array_merge($directProductIds, $categoryProductIds)));
    }

    private function igvPercent(): float
    {
        return (float) optional(
            EmpresaConfiguracion::where('estado', 'Activo')->first()
        )->porcentaje_igv ?: 18.0;
    }

    private function sessionCliente(Request $request): ?Cliente
    {
        $clienteId = $request->session()->get('cliente_id');

        return $clienteId ? Cliente::find($clienteId) : null;
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
                    ->orderByRaw('CASE WHEN stock_web > 0 THEN 0 ELSE 1 END')
                    ->orderBy('precio')
                    ->first();
            }

            if (!$presentation && !empty($rawItem['id'])) {
                $presentation = ProductoPresentacion::with('producto')
                    ->where('estado', 'Activo')
                    ->where('id_producto', $rawItem['id'])
                    ->orderByRaw('CASE WHEN stock_web > 0 THEN 0 ELSE 1 END')
                    ->orderBy('precio')
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
