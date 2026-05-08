<?php

namespace Modules\Auth\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Models\Usuario;
use Modules\Inventory\Models\Producto;
use Modules\Inventory\Models\ProductoPresentacion;
use Modules\Storefront\Models\BannerWeb;
use Modules\Storefront\Models\PedidoWhatsapp;
use Modules\Storefront\Models\PedidoWhatsappDetalle;

class AdminBusinessController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $pedidosBase = PedidoWhatsapp::query()->whereBetween('created_at', [$from, $to]);
        $ordersCount = (clone $pedidosBase)->count();
        $estimatedRevenue = (float) ((clone $pedidosBase)->sum('total_pedido') ?: 0);
        $avgTicket = $ordersCount > 0 ? $estimatedRevenue / $ordersCount : 0;
        $pendingCount = (clone $pedidosBase)->where('estado', 'Pendiente')->count();
        $fulfilledCount = (clone $pedidosBase)->whereIn('estado', ['En Reparto', 'Entregado'])->count();
        $paidCount = (clone $pedidosBase)->where('estado', 'Confirmado')->count();

        $dailyTrend = PedidoWhatsapp::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as dia')
            ->selectRaw('COUNT(*) as pedidos')
            ->selectRaw('SUM(total_pedido) as ventas')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        $maxRevenue = max(1, (float) $dailyTrend->max('ventas'));

        $categoryPerformance = PedidoWhatsappDetalle::query()
            ->join('pedidos_whatsapp', 'pedidos_whatsapp.id_pedido_whatsapp', '=', 'pedidos_whatsapp_detalles.id_pedido_whatsapp')
            ->join('productos', 'productos.id_producto', '=', 'pedidos_whatsapp_detalles.id_producto')
            ->join('categorias', 'categorias.id_categoria', '=', 'productos.id_categoria')
            ->whereBetween('pedidos_whatsapp.created_at', [$from, $to])
            ->select('categorias.nombre')
            ->selectRaw('SUM(pedidos_whatsapp_detalles.cantidad) as unidades')
            ->selectRaw('SUM(pedidos_whatsapp_detalles.subtotal) as total')
            ->groupBy('categorias.nombre')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $topProducts = PedidoWhatsappDetalle::query()
            ->join('pedidos_whatsapp', 'pedidos_whatsapp.id_pedido_whatsapp', '=', 'pedidos_whatsapp_detalles.id_pedido_whatsapp')
            ->whereBetween('pedidos_whatsapp.created_at', [$from, $to])
            ->select('pedidos_whatsapp_detalles.nombre_producto')
            ->selectRaw('SUM(pedidos_whatsapp_detalles.cantidad) as unidades')
            ->selectRaw('SUM(pedidos_whatsapp_detalles.subtotal) as total')
            ->groupBy('pedidos_whatsapp_detalles.nombre_producto')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $statusMix = (clone $pedidosBase)
            ->select('estado')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('estado')
            ->orderByDesc('total')
            ->get();

        $lowStockCount = ProductoPresentacion::where('estado', 'Activo')
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->count();

        $activeProducts = Producto::where('estado', 'Activo')->count();
        $activePromotions = ProductoPresentacion::where('estado', 'Activo')
            ->whereNotNull('precio_oferta')
            ->whereColumn('precio_oferta', '<', 'precio')
            ->count();

        $activeBanners = BannerWeb::where('estado', 'Activo')->count();
        $usersCount = Usuario::count();

        return view('auth::admin.business.index', compact(
            'from',
            'to',
            'ordersCount',
            'estimatedRevenue',
            'avgTicket',
            'pendingCount',
            'fulfilledCount',
            'paidCount',
            'dailyTrend',
            'maxRevenue',
            'categoryPerformance',
            'topProducts',
            'statusMix',
            'lowStockCount',
            'activeProducts',
            'activePromotions',
            'activeBanners',
            'usersCount'
        ));
    }

    private function dateRange(Request $request): array
    {
        $from = Carbon::parse($request->input('from', now()->subDays(29)->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }
}
