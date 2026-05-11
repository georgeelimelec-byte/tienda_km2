<?php

namespace Modules\Storefront\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\ProductoPresentacion;
use Modules\Storefront\Models\PedidoWhatsapp;
use Modules\Storefront\Models\PedidoWhatsappDetalle;
use Modules\Storefront\Models\StorefrontSetting;
use Modules\Storefront\Models\ZonaDelivery;
use Modules\Storefront\Services\OperationalAudit;
use Modules\Storefront\Services\StockService;
use RuntimeException;

class CheckoutController extends Controller
{
    public function process(Request $request, StockService $stockService, OperationalAudit $audit): JsonResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'whatsapp' => 'required|string|max:20',
            'direccion' => 'required|string',
            'referencia' => 'nullable|string',
            'id_zona' => 'required|exists:zonas_entrega,id_zona',
            'items' => 'required|array|min:1',
            'items.*.id_presentacion' => 'required|exists:presentaciones_producto,id_presentacion',
            'items.*.cantidad' => 'required|integer|min:1',
        ]);

        $zona = ZonaDelivery::where('estado', 'Activo')->findOrFail($data['id_zona']);
        $lines = [];
        $setting = StorefrontSetting::current();
        $stockControlEnabled = $setting->stockControlEnabled();

        foreach ($data['items'] as $item) {
            $presentation = ProductoPresentacion::with('producto')
                ->where('estado', 'Activo')
                ->findOrFail($item['id_presentacion']);

            if ($stockControlEnabled && $item['cantidad'] > $presentation->stock) {
                return response()->json([
                    'message' => "Stock insuficiente para {$presentation->producto->nombre_base}.",
                ], 422);
            }

            $variant = trim((string) $presentation->nombre_variante);
            $name = $presentation->producto->nombre_base;
            if ($variant !== '' && strtolower($variant) !== 'unidad') {
                $name .= ' - ' . $variant;
            }

            $price = (float) $presentation->precio_efectivo;
            $key = $presentation->id_presentacion;
            if (isset($lines[$key])) {
                $lines[$key]['quantity'] += (int) $item['cantidad'];
                $lines[$key]['subtotal'] = $lines[$key]['quantity'] * $price;
                continue;
            }

            $lines[$key] = [
                'presentation' => $presentation,
                'name' => $name,
                'quantity' => (int) $item['cantidad'],
                'price' => $price,
                'subtotal' => $price * (int) $item['cantidad'],
            ];
        }

        $lines = array_values($lines);

        foreach ($lines as $line) {
            if ($stockControlEnabled && $line['quantity'] > $line['presentation']->stock) {
                return response()->json([
                    'message' => "Stock insuficiente para {$line['name']}.",
                ], 422);
            }
        }

        do {
            $codigo = '#WA-' . now()->format('ymd') . '-' . str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (PedidoWhatsapp::where('codigo_pedido', $codigo)->exists());

        $numeroEmpresa = $setting->whatsappNumberForUrl();
        $message = "Hola, quiero confirmar mi pedido *{$codigo}*.\n";
        foreach ($lines as $line) {
            $message .= "- {$line['quantity']} x {$line['name']}: S/ " . number_format($line['subtotal'], 2) . "\n";
        }
        $message .= "\nTotal productos: S/ " . number_format(collect($lines)->sum('subtotal'), 2);
        $message .= "\nDelivery {$zona->nombre}: S/ " . number_format((float) $zona->tarifa, 2);
        $message .= "\nTotal referencial del pedido: S/ " . number_format(collect($lines)->sum('subtotal') + (float) $zona->tarifa, 2);
        $whatsappUrl = "https://wa.me/{$numeroEmpresa}?text=" . urlencode($message);

        try {
            $pedido = DB::transaction(function () use ($codigo, $data, $zona, $lines, $whatsappUrl, $stockService, $stockControlEnabled, $audit, $request) {
                $totalProductos = collect($lines)->sum('subtotal');
                $pedido = PedidoWhatsapp::create([
                    'codigo_pedido' => $codigo,
                    'cliente_nombre' => $data['nombre'],
                    'cliente_whatsapp' => $data['whatsapp'],
                    'cliente_direccion' => $data['direccion'],
                    'cliente_referencia' => $data['referencia'] ?? '',
                    'id_zona_delivery' => $zona->id_zona,
                    'total_productos' => $totalProductos,
                    'costo_delivery' => (float) $zona->tarifa,
                    'total_pedido' => $totalProductos + (float) $zona->tarifa,
                    'estado' => 'Pendiente',
                    'whatsapp_url' => $whatsappUrl,
                ]);

                foreach ($lines as $line) {
                    if ($stockControlEnabled && ! $stockService->reserve($line['presentation'], $line['quantity'], $pedido->id_pedido_whatsapp, 'Reserva automatica al crear pedido API')) {
                        throw new RuntimeException("Stock insuficiente para {$line['name']}.");
                    }

                    PedidoWhatsappDetalle::create([
                        'id_pedido_whatsapp' => $pedido->id_pedido_whatsapp,
                        'id_producto' => $line['presentation']->id_producto,
                        'id_presentacion' => $line['presentation']->id_presentacion,
                        'nombre_producto' => $line['name'],
                        'precio_unitario' => $line['price'],
                        'cantidad_solicitada' => $line['quantity'],
                        'cantidad_confirmada' => $line['quantity'],
                        'subtotal' => $line['subtotal'],
                        'estado_item' => 'Solicitado',
                    ]);
                }

                $audit->log(
                    'crear_pedido_api',
                    'pedidos_tienda',
                    $pedido->id_pedido_whatsapp,
                    "Pedido {$pedido->codigo_pedido} creado desde API de tienda virtual",
                    null,
                    ['total' => $pedido->total_pedido, 'items' => count($lines)],
                    $request
                );

                return $pedido->load('detalles', 'zonaDelivery');
            });
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($pedido, 201);
    }

    public function myOrders(Request $request): JsonResponse
    {
        $whatsapp = $request->query('whatsapp');
        $orders = PedidoWhatsapp::with('detalles', 'zonaDelivery')
            ->when($whatsapp, fn ($q) => $q->where('cliente_whatsapp', $whatsapp))
            ->orderByDesc('id_pedido_whatsapp')
            ->paginate($request->integer('per_page', 20));

        return response()->json($orders);
    }

    public function showOrder(int $id): JsonResponse
    {
        return response()->json(
            PedidoWhatsapp::with('detalles', 'zonaDelivery')->findOrFail($id)
        );
    }
}
