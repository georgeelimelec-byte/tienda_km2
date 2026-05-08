<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KM2') — Panel de Administración</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            DEFAULT: '#f97316',
                            light: '#fb923c',
                            dark: '#ea580c',
                        }
                    }
                }
            }
        }
    </script>
    @livewireStyles
    <style>
        :root {
            --primary: #e58c3a;
            --primary-dark: #cc7626;
            --primary-light: #eda864;
            --accent: #f5b041;
            --bg-dark: #f9f8f6;
            --bg-card: #ffffff;
            --bg-card-hover: #fcfbfa;
            --bg-sidebar: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --border: #ece8e3;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --radius: 16px;
            --shadow: 0 4px 24px rgba(0,0,0,0.03), 0 2px 8px rgba(0,0,0,0.02);
            --shadow-lg: 0 12px 32px rgba(0,0,0,0.05), 0 4px 12px rgba(0,0,0,0.03);
            --header-height: 84px;
            --sidebar-width: 260px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        body.admin-panel-modal-open {
            overflow: hidden;
        }

        /* ═══════════ SIDEBAR ═══════════ */
        .sidebar {
            position: fixed;
            left: 0; top: 0; bottom: 0;
            width: var(--sidebar-width);
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.34);
            opacity: 0;
            pointer-events: none;
            transition: var(--transition);
            z-index: 90;
        }

        .sidebar-brand {
            min-height: var(--header-height);
            padding: 10px 24px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-bottom: 1px solid rgba(255,255,255,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 6px;
            text-align: center;
        }

        .sidebar-brand .logo {
            width: 112px;
            height: 66px;
            background: transparent;
            border-radius: 0;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800;
            font-size: 28px;
            color: #ffffff;
            overflow: hidden;
            flex-shrink: 0;
        }

        .sidebar-brand .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: transparent;
            padding: 0;
        }

        .sidebar-brand h1 {
            display: none;
        }

        .sidebar-brand small {
            display: block;
            font-size: 12px;
            color: rgba(255,255,255,0.92);
            font-weight: 600;
            line-height: 1;
            letter-spacing: 0.2px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }

        .nav-section {
            margin-bottom: 24px;
        }

        .nav-section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-muted);
            padding: 0 12px 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: var(--transition);
            margin-bottom: 2px;
        }

        .nav-link:hover {
            background: var(--bg-card);
            color: var(--text-primary);
        }

        .nav-link.active {
            background: rgba(229,140,58,0.08);
            color: var(--primary);
            font-weight: 600;
        }

        .nav-link.disabled {
            cursor: default;
            opacity: 0.58;
        }

        .nav-link.disabled:hover {
            background: transparent;
            color: var(--text-secondary);
            transform: none;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 15px;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--border);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            border-radius: 8px;
            background: var(--bg-card);
        }

        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; color: white;
            overflow: hidden;
            flex-shrink: 0;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .user-details { flex: 1; }
        .user-details .name { font-size: 13px; font-weight: 600; }
        .user-details .role { font-size: 11px; color: var(--text-muted); }

        /* ═══════════ MAIN CONTENT ═══════════ */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: var(--transition);
        }

        .topbar {
            height: var(--header-height);
            background: linear-gradient(180deg, #de8734 0%, #d37b2b 100%);
            border-bottom: 1px solid rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px 0 40px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 10px 22px rgba(204,118,38,0.12);
            overflow: hidden;
        }

        .topbar::before {
            content: '';
            position: absolute;
            inset: auto 10% -150px auto;
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 72%);
            pointer-events: none;
        }

        .topbar::after {
            content: none;
        }

        .topbar-start {
            display: flex;
            align-items: center;
            min-width: 0;
            position: relative;
            z-index: 1;
        }

        .topbar-heading {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
            padding-left: 14px;
            position: relative;
        }

        .topbar-heading::before {
            content: '';
            position: absolute;
            left: 0;
            top: 4px;
            bottom: 4px;
            width: 3px;
            border-radius: 999px;
            background: rgba(255,255,255,0.72);
        }

        .topbar-kicker {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.74);
            line-height: 1;
        }

        .sidebar-hover-zone {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 16px;
            pointer-events: none;
            z-index: 108;
        }

        .sidebar-handle {
            position: fixed;
            top: 16px;
            left: calc(var(--sidebar-width) - 6px);
            width: 12px;
            height: calc(var(--header-height) - 32px);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 0 12px 12px 0;
            background: rgba(255,255,255,0.22);
            backdrop-filter: blur(8px);
            color: #ffffff;
            cursor: pointer;
            transition: var(--transition);
            z-index: 115;
            box-shadow: 0 10px 22px rgba(120,57,8,0.18);
        }

        .sidebar-handle:hover {
            width: 14px;
            background: rgba(255,255,255,0.3);
        }

        .sidebar-handle::before {
            content: '';
            width: 3px;
            height: 26px;
            border-radius: 999px;
            background: rgba(255,255,255,0.85);
            box-shadow: -5px 0 0 rgba(255,255,255,0.5), 5px 0 0 rgba(255,255,255,0.5);
        }

        .topbar-title {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.05;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ffffff;
            justify-content: flex-end;
            position: relative;
            z-index: 1;
        }

        .topbar-slot {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            color: #ffffff;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.16);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);
            backdrop-filter: blur(8px);
            white-space: nowrap;
        }

        .topbar-badge i {
            color: rgba(255,255,255,0.9);
        }

        .topbar-profile {
            min-width: 208px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0;
            border-radius: 0;
            background: transparent;
            border: none;
            box-shadow: none;
        }

        .topbar-profile-avatar {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            background: #fff7ed;
            border: 1px solid rgba(213,126,45,0.16);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            font-size: 13px;
            font-weight: 700;
            color: #c97323;
        }

        .topbar-profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .topbar-profile-meta {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 3px;
            flex: 1;
        }

        .topbar-profile-name {
            font-size: 14px;
            font-weight: 700;
            color: #243041;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.1;
        }

        .topbar-profile-role {
            font-size: 11px;
            font-weight: 500;
            color: #718096;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1;
        }

        .topbar-profile-dot {
            display: none;
        }

        .admin-panel-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.46);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
            z-index: 180;
        }

        .admin-panel-modal {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
            z-index: 190;
        }

        .admin-panel-modal.is-open,
        .admin-panel-modal-overlay.is-open {
            opacity: 1;
            pointer-events: auto;
        }

        .admin-panel-modal-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border: 1px solid rgba(30, 41, 59, 0.08);
            border-radius: 24px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
            overflow: hidden;
            transform: translateY(12px) scale(0.98);
            transition: transform 0.2s ease;
        }

        .admin-panel-modal.is-open .admin-panel-modal-card {
            transform: translateY(0) scale(1);
        }

        .admin-panel-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 22px;
            background: linear-gradient(180deg, #fffaf5 0%, #ffffff 100%);
            border-bottom: 1px solid rgba(30, 41, 59, 0.08);
        }

        .admin-panel-modal-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.1;
        }

        .admin-panel-modal-subtitle {
            margin-top: 4px;
            font-size: 13px;
            color: #64748b;
            line-height: 1.4;
        }

        .admin-panel-modal-close {
            width: 36px;
            height: 36px;
            border: 1px solid rgba(30, 41, 59, 0.08);
            border-radius: 10px;
            background: #ffffff;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .admin-panel-modal-close:hover {
            color: #1e293b;
            border-color: rgba(229,140,58,0.24);
            background: #fffaf5;
        }

        .admin-panel-modal-body {
            padding: 26px 22px 22px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(229,140,58,0.4);
        }

        .btn-ghost {
            background: #ffffff;
            color: var(--text-secondary);
            border: 1px solid var(--border);
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }

        .btn-ghost:hover {
            background: var(--bg-card);
            color: var(--text-primary);
        }

        .topbar .btn-ghost {
            background: rgba(255,255,255,0.14);
            color: #ffffff;
            border-color: rgba(255,255,255,0.22);
            box-shadow: none;
        }

        .topbar .btn-ghost:hover {
            background: rgba(255,255,255,0.22);
            color: #ffffff;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #dc2626);
            color: white;
        }

        .page-content {
            padding: 32px;
        }

        body.sidebar-collapsed .sidebar {
            transform: translateX(calc(-1 * var(--sidebar-width)));
        }

        body.sidebar-collapsed .main-content {
            margin-left: 0;
        }

        body.sidebar-collapsed .sidebar-handle {
            left: 0;
        }

        body.sidebar-collapsed:not(.sidebar-peek) .sidebar-hover-zone {
            pointer-events: auto;
        }

        body.sidebar-collapsed.sidebar-peek .sidebar {
            transform: translateX(0);
            box-shadow: 0 28px 56px rgba(15, 23, 42, 0.16);
        }

        body.sidebar-collapsed.sidebar-peek .main-content {
            margin-left: var(--sidebar-width);
        }

        body.sidebar-collapsed.sidebar-peek .sidebar-handle {
            left: calc(var(--sidebar-width) - 6px);
        }

        /* ═══════════ CARDS ═══════════ */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            border-color: rgba(229,140,58,0.3);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
        }

        .stat-icon.primary { background: rgba(229,140,58,0.15); color: var(--primary-light); }
        .stat-icon.success { background: rgba(34,197,94,0.15); color: var(--success); }
        .stat-icon.warning { background: rgba(245,158,11,0.15); color: var(--warning); }
        .stat-icon.danger  { background: rgba(239,68,68,0.15); color: var(--danger); }

        .stat-value { font-size: 28px; font-weight: 800; }
        .stat-label { font-size: 13px; color: var(--text-muted); }

        /* ═══════════ ALERTS ═══════════ */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: var(--success); }
        .alert-danger  { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: var(--danger); }

        /* ═══════════ RESPONSIVE ═══════════ */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: 0 24px 48px rgba(15, 23, 42, 0.18);
            }

            .main-content {
                margin-left: 0;
            }

            body.sidebar-open .sidebar {
                transform: translateX(0);
            }

            body.sidebar-open .sidebar-overlay {
                opacity: 1;
                pointer-events: auto;
            }

            body.sidebar-collapsed .sidebar {
                transform: translateX(-100%);
            }

            .sidebar-hover-zone {
                display: none;
            }

            .sidebar-handle {
                top: 16px;
                left: 0;
                height: calc(var(--header-height) - 32px);
            }

            body.sidebar-open .sidebar-handle {
                left: calc(var(--sidebar-width) - 6px);
            }

            .topbar {
                padding: 0 16px 0 22px;
            }

            .topbar-kicker {
                display: none;
            }

            .topbar-title {
                font-size: 18px;
            }

            .topbar-badge {
                display: none;
            }

            .topbar-profile {
                min-width: auto;
                padding: 0;
                background: transparent;
            }

            .topbar-profile-meta,
            .topbar-profile-dot {
                display: none;
            }

            .admin-panel-modal {
                padding: 16px;
            }

            .admin-panel-modal-card {
                max-width: 100%;
                border-radius: 18px;
            }

            .admin-panel-modal-header,
            .admin-panel-modal-body {
                padding-left: 18px;
                padding-right: 18px;
            }

            .page-content {
                padding: 20px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $companyLogoPath = collect([
            'images/logo-marketkm2.webp',
            'images/logomarket.webp',
            'images/company-logo.svg',
            'images/company-logo.png',
            'images/company-logo.webp',
            'images/company-logo.jpg',
            'images/company-logo.jpeg',
        ])->first(fn ($path) => file_exists(public_path($path)));

        $currentUser = auth()->user();
        $userName = $currentUser->nombres ?? 'Usuario';
        $userRole = $currentUser->role->nombre_rol ?? 'Sin rol';
        $userInitial = strtoupper(substr($userName ?: 'U', 0, 1));
        $rawUserPhoto = trim((string) ($currentUser->foto_url ?? ''));
        $userPhotoUrl = null;

        if ($rawUserPhoto !== '') {
            if (str_starts_with($rawUserPhoto, 'http://') || str_starts_with($rawUserPhoto, 'https://') || str_starts_with($rawUserPhoto, '//')) {
                $userPhotoUrl = $rawUserPhoto;
            } else {
                $normalizedUserPhoto = ltrim($rawUserPhoto, '/');

                foreach (array_values(array_unique([
                    $normalizedUserPhoto,
                    'images/' . $normalizedUserPhoto,
                    'storage/' . $normalizedUserPhoto,
                ])) as $candidatePath) {
                    if (file_exists(public_path($candidatePath))) {
                        $userPhotoUrl = asset($candidatePath);
                        break;
                    }
                }
            }
        }

        if (!$userPhotoUrl && file_exists(public_path('images/default-user-avatar.svg'))) {
            $userPhotoUrl = asset('images/default-user-avatar.svg');
        }

        $topbarTitle = html_entity_decode(trim($__env->yieldContent('page-title', 'Dashboard')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $topbarKicker = html_entity_decode(trim($__env->yieldContent('page-kicker', 'Panel administrativo')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    @endphp

    <div class="sidebar-overlay" data-sidebar-close></div>
    <div class="sidebar-hover-zone" id="sidebar-hover-zone" aria-hidden="true"></div>
    <button type="button" class="sidebar-handle" id="sidebar-handle" aria-label="Mostrar u ocultar menú lateral" aria-controls="admin-sidebar" aria-expanded="true"></button>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="admin-sidebar">
        <div class="sidebar-brand">
            <div class="logo">
                @if($companyLogoPath)
                    <img src="{{ asset($companyLogoPath) }}" alt="Logo de la empresa">
                @else
                    K2
                @endif
            </div>
            <small>Sistema de Market</small>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Principal</div>
                <a href="{{ route('admin.dashboard.main') }}" class="nav-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.dashboard.main') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Gestión</div>
                @if(\Illuminate\Support\Facades\Route::has('admin.productos.index'))
                    <a href="{{ route('admin.productos.index') }}" class="nav-link {{ request()->routeIs('admin.productos.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes-stacked"></i> Productos
                    </a>
                @endif
                @if(\Illuminate\Support\Facades\Route::has('inventory.products'))
                    <a href="{{ route('inventory.products') }}" class="nav-link {{ request()->routeIs('inventory.products') ? 'active' : '' }}">
                        <i class="fas fa-layer-group"></i> Catalogo tecnico
                    </a>
                @endif
                @if(\Illuminate\Support\Facades\Route::has('admin.categorias.index'))
                    <a href="{{ route('admin.categorias.index') }}" class="nav-link {{ request()->routeIs('admin.categorias.*') || request()->routeIs('inventory.categories') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i> Categorías
                    </a>
                @endif
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Tienda Virtual</div>
                @if(\Illuminate\Support\Facades\Route::has('storefront.index'))
                    <a href="{{ route('storefront.index') }}" class="nav-link" target="_blank" rel="noopener">
                        <i class="fas fa-store"></i> Ver tienda publica
                    </a>
                @endif
                @if(\Illuminate\Support\Facades\Route::has('admin.zonas.index'))
                    <a href="{{ route('admin.zonas.index') }}" class="nav-link {{ request()->routeIs('admin.zonas.*') ? 'active' : '' }}">
                        <i class="fas fa-map-location-dot"></i> Delivery
                    </a>
                @endif
                @if(\Illuminate\Support\Facades\Route::has('admin.pedidos.index'))
                    <a href="{{ route('admin.pedidos.index') }}" class="nav-link {{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}">
                        <i class="fab fa-whatsapp"></i> Pedidos WhatsApp
                    </a>
                @endif
                @if(\Illuminate\Support\Facades\Route::has('admin.banners.index'))
                    <a href="{{ route('admin.banners.index') }}" class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                        <i class="fas fa-image"></i> Banners
                    </a>
                @else
                <span class="nav-link disabled" title="Modulo pendiente">
                    <i class="fas fa-image"></i> Banners
                </span>
                @endif
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Sistema</div>
                @if(\Illuminate\Support\Facades\Route::has('admin.reportes.index'))
                    <a href="{{ route('admin.reportes.index') }}" class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i> Reportes
                    </a>
                @endif
                @if(\Illuminate\Support\Facades\Route::has('admin.business.index'))
                    <a href="{{ route('admin.business.index') }}" class="nav-link {{ request()->routeIs('admin.business.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i> Business & data
                    </a>
                @endif
                @if(\Illuminate\Support\Facades\Route::has('admin.configuracion.index'))
                    <a href="{{ route('admin.configuracion.index') }}" class="nav-link {{ request()->routeIs('admin.configuracion.*') || request()->routeIs('admin.storefront.*') || request()->routeIs('admin.usuarios.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.permisos.*') ? 'active' : '' }}">
                        <i class="fas fa-gear"></i> Configuración
                    </a>
                @endif
            </div>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    @if($userPhotoUrl)
                        <img src="{{ $userPhotoUrl }}" alt="Foto de perfil de {{ $userName }}">
                    @else
                        {{ $userInitial }}
                    @endif
                </div>
                <div class="user-details">
                    <div class="name">{{ $userName }}</div>
                    <div class="role">{{ $userRole }}</div>
                </div>
                <form method="POST" action="{{ route('auth.logout') }}" id="logout-form" style="display: none;">
                    @csrf
                </form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Cerrar sesión" style="color: var(--text-muted); font-size: 14px; transition: color 0.3s;" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'">
                    <i class="fas fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <header class="topbar">
            <div class="topbar-start">
                <div class="topbar-heading">
                    <span class="topbar-kicker">{{ $topbarKicker }}</span>
                    <h2 class="topbar-title">{{ $topbarTitle }}</h2>
                </div>
            </div>
            <div class="topbar-actions">
                <div class="topbar-slot">
                    @yield('topbar-actions')
                </div>
                <div class="topbar-profile">
                    <div class="topbar-profile-avatar">
                        @if($userPhotoUrl)
                            <img src="{{ $userPhotoUrl }}" alt="Foto de perfil de {{ $userName }}">
                        @else
                            {{ $userInitial }}
                        @endif
                    </div>
                    <div class="topbar-profile-meta">
                        <span class="topbar-profile-name">{{ $userName }}</span>
                        <span class="topbar-profile-role">{{ $userRole }}</span>
                    </div>
                    <span class="topbar-profile-dot" aria-hidden="true"></span>
                </div>
            </div>
        </header>

        <main class="page-content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @livewireScripts
    <script>
        (() => {
            const body = document.body;
            const toggleButton = document.getElementById('sidebar-handle');
            const hoverZone = document.getElementById('sidebar-hover-zone');
            const sidebar = document.getElementById('admin-sidebar');
            const closeTargets = document.querySelectorAll('[data-sidebar-close]');
            const desktopQuery = window.matchMedia('(min-width: 769px)');
            const storageKey = 'km2-sidebar-collapsed';
            let peekTimeout;

            if (!toggleButton) {
                return;
            }

            const syncButtonState = () => {
                const expanded = desktopQuery.matches
                    ? !body.classList.contains('sidebar-collapsed') || body.classList.contains('sidebar-peek')
                    : body.classList.contains('sidebar-open');

                toggleButton.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            };

            const applyDesktopState = () => {
                const storedState = localStorage.getItem(storageKey);

                body.classList.toggle('sidebar-collapsed', storedState === 'true');
                body.classList.remove('sidebar-open');
                body.classList.remove('sidebar-peek');
                syncButtonState();
            };

            const applyMobileState = () => {
                body.classList.remove('sidebar-collapsed');
                body.classList.remove('sidebar-open');
                body.classList.remove('sidebar-peek');
                syncButtonState();
            };

            const clearPeekTimeout = () => {
                if (peekTimeout) {
                    clearTimeout(peekTimeout);
                    peekTimeout = null;
                }
            };

            const openPeekSidebar = () => {
                if (!desktopQuery.matches || !body.classList.contains('sidebar-collapsed')) {
                    return;
                }

                clearPeekTimeout();
                body.classList.add('sidebar-peek');
                syncButtonState();
            };

            const closePeekSidebar = () => {
                if (!desktopQuery.matches || !body.classList.contains('sidebar-collapsed')) {
                    return;
                }

                clearPeekTimeout();
                peekTimeout = window.setTimeout(() => {
                    body.classList.remove('sidebar-peek');
                    syncButtonState();
                }, 140);
            };

            if (desktopQuery.matches) {
                applyDesktopState();
            } else {
                applyMobileState();
            }

            toggleButton.addEventListener('click', () => {
                if (desktopQuery.matches) {
                    const collapsed = body.classList.toggle('sidebar-collapsed');
                    body.classList.remove('sidebar-peek');
                    localStorage.setItem(storageKey, collapsed ? 'true' : 'false');
                } else {
                    body.classList.toggle('sidebar-open');
                }

                syncButtonState();
            });

            if (hoverZone) {
                hoverZone.addEventListener('mouseenter', openPeekSidebar);
            }

            toggleButton.addEventListener('mouseenter', openPeekSidebar);

            [sidebar, toggleButton].forEach((element) => {
                if (!element) {
                    return;
                }

                element.addEventListener('mouseenter', () => {
                    if (desktopQuery.matches) {
                        clearPeekTimeout();
                    }
                });

                element.addEventListener('mouseleave', closePeekSidebar);
            });

            closeTargets.forEach((element) => {
                element.addEventListener('click', () => {
                    if (!desktopQuery.matches) {
                        body.classList.remove('sidebar-open');
                        syncButtonState();
                    }
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    body.classList.remove('sidebar-open');
                    syncButtonState();
                }
            });

            const handleViewportChange = (event) => {
                clearPeekTimeout();

                if (event.matches) {
                    applyDesktopState();
                } else {
                    applyMobileState();
                }
            };

            if (typeof desktopQuery.addEventListener === 'function') {
                desktopQuery.addEventListener('change', handleViewportChange);
            } else if (typeof desktopQuery.addListener === 'function') {
                desktopQuery.addListener(handleViewportChange);
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>
