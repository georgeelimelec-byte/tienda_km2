@extends('layouts.admin')

@section('title', 'Business data')
@section('page-title', 'Business & data')
@section('page-kicker', 'Dashboard ejecutivo')

@push('styles')
<style>
    .biz-stack { display: flex; flex-direction: column; gap: 24px; }
    .panel-card { background: #ffffff; border: 1px solid var(--border); border-radius: 14px; box-shadow: var(--shadow); }
    .panel-head { padding: 22px 24px; border-bottom: 1px solid var(--border); display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap; }
    .panel-head h3 { margin: 0; color: #111827; font-size: 20px; font-weight: 900; }
    .panel-head p { margin: 4px 0 0; color: #64748b; font-size: 14px; }
    .panel-body { padding: 24px; }
    .filters { display:grid; grid-template-columns: repeat(2, minmax(0, 220px)) auto; gap:12px; align-items:end; }
    .field { width: 100%; min-height: 44px; border: 1px solid var(--border); border-radius: 10px; background: #f8fafc; padding: 10px 12px; font-size: 14px; font-family: inherit; }
    .field:focus { outline:none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(229, 140, 58, 0.12); background:#fff; }
    .kpi-grid { display:grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap:16px; }
    .kpi-card { background:#fff; border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); padding:18px; }
    .kpi-label { color:#64748b; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.04em; }
    .kpi-value { margin-top:6px; color:#111827; font-size:26px; font-weight:900; letter-spacing:-.04em; }
    .split { display:grid; grid-template-columns: minmax(0, 1.2fr) minmax(320px, .8fr); gap:16px; }
    .table { width:100%; border-collapse:collapse; }
    .table th { text-align:left; font-size:12px; color:#64748b; text-transform:uppercase; letter-spacing:.04em; padding-bottom:12px; border-bottom:1px solid #e5e7eb; }
    .table td { padding:14px 0; border-bottom:1px solid #eef2f7; color:#334155; font-size:14px; vertical-align:middle; }
    .table tr:last-child td { border-bottom:none; }
    .bars { display:flex; align-items:flex-end; gap:10px; min-height:240px; }
    .bar-col { flex:1; display:flex; flex-direction:column; justify-content:flex-end; gap:8px; }
    .bar { border-radius:10px 10px 4px 4px; background: linear-gradient(180deg, var(--primary), var(--primary-dark)); min-height: 8px; }
    .bar-meta { color:#64748b; font-size:12px; text-align:center; }
    .status-list { display:flex; flex-direction:column; gap:10px; }
    .status-item { border:1px solid #e5e7eb; border-radius:12px; padding:12px 14px; display:flex; justify-content:space-between; gap:12px; align-items:center; }
    .note { padding:14px 16px; border-radius:12px; background:#fff7ed; color:#9a3412; font-size:13px; line-height:1.55; border:1px solid #fed7aa; }
    @media (max-width: 1200px) { .kpi-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } .split, .filters { grid-template-columns: 1fr; } }
    @media (max-width: 760px) { .kpi-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="biz-stack">
    <section class="panel-card">
        <div class="panel-head">
            <div>
                <h3>Filtro ejecutivo</h3>
                <p>Metricas de demanda, ticket, mix de estados y rendimiento comercial con la data real disponible hoy.</p>
            </div>
        </div>
        <div class="panel-body">
            <form method="GET" action="{{ route('admin.business.index') }}" class="filters">
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:800;color:#64748b;text-transform:uppercase;">Desde</label>
                    <input class="field" type="date" name="from" value="{{ $from->toDateString() }}">
                </div>
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:800;color:#64748b;text-transform:uppercase;">Hasta</label>
                    <input class="field" type="date" name="to" value="{{ $to->toDateString() }}">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-chart-line"></i> Actualizar</button>
            </form>
        </div>
    </section>

    <div class="kpi-grid">
        <div class="kpi-card"><div class="kpi-label">Ingresos estimados</div><div class="kpi-value">S/ {{ number_format($estimatedRevenue, 2) }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Pedidos</div><div class="kpi-value">{{ $ordersCount }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Ticket promedio</div><div class="kpi-value">S/ {{ number_format($avgTicket, 2) }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Pagados</div><div class="kpi-value">{{ $paidCount }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Despachados / entregados</div><div class="kpi-value">{{ $fulfilledCount }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Pendientes</div><div class="kpi-value">{{ $pendingCount }}</div></div>
    </div>

    <div class="split">
        <section class="panel-card">
            <div class="panel-head">
                <div>
                    <h3>Tendencia diaria</h3>
                    <p>Pedidos y ventas estimadas por dia dentro del periodo seleccionado.</p>
                </div>
            </div>
            <div class="panel-body">
                <div class="bars">
                    @forelse($dailyTrend as $day)
                        @php
                            $height = max(8, round((((float) $day->ventas) / $maxRevenue) * 180));
                        @endphp
                        <div class="bar-col">
                            <div class="bar" style="height: {{ $height }}px;"></div>
                            <div class="bar-meta">{{ \Carbon\Carbon::parse($day->dia)->format('d/m') }}</div>
                            <div class="bar-meta" style="font-weight:800;color:#111827;">S/ {{ number_format((float) $day->ventas, 0) }}</div>
                        </div>
                    @empty
                        <div class="note" style="width:100%;">Todavia no hay suficientes pedidos en el periodo para construir una tendencia diaria.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="panel-card">
            <div class="panel-head">
                <div>
                    <h3>Salud operativa</h3>
                    <p>Indicadores complementarios del sistema comercial.</p>
                </div>
            </div>
            <div class="panel-body">
                <div class="status-list">
                    <div class="status-item"><span>Productos activos</span><strong>{{ $activeProducts }}</strong></div>
                    <div class="status-item"><span>Promociones activas</span><strong>{{ $activePromotions }}</strong></div>
                    <div class="status-item"><span>Banners activos</span><strong>{{ $activeBanners }}</strong></div>
                    <div class="status-item"><span>Usuarios del sistema</span><strong>{{ $usersCount }}</strong></div>
                    <div class="status-item"><span>Alertas de stock bajo</span><strong>{{ $lowStockCount }}</strong></div>
                </div>
                <div class="note" style="margin-top:16px;">
                    Este tablero usa data operativa real de pedidos WhatsApp, detalle de productos, catalogo y banners. La conversion web real aun requiere tracking de sesiones o eventos del storefront.
                </div>
            </div>
        </section>
    </div>

    <div class="split">
        <section class="panel-card">
            <div class="panel-head"><div><h3>Categorias con mayor demanda</h3><p>Ranking por unidades y total vendido estimado.</p></div></div>
            <div class="panel-body">
                <table class="table">
                    <thead><tr><th>Categoria</th><th>Unidades</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse($categoryPerformance as $row)
                            <tr>
                                <td style="font-weight:800;color:#111827;">{{ $row->nombre }}</td>
                                <td>{{ number_format($row->unidades) }}</td>
                                <td>S/ {{ number_format((float) $row->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" style="color:#64748b;">Sin categorias con ventas en este periodo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel-card">
            <div class="panel-head"><div><h3>Mix de estados</h3><p>Distribucion operativa de los pedidos.</p></div></div>
            <div class="panel-body">
                <div class="status-list">
                    @forelse($statusMix as $status)
                        <div class="status-item">
                            <span>{{ $status->estado }}</span>
                            <strong>{{ $status->total }}</strong>
                        </div>
                    @empty
                        <div class="note">No hay estados registrados para el periodo seleccionado.</div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>

    <section class="panel-card">
        <div class="panel-head"><div><h3>Top productos por ingreso estimado</h3><p>Productos con mejor respuesta comercial en el periodo.</p></div></div>
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
                        <tr><td colspan="3" style="color:#64748b;">Sin productos vendidos en este periodo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
