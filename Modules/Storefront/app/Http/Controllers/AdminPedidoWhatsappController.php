<?php

namespace Modules\Storefront\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Storefront\Models\PedidoWhatsapp;
use Modules\Storefront\Models\PedidoWhatsappDetalle;
use Modules\Storefront\Services\OperationalAudit;
use Modules\Storefront\Services\StockWebService;
use RuntimeException;

class AdminPedidoWhatsappController extends Controller
{
    private const STATUSES = [
        'Pendiente',
        'Observado',
        'Ajustado',
        'Confirmado',
        'En Preparacion',
        'En Delivery',
        'Entregado',
        'Cancelado',
    ];

    public function index(Request $request)
    {
        $estado = $request->query('estado');
        $search = $request->query('q');

        $pedidos = PedidoWhatsapp::with(['detalles.presentacion', 'zonaDelivery', 'operador'])
            ->when(in_array($estado, self::STATUSES, true), fn ($q) => $q->where('estado', $estado))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('codigo_pedido', 'LIKE', "%{$search}%")
                        ->orWhere('cliente_nombre', 'LIKE', "%{$search}%")
                        ->orWhere('cliente_whatsapp', 'LIKE', "%{$search}%");
                });
            })
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        $resumenEstados = PedidoWhatsapp::select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return view('storefront::admin.pedidos', [
            'pedidos' => $pedidos,
            'statuses' => self::STATUSES,
            'resumenEstados' => $resumenEstados,
            'activeStatus' => $estado,
            'search' => $search,
        ]);
    }

    public function updateStatus(Request $request, int $id, StockWebService $stockWeb, OperationalAudit $audit)
    {
        $data = $request->validate([
            'estado' => 'required|in:' . implode(',', self::STATUSES),
            'referencia_atencion' => 'nullable|string|max:80',
            'nota_interna' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($id, $data, $stockWeb, $audit, $request) {
                $pedido = PedidoWhatsapp::whereKey($id)->lockForUpdate()->firstOrFail();
                $pedido->load('detalles.presentacion');
                $previousStatus = $pedido->estado;

                if ($previousStatus !== 'Cancelado' && $data['estado'] === 'Cancelado') {
                    $this->restoreOrderStock($pedido, $stockWeb, 'Devolucion por cancelacion del pedido');
                }

                if ($previousStatus === 'Cancelado' && $data['estado'] !== 'Cancelado') {
                    $reserveError = $this->reserveOrderStock($pedido, $stockWeb, 'Nueva reserva al reactivar pedido cancelado');
                    if ($reserveError) {
                        throw new RuntimeException($reserveError);
                    }
                }

                $old = $pedido->only(['estado', 'referencia_atencion', 'nota_interna']);
                $pedido->fill([
                    'estado' => $data['estado'],
                    'referencia_atencion' => $data['referencia_atencion'] ?? $pedido->referencia_atencion,
                    'nota_interna' => $data['nota_interna'] ?? $pedido->nota_interna,
                    'id_operador' => Auth::id() ?: $pedido->id_operador,
                ])->save();

                $audit->log(
                    'actualizar_estado_pedido',
                    'pedidos_whatsapp',
                    $pedido->id_pedido_whatsapp,
                    "Pedido {$pedido->codigo_pedido} cambio de {$previousStatus} a {$pedido->estado}",
                    $old,
                    $pedido->only(['estado', 'referencia_atencion', 'nota_interna']),
                    $request
                );
            });
        } catch (RuntimeException $e) {
            return $this->statusResponse($request, false, $e->getMessage());
        }

        return $this->statusResponse($request, true, 'Estado del pedido actualizado.');
    }

    public function adjustItem(Request $request, int $id, int $detailId, StockWebService $stockWeb, OperationalAudit $audit)
    {
        $data = $request->validate([
            'cantidad_confirmada' => 'required|integer|min:0',
            'motivo_ajuste' => 'required|string|max:1000',
        ]);

        $result = DB::transaction(function () use ($id, $detailId, $data, $stockWeb, $audit, $request) {
            $pedido = PedidoWhatsapp::whereKey($id)->lockForUpdate()->firstOrFail();
            $detalle = PedidoWhatsappDetalle::with('presentacion')
                ->where('id_pedido_whatsapp', $pedido->id_pedido_whatsapp)
                ->where('id_detalle', $detailId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pedido->estado === 'Cancelado' || $pedido->estado === 'Entregado') {
                return ['ok' => false, 'message' => 'No se puede ajustar un pedido cancelado o entregado.'];
            }

            $oldQuantity = (int) $detalle->cantidad_confirmada;
            $newQuantity = (int) $data['cantidad_confirmada'];
            $diff = $newQuantity - $oldQuantity;

            if ($diff > 0 && ! $stockWeb->reserve($detalle->presentacion, $diff, $pedido->id_pedido_whatsapp, 'Reserva por ajuste operativo de pedido')) {
                return ['ok' => false, 'message' => "Stock web insuficiente para aumentar {$detalle->nombre_producto}."];
            }

            if ($diff < 0) {
                $stockWeb->restore($detalle->presentacion, abs($diff), $pedido->id_pedido_whatsapp, 'Devolucion por ajuste operativo de pedido');
            }

            $old = $detalle->only(['cantidad_solicitada', 'cantidad_confirmada', 'subtotal', 'motivo_ajuste', 'estado_item']);
            $detalle->update([
                'cantidad_confirmada' => $newQuantity,
                'subtotal' => (float) $detalle->precio_unitario * $newQuantity,
                'motivo_ajuste' => $data['motivo_ajuste'],
                'estado_item' => $this->itemStatus($detalle, $newQuantity),
            ]);

            $this->recalculateOrderTotals($pedido);

            if (!in_array($pedido->estado, ['Confirmado', 'En Preparacion', 'En Delivery'], true)) {
                $pedido->update([
                    'estado' => $newQuantity === (int) $detalle->cantidad_solicitada ? 'Observado' : 'Ajustado',
                    'id_operador' => Auth::id() ?: $pedido->id_operador,
                ]);
            }

            $audit->log(
                'ajustar_item_pedido',
                'pedidos_whatsapp_detalles',
                $detalle->id_detalle,
                "Se ajusto {$detalle->nombre_producto} en el pedido {$pedido->codigo_pedido} de {$oldQuantity} a {$newQuantity}",
                $old,
                $detalle->fresh()->only(['cantidad_solicitada', 'cantidad_confirmada', 'subtotal', 'motivo_ajuste', 'estado_item']),
                $request
            );

            return ['ok' => true];
        });

        if (!$result['ok']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', 'Item del pedido ajustado y stock web actualizado.');
    }

    public function ticket(int $id)
    {
        $pedido = PedidoWhatsapp::with('detalles', 'zonaDelivery')->findOrFail($id);

        return view('storefront::admin.pedido-ticket', compact('pedido'));
    }

    private function restoreOrderStock(PedidoWhatsapp $pedido, StockWebService $stockWeb, string $motivo): void
    {
        foreach ($pedido->detalles as $detalle) {
            if ($detalle->presentacion) {
                $stockWeb->restore($detalle->presentacion, (int) $detalle->cantidad_confirmada, $pedido->id_pedido_whatsapp, $motivo);
            }
        }
    }

    private function reserveOrderStock(PedidoWhatsapp $pedido, StockWebService $stockWeb, string $motivo): ?string
    {
        foreach ($pedido->detalles as $detalle) {
            if (! $detalle->presentacion) {
                continue;
            }

            if (! $stockWeb->reserve($detalle->presentacion, (int) $detalle->cantidad_confirmada, $pedido->id_pedido_whatsapp, $motivo)) {
                return "Stock web insuficiente para reactivar {$detalle->nombre_producto}.";
            }
        }

        return null;
    }

    private function recalculateOrderTotals(PedidoWhatsapp $pedido): void
    {
        $totalProductos = (float) PedidoWhatsappDetalle::where('id_pedido_whatsapp', $pedido->id_pedido_whatsapp)->sum('subtotal');

        $pedido->update([
            'total_productos' => $totalProductos,
            'total_pedido' => $totalProductos + (float) $pedido->costo_delivery,
            'id_operador' => Auth::id() ?: $pedido->id_operador,
        ]);
    }

    private function itemStatus(PedidoWhatsappDetalle $detalle, int $confirmedQuantity): string
    {
        if ($confirmedQuantity <= 0) {
            return 'Sin stock';
        }

        if ($confirmedQuantity !== (int) $detalle->cantidad_solicitada) {
            return 'Ajustado';
        }

        return 'Confirmado';
    }

    private function statusResponse(Request $request, bool $success, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => $success, 'message' => $message], $success ? 200 : 422);
        }

        return back()->with($success ? 'success' : 'error', $message);
    }
}
