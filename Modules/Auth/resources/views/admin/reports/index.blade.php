@extends('layouts.admin')

@section('title', 'Reportes')
@section('page-title', 'Reportes')
@section('page-kicker', 'Pedidos WhatsApp y catalogo')

@section('topbar-actions')
    <a href="{{ route('admin.reportes.export.pedidos', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}" class="btn btn-primary" style="text-decoration:none;">
        <i class="fas fa-file-csv"></i> Exportar CSV
    </a>
@endsection

@push('styles')
<style>
    .report-stack { display: flex; flex-direction: column; gap: 24px; }
    .panel-card { background: #ffffff; border: 1px solid var(--border); border-radius: 14px; box-shadow: var(--shadow); }
    .panel-head { padding: 22px 24px; border-bottom: 1px solid var(--border); display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap; }
    .panel-head h3 { margin: 0; color: #111827; font-size: 20px; font-weight: 900; }
    .panel-head p { margin: 4px 0 0; color: #64748b; font-size: 14px; }
    .panel-body { padding: 24px; }
    .filters { display: grid; grid-template-columns: repeat(2, minmax(0, 220px)) auto; gap: 12px; align-items: end; }
    .field { width: 100%; min-height: 44px; border: 1px solid var(--border); border-radius: 10px; background: #f8fafc; padding: 10px 12px; font-size: 14px; font-family: inherit; }
    .field:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(229, 140, 58, 0.12); background: #fff; }
    .kpi-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
    .kpi-card { background:#fff; border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); padding:18px; }
    .kpi-label { color:#64748b; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.04em; }
    .kpi-value { margin-top:6px; color:#111827; font-size:28px; font-weight:900; letter-spacing:-.04em; }
    .split { display:grid; grid-template-columns: minmax(0, 1fr) minmax(320px, .9fr); gap:16px; }
    .table { width:100%; border-collapse:collapse; }
    .table th { text-align:left; font-size:12px; color:#64748b; text-transform:uppercase; letter-spacing:.04em; padding-bottom:12px; border-bottom:1px solid #e5e7eb; }
    .table td { padding:14px 0; border-bottom:1px solid #eef2f7; color:#334155; font-size:14px; vertical-align:middle; }
    .table tr:last-child td { border-bottom:none; }
    .badge { display:inline-flex; align-items:center; min-height:26px; padding:0 10px; border-radius:999px; font-size:12px; font-weight:800; background:#eff6ff; color:#1d4ed8; }
    @media (max-width: 1100px) { .kpi-grid, .split, .filters { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="report-stack">
    <section class="panel-card">
        <div class="panel-head">
            <div>
                <h3>Filtro de reporte</h3>
                <p>Analiza pedidos WhatsApp, ventas estimadas, productos vendidos, zonas y alertas de stock por rango de fechas.</p>
            </div>
        </div>
        <div class="panel-body">
            <form method="GET" action="{{ route('admin.reportes.index') }}" class="filters">
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:800;color:#64748b;text-transform:uppercase;">Desde</label>
                    <input class="field" type="date" name="from" value="{{ $from->toDateString() }}">
                </div>
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:800;color:#64748b;text-transform:uppercase;">Hasta</label>
                    <input class="field" type="date" name="to" value="{{ $to->toDateString() }}">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Actualizar</button>
            </form>
        </div>
    </section>

    <div class="kpi-grid">
        <div class="kpi-card"><div class="kpi-label">Ventas estimadas</div><div class="kpi-value">S/ {{ number_format($grossRevenue, 2) }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Pedidos</div><div class="kpi-value">{{ $ordersCount }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Pendientes</div><div class="kpi-value">{{ $pendingCount }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Items vendidos</div><div class="kpi-value">{{ number_format($itemsSold) }}</div></div>
    </div>

    <div class="split">
        <section class="panel-card">
            <div class="panel-head"><div><h3>Top productos del periodo</h3><p>Ranking por unidades y total estimado.</p></div></div>
            <div class="panel-body">
                <table class="table">
                    <thead><tr><th>Producto</th><th>Unidades</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse($topProducts as $item)
                            <tr>
                                <td style="font-weight:800;color:#111827;">{{ $item->nombre_producto }}</td>
                                <td>{{ number_format($item->unidades) }}</td>
                                <td>S/ {{ number_format((float) $item->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" style="color:#64748b;">Sin ventas registradas en este rango.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel-card">
            <div class="panel-head"><div><h3>Estados y zonas</h3><p>Distribucion operativa del periodo.</p></div></div>
            <div class="panel-body">
                <div style="margin-bottom:18px;">
                    <div style="font-size:12px;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.04em;margin-bottom:10px;">Estados</div>
                    @forelse($statusSummary as $status)
                        <div style="display:flex;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid #eef2f7;">
                            <span>{{ $status->estado }}</span>
                            <strong>{{ $status->total }}</strong>
                        </div>
                    @empty
                        <div style="color:#64748b;">Sin estados para mostrar.</div>
                    @endforelse
                </div>
                <div>
                    <div style="font-size:12px;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.04em;margin-bottom:10px;">Zonas</div>
                    @forelse($zoneSummary as $zone)
                        <div style="padding:10px 0;border-bottom:1px solid #eef2f7;">
                            <div style="display:flex;justify-content:space-between;gap:12px;">
                                <span>{{ $zone->zona_nombre }}</span>
                                <strong>{{ $zone->total_pedidos }}</strong>
                            </div>
                            <div style="color:#64748b;font-size:12px;margin-top:3px;">S/ {{ number_format((float) $zone->total_ventas, 2) }}</div>
                        </div>
                    @empty
                        <div style="color:#64748b;">Sin zonas para mostrar.</div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>

    <div class="split">
        <section class="panel-card">
            <div class="panel-head"><div><h3>Pedidos recientes</h3><p>Ultimos movimientos dentro del rango filtrado.</p></div></div>
            <div class="panel-body">
                <table class="table">
                    <thead><tr><th>Codigo</th><th>Cliente</th><th>Zona</th><th>Estado</th><th>Total</th><th>Fecha</th></tr></thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td><span class="badge">{{ $order->codigo_pedido }}</span></td>
                                <td>{{ $order->cliente_nombre }}</td>
                                <td>{{ optional($order->zonaDelivery)->nombre ?: 'Sin zona' }}</td>
                                <td>{{ $order->estado }}</td>
                                <td>S/ {{ number_format((float) $order->total_pedido, 2) }}</td>
                                <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" style="color:#64748b;">No hay pedidos en este rango.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel-card">
            <div class="panel-head"><div><h3>Alertas de inventario</h3><p>Presentaciones activas con stock por debajo o igual al minimo.</p></div></div>
            <div class="panel-body">
                @forelse($lowStock as $presentation)
                    <div style="display:flex;justify-content:space-between;gap:12px;padding:12px 0;border-bottom:1px solid #eef2f7;">
                        <div>
                            <div style="font-weight:800;color:#111827;">{{ $presentation->producto->nombre_base ?? 'Producto' }}</div>
                            <div style="color:#64748b;font-size:13px;">{{ $presentation->nombre_variante }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:900;color:#dc2626;">{{ $presentation->stock_web }}</div>
                            <div style="color:#64748b;font-size:12px;">Min {{ $presentation->stock_web_minimo }}</div>
                        </div>
                    </div>
                @empty
                    <div style="color:#64748b;">No hay alertas de stock bajo en este momento.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
