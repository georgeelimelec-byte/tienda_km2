<?php

namespace Modules\Auth\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\Models\ProductoPresentacion;
use Modules\Storefront\Models\PedidoWhatsapp;
use Modules\Storefront\Models\PedidoWhatsappDetalle;

class AdminReportsController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $pedidosBase = PedidoWhatsapp::query()->whereBetween('created_at', [$from, $to]);

        $grossRevenue = (float) ((clone $pedidosBase)->sum('total_pedido') ?: 0);
        $ordersCount = (clone $pedidosBase)->count();
        $pendingCount = (clone $pedidosBase)->where('estado', 'Pendiente')->count();
        $itemsSold = PedidoWhatsappDetalle::query()
            ->join('pedidos_tienda', 'pedidos_tienda.id_pedido_whatsapp', '=', 'detalle_pedidos_tienda.id_pedido_whatsapp')
            ->whereBetween('pedidos_tienda.created_at', [$from, $to])
            ->sum('detalle_pedidos_tienda.cantidad_confirmada');

        $statusSummary = (clone $pedidosBase)
            ->select('estado')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('estado')
            ->orderBy('estado')
            ->get();

        $zoneSummary = PedidoWhatsapp::query()
            ->leftJoin('zonas_entrega', 'zonas_entrega.id_zona', '=', 'pedidos_tienda.id_zona_delivery')
            ->whereBetween('pedidos_tienda.created_at', [$from, $to])
            ->selectRaw('COALESCE(zonas_entrega.nombre, "Sin zona") as zona_nombre')
            ->selectRaw('COUNT(*) as total_pedidos')
            ->selectRaw('SUM(pedidos_tienda.total_pedido) as total_ventas')
            ->groupBy('zona_nombre')
            ->orderByDesc('total_ventas')
            ->get();

        $topProducts = PedidoWhatsappDetalle::query()
            ->join('pedidos_tienda', 'pedidos_tienda.id_pedido_whatsapp', '=', 'detalle_pedidos_tienda.id_pedido_whatsapp')
            ->whereBetween('pedidos_tienda.created_at', [$from, $to])
            ->select('detalle_pedidos_tienda.nombre_producto')
            ->selectRaw('SUM(detalle_pedidos_tienda.cantidad_confirmada) as unidades')
            ->selectRaw('SUM(detalle_pedidos_tienda.subtotal) as total')
            ->groupBy('detalle_pedidos_tienda.nombre_producto')
            ->orderByDesc('unidades')
            ->limit(10)
            ->get();

        $recentOrders = PedidoWhatsapp::with('zonaDelivery')
            ->whereBetween('created_at', [$from, $to])
            ->latest('created_at')
            ->limit(15)
            ->get();

        $lowStock = ProductoPresentacion::with('producto')
            ->where('estado', 'Activo')
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->orderBy('stock')
            ->limit(8)
            ->get();

        return view('auth::admin.reports.index', compact(
            'from',
            'to',
            'grossRevenue',
            'ordersCount',
            'pendingCount',
            'itemsSold',
            'statusSummary',
            'zoneSummary',
            'topProducts',
            'recentOrders',
            'lowStock'
        ));
    }

    public function exportOrdersCsv(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $rows = PedidoWhatsapp::with('zonaDelivery')
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->get();

        $filename = 'reporte-pedidos-whatsapp-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Codigo', 'Cliente', 'Numero de WhatsApp', 'Zona', 'Estado', 'Productos', 'Delivery', 'Total', 'Fecha']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->codigo_pedido,
                    $row->cliente_nombre,
                    $row->cliente_whatsapp,
                    optional($row->zonaDelivery)->nombre,
                    $row->estado,
                    number_format((float) $row->total_productos, 2, '.', ''),
                    number_format((float) $row->costo_delivery, 2, '.', ''),
                    number_format((float) $row->total_pedido, 2, '.', ''),
                    optional($row->created_at)->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function dateRange(Request $request): array
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }
}
