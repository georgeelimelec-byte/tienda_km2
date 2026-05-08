@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard KM2')
@section('page-kicker', 'Centro de control')

@section('topbar-actions')
    <a href="{{ route('storefront.index') }}" class="topbar-badge" style="text-decoration:none;">
        <i class="fas fa-arrow-up-right-from-square"></i> Ver tienda
    </a>
@endsection

@push('styles')
<style>
    .dashboard-wrap {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .control-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(229, 140, 58, 0.20);
        border-radius: 14px;
        background:
            linear-gradient(115deg, rgba(17, 24, 39, 0.95), rgba(67, 20, 7, 0.80)),
            radial-gradient(circle at 84% 12%, rgba(249, 115, 22, 0.22), transparent 34%);
        color: #ffffff;
        padding: 30px;
        box-shadow: 0 22px 54px -34px rgba(15, 23, 42, 0.65);
    }

    .control-hero::after {
        content: '';
        position: absolute;
        right: -70px;
        bottom: -90px;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: rgba(249, 115, 22, 0.18);
        filter: blur(2px);
        pointer-events: none;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        gap: 24px;
    }

    .hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        color: #fed7aa;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .hero-eyebrow::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #22c55e;
        box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.12);
    }

    .control-hero h1 {
        margin: 0;
        max-width: 680px;
        font-size: clamp(28px, 4vw, 44px);
        line-height: 1.02;
        letter-spacing: -0.045em;
        font-weight: 900;
    }

    .control-hero p {
        max-width: 720px;
        margin-top: 12px;
        color: rgba(255, 255, 255, 0.78);
        font-size: 15px;
        line-height: 1.65;
    }

    .hero-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .hero-button {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        min-height: 42px;
        padding: 0 15px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: rgba(255, 255, 255, 0.10);
        color: #ffffff;
        text-decoration: none;
        font-size: 13px;
        font-weight: 800;
        transition: transform 0.2s ease, background 0.2s ease;
    }

    .hero-button.primary {
        border-color: transparent;
        background: #f97316;
        box-shadow: 0 18px 32px -24px rgba(249, 115, 22, 0.9);
    }

    .hero-button:hover {
        transform: translateY(-1px);
        background: rgba(255, 255, 255, 0.16);
    }

    .hero-button.primary:hover {
        background: #ea580c;
    }

    .metric-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .metric-card {
        display: flex;
        align-items: center;
        gap: 14px;
        min-height: 104px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #ffffff;
        padding: 18px;
        box-shadow: var(--shadow);
    }

    .metric-icon {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        flex-shrink: 0;
        font-size: 18px;
    }

    .metric-icon.orange { background: #fff7ed; color: #ea580c; }
    .metric-icon.green { background: #ecfdf5; color: #16a34a; }
    .metric-icon.blue { background: #eff6ff; color: #2563eb; }
    .metric-icon.slate { background: #f1f5f9; color: #475569; }

    .metric-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .metric-value {
        margin-top: 3px;
        color: #111827;
        font-size: 28px;
        font-weight: 900;
        letter-spacing: -0.04em;
    }

    .dashboard-section {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .section-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
    }

    .section-head h2 {
        margin: 0;
        color: #111827;
        font-size: 21px;
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .section-head p {
        margin-top: 4px;
        color: #64748b;
        font-size: 14px;
    }

    .module-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .module-card {
        position: relative;
        min-height: 172px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #ffffff;
        padding: 18px;
        color: #111827;
        text-decoration: none;
        box-shadow: var(--shadow);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    a.module-card:hover {
        transform: translateY(-3px);
        border-color: rgba(249, 115, 22, 0.30);
        box-shadow: var(--shadow-lg);
    }

    .module-card.is-planned {
        background: linear-gradient(180deg, #ffffff, #fbfbfc);
        border-style: dashed;
    }

    .module-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .module-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #fff7ed;
        color: #ea580c;
        font-size: 17px;
    }

    .module-status {
        display: inline-flex;
        align-items: center;
        min-height: 24px;
        padding: 0 9px;
        border-radius: 999px;
        background: #ecfdf5;
        color: #047857;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .module-status.pending {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .module-card h3 {
        margin: 0 0 8px;
        color: #111827;
        font-size: 17px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .module-card p {
        color: #64748b;
        font-size: 13px;
        line-height: 1.55;
    }

    .module-action {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-top: 18px;
        color: #ea580c;
        font-size: 13px;
        font-weight: 900;
    }

    .data-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr);
        gap: 16px;
    }

    .data-card {
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #ffffff;
        padding: 20px;
        box-shadow: var(--shadow);
    }

    .data-card h3 {
        margin: 0 0 10px;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .data-card p {
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }

    .pipeline {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-top: 18px;
    }

    .pipeline-step {
        min-height: 96px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        padding: 12px;
    }

    .pipeline-step strong {
        display: block;
        color: #111827;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .pipeline-step span {
        display: block;
        color: #64748b;
        font-size: 12px;
        line-height: 1.45;
    }

    .check-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 14px;
    }

    .check-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        color: #475569;
        font-size: 13px;
        line-height: 1.5;
    }

    .check-item i {
        margin-top: 3px;
        color: #22c55e;
    }

    @media (max-width: 1180px) {
        .metric-grid,
        .module-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .data-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 720px) {
        .control-hero {
            padding: 24px;
        }

        .hero-content {
            grid-template-columns: 1fr;
        }

        .hero-actions {
            justify-content: flex-start;
        }

        .metric-grid,
        .module-grid,
        .pipeline {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $activeProducts = \Modules\Inventory\Models\Producto::where('estado', 'Activo')->count();
    $activeBanners = \Modules\Storefront\Models\BannerWeb::where('estado', 'Activo')->count();
    $activeZones = \Modules\Storefront\Models\ZonaDelivery::where('estado', 'Activo')->count();
    $pendingOrders = \Modules\Storefront\Models\PedidoWhatsapp::where('estado', 'Pendiente')->count();
    $usersCount = \Modules\Auth\Models\Usuario::count();
    $rolesCount = \Modules\Auth\Models\Role::count();

    $modules = [
        [
            'title' => 'Bandeja de pedidos',
            'description' => 'Gestiona pedidos WhatsApp, estados, tickets y confirmaciones de clientes.',
            'icon' => 'fa-clipboard-list',
            'route' => \Illuminate\Support\Facades\Route::has('admin.pedidos.index') ? route('admin.pedidos.index') : null,
            'status' => 'Activo',
            'action' => 'Abrir pedidos',
        ],
        [
            'title' => 'Catalogo',
            'description' => 'Administra productos, fotos, precios, disponibilidad y variantes visibles en tienda.',
            'icon' => 'fa-boxes-stacked',
            'route' => \Illuminate\Support\Facades\Route::has('admin.productos.index') ? route('admin.productos.index') : null,
            'status' => 'Activo',
            'action' => 'Gestionar catalogo',
        ],
        [
            'title' => 'Categorias',
            'description' => 'Ordena la vitrina por categorias padre, subcategorias y niveles internos.',
            'icon' => 'fa-tags',
            'route' => \Illuminate\Support\Facades\Route::has('admin.categorias.index') ? route('admin.categorias.index') : null,
            'status' => 'Activo',
            'action' => 'Gestionar categorias',
        ],
        [
            'title' => 'Banners y promociones',
            'description' => 'Controla imagenes del carrusel principal, estado, enlaces y campanas visibles.',
            'icon' => 'fa-images',
            'route' => \Illuminate\Support\Facades\Route::has('admin.banners.index') ? route('admin.banners.index') : null,
            'status' => 'Activo',
            'action' => 'Gestionar banners',
        ],
        [
            'title' => 'Delivery y zonas',
            'description' => 'Define zonas, tarifas y disponibilidad de entrega para pedidos web.',
            'icon' => 'fa-map-location-dot',
            'route' => \Illuminate\Support\Facades\Route::has('admin.zonas.index') ? route('admin.zonas.index') : null,
            'status' => 'Activo',
            'action' => 'Configurar delivery',
        ],
        [
            'title' => 'Catalogo tecnico',
            'description' => 'Gestiona maestro-detalle, codigos de barra, variantes, costos y stock directo.',
            'icon' => 'fa-layer-group',
            'route' => \Illuminate\Support\Facades\Route::has('inventory.products') ? route('inventory.products') : null,
            'status' => 'Activo',
            'action' => 'Abrir catalogo',
        ],
    ];

    $adminModules = [
        [
            'title' => 'Usuarios y accesos',
            'icon' => 'fa-users',
            'description' => 'Alta, baja y mantenimiento de usuarios internos con estado, correo y asignacion directa de rol.',
            'route' => \Illuminate\Support\Facades\Route::has('admin.usuarios.index') ? route('admin.usuarios.index') : null,
            'status' => \Illuminate\Support\Facades\Route::has('admin.usuarios.index') ? 'Activo' : 'Pendiente',
            'action' => \Illuminate\Support\Facades\Route::has('admin.usuarios.index') ? 'Abrir usuarios' : 'En espera',
        ],
        [
            'title' => 'Permisos granulares',
            'icon' => 'fa-key',
            'description' => 'Control por modulo y accion: leer, crear, editar y eliminar por rol o por usuario.',
            'route' => \Illuminate\Support\Facades\Route::has('admin.permisos.index') ? route('admin.permisos.index') : null,
            'status' => \Illuminate\Support\Facades\Route::has('admin.permisos.index') ? 'Activo' : 'Pendiente',
            'action' => \Illuminate\Support\Facades\Route::has('admin.permisos.index') ? 'Abrir permisos' : 'En espera',
        ],
        [
            'title' => 'Configuracion',
            'icon' => 'fa-gear',
            'description' => 'Centro de ajustes para apariencia de tienda, datos comerciales, usuarios, roles y permisos.',
            'route' => \Illuminate\Support\Facades\Route::has('admin.configuracion.index') ? route('admin.configuracion.index') : null,
            'status' => \Illuminate\Support\Facades\Route::has('admin.configuracion.index') ? 'Activo' : 'Pendiente',
            'action' => \Illuminate\Support\Facades\Route::has('admin.configuracion.index') ? 'Abrir configuracion' : 'En espera',
        ],
        [
            'title' => 'Reportes',
            'icon' => 'fa-chart-column',
            'description' => 'Exportacion de pedidos WhatsApp, ventas estimadas, zonas y productos top por rango de fechas.',
            'route' => \Illuminate\Support\Facades\Route::has('admin.reportes.index') ? route('admin.reportes.index') : null,
            'status' => \Illuminate\Support\Facades\Route::has('admin.reportes.index') ? 'Activo' : 'Pendiente',
            'action' => \Illuminate\Support\Facades\Route::has('admin.reportes.index') ? 'Abrir reportes' : 'En espera',
        ],
        [
            'title' => 'Business & data',
            'icon' => 'fa-chart-line',
            'description' => 'Dashboard ejecutivo con ticket promedio, demanda por categoria, mix de estados y tendencia diaria.',
            'route' => \Illuminate\Support\Facades\Route::has('admin.business.index') ? route('admin.business.index') : null,
            'status' => \Illuminate\Support\Facades\Route::has('admin.business.index') ? 'Activo' : 'Pendiente',
            'action' => \Illuminate\Support\Facades\Route::has('admin.business.index') ? 'Abrir analitica' : 'En espera',
        ],
    ];
@endphp

<div class="dashboard-wrap">
    <section class="control-hero">
        <div class="hero-content">
            <div>
                <span class="hero-eyebrow">Operaciones en linea</span>
                <h1>Gestiona tienda, catalogo, pedidos y crecimiento desde un solo panel.</h1>
                <p>
                    Este dashboard queda organizado como centro de trabajo: modulos activos para operar hoy y una ruta clara para completar configuracion, usuarios, roles, permisos, reportes y analitica del negocio.
                </p>
            </div>
            <div class="hero-actions">
                @if(\Illuminate\Support\Facades\Route::has('admin.productos.index'))
                    <a href="{{ route('admin.productos.index') }}" class="hero-button primary">
                        <i class="fas fa-plus"></i> Producto
                    </a>
                @endif
                <a href="{{ route('storefront.index') }}" class="hero-button">
                    <i class="fas fa-store"></i> Ver tienda
                </a>
            </div>
        </div>
    </section>

    <section class="metric-grid" aria-label="Indicadores principales">
        <div class="metric-card">
            <span class="metric-icon orange"><i class="fas fa-clipboard-list"></i></span>
            <div>
                <div class="metric-label">Pedidos pendientes</div>
                <div class="metric-value">{{ $pendingOrders }}</div>
            </div>
        </div>
        <div class="metric-card">
            <span class="metric-icon green"><i class="fas fa-box-open"></i></span>
            <div>
                <div class="metric-label">Productos activos</div>
                <div class="metric-value">{{ $activeProducts }}</div>
            </div>
        </div>
        <div class="metric-card">
            <span class="metric-icon blue"><i class="fas fa-image"></i></span>
            <div>
                <div class="metric-label">Banners activos</div>
                <div class="metric-value">{{ $activeBanners }}</div>
            </div>
        </div>
        <div class="metric-card">
            <span class="metric-icon slate"><i class="fas fa-user-gear"></i></span>
            <div>
                <div class="metric-label">Usuarios / roles</div>
                <div class="metric-value">{{ $usersCount }} / {{ $rolesCount }}</div>
            </div>
        </div>
    </section>

    <section class="dashboard-section">
        <div class="section-head">
            <div>
                <h2>Modulos operativos</h2>
                <p>Funciones disponibles para administrar la tienda y su vitrina publica.</p>
            </div>
        </div>

        <div class="module-grid">
            @foreach($modules as $module)
                @if($module['route'])
                    <a href="{{ $module['route'] }}" class="module-card">
                @else
                    <div class="module-card is-planned">
                @endif
                    <div>
                        <div class="module-top">
                            <span class="module-icon"><i class="fas {{ $module['icon'] }}"></i></span>
                            <span class="module-status">{{ $module['status'] }}</span>
                        </div>
                        <h3>{{ $module['title'] }}</h3>
                        <p>{{ $module['description'] }}</p>
                    </div>
                    <span class="module-action">
                        {{ $module['action'] }} <i class="fas fa-arrow-right"></i>
                    </span>
                @if($module['route'])
                    </a>
                @else
                    </div>
                @endif
            @endforeach
        </div>
    </section>

    <section class="dashboard-section">
        <div class="section-head">
            <div>
                <h2>Gestion administrativa y data</h2>
                <p>Interfaces de control ya conectadas para usuarios, seguridad, configuracion, reportes y capa ejecutiva.</p>
            </div>
        </div>

        <div class="module-grid">
            @foreach($adminModules as $module)
                @if($module['route'])
                    <a href="{{ $module['route'] }}" class="module-card">
                @else
                    <div class="module-card is-planned">
                @endif
                    <div>
                        <div class="module-top">
                            <span class="module-icon"><i class="fas {{ $module['icon'] }}"></i></span>
                            <span class="module-status {{ $module['status'] === 'Activo' ? '' : 'pending' }}">{{ $module['status'] }}</span>
                        </div>
                        <h3>{{ $module['title'] }}</h3>
                        <p>{{ $module['description'] }}</p>
                    </div>
                    <span class="module-action" @if(!$module['route']) style="color:#64748b;" @endif>
                        {{ $module['action'] }} <i class="fas {{ $module['route'] ? 'fa-arrow-right' : 'fa-lock' }}"></i>
                    </span>
                @if($module['route'])
                    </a>
                @else
                    </div>
                @endif
            @endforeach
        </div>
    </section>

    <section class="data-layout">
        <div class="data-card">
            <h3>Ruta para dashboard business y data</h3>
            <p>
                Ya existe una primera capa de metricas conectada a pedidos WhatsApp, detalle de productos, catalogo, banners y usuarios. La siguiente fase natural es sumar tracking de conversion web y exportaciones adicionales.
            </p>
            <div class="pipeline">
                <div class="pipeline-step">
                    <strong>1. Datos</strong>
                    <span>Pedidos, productos, stock, zonas y usuarios ya consolidados.</span>
                </div>
                <div class="pipeline-step">
                    <strong>2. Metricas</strong>
                    <span>Ticket promedio, top productos, estados y ventas por periodo activos.</span>
                </div>
                <div class="pipeline-step">
                    <strong>3. Reportes</strong>
                    <span>Filtro por fecha, exportacion CSV y alertas operativas ya disponibles.</span>
                </div>
                <div class="pipeline-step">
                    <strong>4. Exportacion</strong>
                    <span>Siguiente ampliacion: Excel, PDF y tracking de conversion del storefront.</span>
                </div>
            </div>
        </div>

        <div class="data-card">
            <h3>Base de control detectada</h3>
            <p>La base ya quedo enlazada con interfaz administrativa para seguridad, configuracion y reportes.</p>
            <div class="check-list">
                <div class="check-item"><i class="fas fa-check-circle"></i> Roles registrados para clasificar usuarios.</div>
                <div class="check-item"><i class="fas fa-check-circle"></i> Permisos por rol y permisos por usuario disponibles en base de datos.</div>
                <div class="check-item"><i class="fas fa-check-circle"></i> Middleware de permisos granular ya existe para proteger modulos.</div>
                <div class="check-item"><i class="fas fa-check-circle"></i> Pantallas de usuarios, roles, permisos, configuracion, reportes y business data ya operativas.</div>
            </div>
        </div>
    </section>
</div>
@endsection
