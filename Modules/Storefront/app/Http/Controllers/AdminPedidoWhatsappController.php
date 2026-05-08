<?php

namespace Modules\Storefront\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Storefront\Models\PedidoWhatsapp;

class AdminPedidoWhatsappController extends Controller
{
    private const STATUSES = ['Pendiente', 'Confirmado', 'En Preparacion', 'En Reparto', 'Entregado', 'Cancelado'];

    public function index()
    {
        $rawOrders = PedidoWhatsapp::with('detalles', 'zonaDelivery')
            ->latest('created_at')
            ->get();

        $pedidos = collect(self::STATUSES)
            ->mapWithKeys(fn (string $status) => [$status => []])
            ->all();

        foreach ($rawOrders as $order) {
            if (array_key_exists($order->estado, $pedidos)) {
                $pedidos[$order->estado][] = $order;
            }
        }

        return view('storefront::admin.pedidos', [
            'pedidos' => $pedidos,
            'statuses' => self::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'estado' => 'required|in:' . implode(',', self::STATUSES),
            'comprobante_referencia' => 'nullable|string|max:80',
            'nota_interna' => 'nullable|string|max:1000',
        ]);

        $pedido = PedidoWhatsapp::findOrFail($id);
        $pedido->fill([
            'estado' => $data['estado'],
            'comprobante_referencia' => $data['comprobante_referencia'] ?? $pedido->comprobante_referencia,
            'nota_interna' => $data['nota_interna'] ?? $pedido->nota_interna,
            'id_operador' => Auth::id() ?: $pedido->id_operador,
        ])->save();

        return response()->json(['success' => true, 'pedido' => $pedido]);
    }

    public function ticket(int $id)
    {
        $pedido = PedidoWhatsapp::with('detalles', 'zonaDelivery')->findOrFail($id);

        return view('storefront::admin.pedido-ticket', compact('pedido'));
    }
}
