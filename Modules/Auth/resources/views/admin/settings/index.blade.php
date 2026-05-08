@extends('layouts.admin')

@section('title', 'Configuracion')
@section('page-title', 'Configuracion')
@section('page-kicker', 'Centro de ajustes del sistema')

@section('topbar-actions')
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ route('admin.storefront.index') }}" class="topbar-badge" style="text-decoration:none;">
            <i class="fas fa-palette"></i> Apariencia
        </a>
        <a href="{{ route('admin.usuarios.index') }}" class="topbar-badge" style="text-decoration:none;">
            <i class="fas fa-users"></i> Usuarios y roles
        </a>
        <a href="{{ route('admin.permisos.index') }}" class="topbar-badge" style="text-decoration:none;">
            <i class="fas fa-key"></i> Permisos
        </a>
    </div>
@endsection

@push('styles')
<style>
    .settings-page {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .settings-hero,
    .settings-card,
    .settings-form-card,
    .settings-preview {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow);
    }

    .settings-hero {
        padding: 26px;
        background:
            linear-gradient(135deg, rgba(249,115,22,0.10), rgba(15,23,42,0.03)),
            #ffffff;
        border-color: rgba(249,115,22,0.18);
    }

    .settings-hero-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
    }

    .settings-hero h3,
    .settings-card h3,
    .settings-form-head h3,
    .settings-preview-head h3 {
        margin: 0;
        color: #111827;
        font-size: 22px;
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .settings-hero p,
    .settings-card p,
    .settings-form-head p,
    .settings-preview-head p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }

    .settings-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) 360px;
        gap: 16px;
        align-items: start;
    }

    .settings-main {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .settings-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .settings-metric {
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .settings-metric-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fff7ed;
        color: #ea580c;
        font-size: 18px;
        flex-shrink: 0;
    }

    .settings-metric-label {
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .settings-metric-value {
        margin-top: 5px;
        color: #111827;
        font-size: 26px;
        font-weight: 900;
        letter-spacing: -0.04em;
    }

    .settings-shortcuts {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .settings-card {
        padding: 20px;
    }

    .settings-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .settings-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fff7ed;
        color: #ea580c;
        font-size: 17px;
        flex-shrink: 0;
    }

    .settings-chip {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 0 10px;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .settings-card h4,
    .settings-preview h4 {
        margin: 0;
        color: #111827;
        font-size: 17px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .settings-inline-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    .settings-inline-stat {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 10px;
        border-radius: 999px;
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        border: 1px solid #e2e8f0;
    }

    .settings-card-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .settings-btn,
    .settings-btn-ghost {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    .settings-btn {
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #ffffff;
        box-shadow: 0 14px 28px -20px rgba(234, 88, 12, 0.85);
    }

    .settings-btn-ghost {
        background: #ffffff;
        color: #334155;
        border: 1px solid #dbe2ea;
    }

    .settings-form-card,
    .settings-preview {
        overflow: hidden;
    }

    .settings-form-head,
    .settings-preview-head {
        padding: 22px 24px;
        border-bottom: 1px solid var(--border);
    }

    .settings-form-body,
    .settings-preview-body {
        padding: 24px;
    }

    .settings-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .settings-form-group.full {
        grid-column: 1 / -1;
    }

    .settings-label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .settings-field {
        width: 100%;
        min-height: 46px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #f8fafc;
        padding: 10px 13px;
        font: inherit;
        color: #111827;
        transition: var(--transition);
    }

    .settings-field:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(229, 140, 58, 0.12);
        background: #ffffff;
    }

    .settings-error {
        margin-top: 6px;
        color: #dc2626;
        font-size: 12px;
        font-weight: 700;
    }

    .settings-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
    }

    .settings-subsection {
        grid-column: 1 / -1;
        margin-top: 6px;
        padding: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #f8fafc;
    }

    .settings-subsection-head {
        margin-bottom: 16px;
    }

    .settings-subsection-head h4 {
        margin: 0;
        color: #111827;
        font-size: 16px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .settings-subsection-head p {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.55;
    }

    .settings-check-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 12px;
    }

    .settings-checkline {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        min-height: 44px;
        padding: 0 14px;
        border: 1px solid #dbe2ea;
        border-radius: 12px;
        background: #ffffff;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
    }

    .storefront-preview-header {
        padding: 18px;
        border-radius: 14px;
        color: #ffffff;
    }

    .storefront-preview-brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .storefront-preview-logo {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        overflow: hidden;
        background: rgba(255,255,255,0.18);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 20px;
        font-weight: 900;
        flex-shrink: 0;
    }

    .storefront-preview-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .storefront-preview-name {
        font-size: 20px;
        font-weight: 900;
        line-height: 1.05;
        letter-spacing: -0.03em;
    }

    .storefront-preview-tagline {
        margin-top: 3px;
        font-size: 13px;
        opacity: 0.88;
    }

    .palette-row {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-top: 16px;
    }

    .palette-swatch {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        background: #ffffff;
    }

    .palette-sample {
        height: 48px;
    }

    .palette-meta {
        padding: 8px 10px 10px;
    }

    .palette-label {
        color: #64748b;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .palette-code {
        margin-top: 3px;
        color: #111827;
        font-size: 12px;
        font-weight: 800;
    }

    .settings-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 16px;
    }

    .settings-list-item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
    }

    .settings-list-item strong {
        display: block;
        color: #111827;
        font-size: 13px;
    }

    .settings-list-item span {
        display: block;
        color: #64748b;
        font-size: 12px;
        margin-top: 4px;
        line-height: 1.5;
    }

    @media (max-width: 1180px) {
        .settings-layout,
        .settings-grid,
        .settings-shortcuts,
        .settings-form-grid,
        .settings-check-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $logoUrl = $storefront->displayLogoUrl();
    $storeInitial = strtoupper(mb_substr(trim($storefront->store_name ?: 'Tienda'), 0, 1));
@endphp
<div class="settings-page">
    <section class="settings-hero">
        <div class="settings-hero-top">
            <div>
                <h3>Configuracion central del sistema</h3>
                <p>
                    Desde aqui se ordenan los ajustes del negocio. La estructura queda separada por capas: apariencia publica, operacion de empresa, pedidos WhatsApp y accesos internos.
                </p>
            </div>
            <div class="settings-card-actions">
                <a href="#empresa-config" class="settings-btn-ghost">
                    <i class="fas fa-building"></i> Empresa
                </a>
                <a href="{{ route('admin.storefront.index') }}" class="settings-btn">
                    <i class="fas fa-palette"></i> Editar apariencia
                </a>
            </div>
        </div>
    </section>

    <section class="settings-grid">
        <article class="settings-card settings-metric">
            <span class="settings-metric-icon"><i class="fas fa-users"></i></span>
            <div>
                <div class="settings-metric-label">Usuarios activos</div>
                <div class="settings-metric-value">{{ number_format($settingsSummary['users_active']) }}</div>
            </div>
        </article>
        <article class="settings-card settings-metric">
            <span class="settings-metric-icon"><i class="fas fa-user-shield"></i></span>
            <div>
                <div class="settings-metric-label">Roles definidos</div>
                <div class="settings-metric-value">{{ number_format($settingsSummary['roles_total']) }}</div>
            </div>
        </article>
        <article class="settings-card settings-metric">
            <span class="settings-metric-icon"><i class="fas fa-key"></i></span>
            <div>
                <div class="settings-metric-label">Permisos por rol</div>
                <div class="settings-metric-value">{{ number_format($settingsSummary['role_permissions']) }}</div>
            </div>
        </article>
        <article class="settings-card settings-metric">
            <span class="settings-metric-icon"><i class="fas fa-sliders"></i></span>
            <div>
                <div class="settings-metric-label">Excepciones por usuario</div>
                <div class="settings-metric-value">{{ number_format($settingsSummary['user_overrides']) }}</div>
            </div>
        </article>
    </section>

    <div class="settings-layout">
        <div class="settings-main">
            <section class="settings-shortcuts">
                <article class="settings-card">
                    <div class="settings-card-head">
                        <span class="settings-icon-box"><i class="fas fa-palette"></i></span>
                        <span class="settings-chip">Tienda</span>
                    </div>
                    <h4>Apariencia y Storefront</h4>
                    <p>Nombre visible, logo, colores, header, tarjetas y footer de la tienda virtual.</p>
                    <div class="settings-inline-stats">
                        <span class="settings-inline-stat">{{ $storefront->store_name }}</span>
                        <span class="settings-inline-stat">{{ $storefront->header_style === 'dark' ? 'Header oscuro' : 'Header solido' }}</span>
                        <span class="settings-inline-stat">{{ $storefront->card_style }}</span>
                    </div>
                    <div class="settings-card-actions">
                        <a href="{{ route('admin.storefront.index') }}" class="settings-btn">
                            <i class="fas fa-pen-ruler"></i> Editar apariencia
                        </a>
                    </div>
                </article>

                <article class="settings-card">
                    <div class="settings-card-head">
                        <span class="settings-icon-box"><i class="fas fa-users-cog"></i></span>
                        <span class="settings-chip">Accesos</span>
                    </div>
                    <h4>Usuarios, roles y permisos</h4>
                    <p>Alta de usuarios, asignacion de roles base y control granular por modulo o por persona.</p>
                    <div class="settings-inline-stats">
                        <span class="settings-inline-stat">{{ number_format($settingsSummary['users_total']) }} usuarios</span>
                        <span class="settings-inline-stat">{{ number_format($settingsSummary['roles_total']) }} roles</span>
                    </div>
                    <div class="settings-card-actions">
                        <a href="{{ route('admin.usuarios.index') }}" class="settings-btn-ghost">
                            <i class="fas fa-users"></i> Usuarios y roles
                        </a>
                        <a href="{{ route('admin.permisos.index') }}" class="settings-btn">
                            <i class="fas fa-key"></i> Permisos
                        </a>
                    </div>
                </article>

            </section>

            <section class="settings-form-card" id="empresa-config">
                <div class="settings-form-head">
                    <h3>Empresa y operacion</h3>
                    <p>Datos fiscales, IGV, moneda, contacto y mensajes internos para la tienda virtual y la operacion por WhatsApp.</p>
                </div>
                <div class="settings-form-body">
                    <form method="POST" action="{{ route('admin.configuracion.update') }}">
                        @csrf
                        <div class="settings-form-grid">
                            <div class="settings-form-group">
                                <label class="settings-label" for="ruc">RUC</label>
                                <input class="settings-field" id="ruc" name="ruc" value="{{ old('ruc', $company->ruc) }}" maxlength="11" required>
                                @error('ruc') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="settings-form-group">
                                <label class="settings-label" for="nombre_comercial">Nombre comercial</label>
                                <input class="settings-field" id="nombre_comercial" name="nombre_comercial" value="{{ old('nombre_comercial', $company->nombre_comercial) }}" maxlength="150" required>
                                @error('nombre_comercial') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="settings-form-group full">
                                <label class="settings-label" for="razon_social">Razon social</label>
                                <input class="settings-field" id="razon_social" name="razon_social" value="{{ old('razon_social', $company->razon_social) }}" maxlength="150" required>
                                @error('razon_social') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="settings-form-group full">
                                <label class="settings-label" for="direccion_fiscal">Direccion fiscal</label>
                                <textarea class="settings-field" id="direccion_fiscal" name="direccion_fiscal" rows="3" required>{{ old('direccion_fiscal', $company->direccion_fiscal) }}</textarea>
                                @error('direccion_fiscal') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="settings-form-group">
                                <label class="settings-label" for="telefono_contacto">Telefono</label>
                                <input class="settings-field" id="telefono_contacto" name="telefono_contacto" value="{{ old('telefono_contacto', $company->telefono_contacto) }}" maxlength="20">
                                @error('telefono_contacto') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="settings-form-group">
                                <label class="settings-label" for="correo_contacto">Correo</label>
                                <input class="settings-field" id="correo_contacto" name="correo_contacto" type="email" value="{{ old('correo_contacto', $company->correo_contacto) }}" maxlength="100">
                                @error('correo_contacto') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="settings-form-group">
                                <label class="settings-label" for="ubigeo">Ubigeo</label>
                                <input class="settings-field" id="ubigeo" name="ubigeo" value="{{ old('ubigeo', $company->ubigeo) }}" maxlength="6">
                                @error('ubigeo') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="settings-form-group">
                                <label class="settings-label" for="porcentaje_igv">IGV (%)</label>
                                <input class="settings-field" id="porcentaje_igv" name="porcentaje_igv" type="number" step="0.01" min="0" max="99.99" value="{{ old('porcentaje_igv', $company->porcentaje_igv) }}" required>
                                @error('porcentaje_igv') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="settings-form-group">
                                <label class="settings-label" for="moneda">Moneda</label>
                                <input class="settings-field" id="moneda" name="moneda" value="{{ old('moneda', $company->moneda ?? 'PEN') }}" maxlength="10" required>
                                @error('moneda') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="settings-form-group">
                                <label class="settings-label" for="estado">Estado</label>
                                <select class="settings-field" id="estado" name="estado">
                                    <option value="Activo" @selected(old('estado', $company->estado ?: 'Activo') === 'Activo')>Activo</option>
                                    <option value="Inactivo" @selected(old('estado', $company->estado) === 'Inactivo')>Inactivo</option>
                                </select>
                                @error('estado') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="settings-form-group full">
                                <label class="settings-label" for="horario_atencion">Horario de atencion</label>
                                <input class="settings-field" id="horario_atencion" name="horario_atencion" value="{{ old('horario_atencion', $company->horario_atencion) }}" maxlength="160" placeholder="Lunes a domingo | 7:00 a.m. - 10:00 p.m.">
                                @error('horario_atencion') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="settings-form-group full">
                                <label class="settings-label" for="mensaje_operativo">Mensaje operativo</label>
                                <textarea class="settings-field" id="mensaje_operativo" name="mensaje_operativo" rows="4" placeholder="Mensaje visible para el equipo o para avisos internos.">{{ old('mensaje_operativo', $company->mensaje_operativo) }}</textarea>
                                @error('mensaje_operativo') <div class="settings-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="settings-form-actions">
                            <button type="submit" class="settings-btn">
                                <i class="fas fa-save"></i> Guardar configuracion general
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        <aside class="settings-preview">
            <div class="settings-preview-head">
                <h3>Resumen de configuracion</h3>
                <p>Vista rapida para no perder de vista la identidad de tienda y la estructura de accesos.</p>
            </div>
            <div class="settings-preview-body">
                <div class="storefront-preview-header" style="background: linear-gradient(135deg, {{ $storefront->primary_color }}, {{ $storefront->primary_dark_color }});">
                    <div class="storefront-preview-brand">
                        <div class="storefront-preview-logo">
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" alt="Logo actual">
                            @else
                                {{ $storeInitial }}
                            @endif
                        </div>
                        <div>
                            <div class="storefront-preview-name">{{ $storefront->store_name }}</div>
                            <div class="storefront-preview-tagline">{{ $storefront->store_tagline ?: 'Sin subtitulo configurado' }}</div>
                        </div>
                    </div>
                </div>

                <div class="palette-row">
                    <div class="palette-swatch">
                        <div class="palette-sample" style="background: {{ $storefront->primary_color }};"></div>
                        <div class="palette-meta">
                            <div class="palette-label">Primario</div>
                            <div class="palette-code">{{ $storefront->primary_color }}</div>
                        </div>
                    </div>
                    <div class="palette-swatch">
                        <div class="palette-sample" style="background: {{ $storefront->primary_light_color }};"></div>
                        <div class="palette-meta">
                            <div class="palette-label">Claro</div>
                            <div class="palette-code">{{ $storefront->primary_light_color }}</div>
                        </div>
                    </div>
                    <div class="palette-swatch">
                        <div class="palette-sample" style="background: {{ $storefront->primary_dark_color }};"></div>
                        <div class="palette-meta">
                            <div class="palette-label">Oscuro</div>
                            <div class="palette-code">{{ $storefront->primary_dark_color }}</div>
                        </div>
                    </div>
                    <div class="palette-swatch">
                        <div class="palette-sample" style="background: {{ $storefront->accent_color }};"></div>
                        <div class="palette-meta">
                            <div class="palette-label">Contraste</div>
                            <div class="palette-code">{{ $storefront->accent_color }}</div>
                        </div>
                    </div>
                </div>

                <div class="settings-list">
                    <div class="settings-list-item">
                        <div>
                            <strong>Apariencia de tienda</strong>
                            <span>Header: {{ $storefront->header_style }} | Cards: {{ $storefront->card_style }} | Login visible: {{ $storefront->show_login_link ? 'Si' : 'No' }}</span>
                        </div>
                        <a href="{{ route('admin.storefront.index') }}" class="settings-btn-ghost">Abrir</a>
                    </div>
                    <div class="settings-list-item">
                        <div>
                            <strong>Accesos internos</strong>
                            <span>{{ number_format($settingsSummary['users_total']) }} usuarios registrados y {{ number_format($settingsSummary['roles_total']) }} roles definidos.</span>
                        </div>
                        <a href="{{ route('admin.usuarios.index') }}" class="settings-btn-ghost">Abrir</a>
                    </div>
                    <div class="settings-list-item">
                        <div>
                            <strong>Gobierno de permisos</strong>
                            <span>{{ number_format($settingsSummary['role_permissions']) }} permisos por rol y {{ number_format($settingsSummary['user_overrides']) }} excepciones por usuario.</span>
                        </div>
                        <a href="{{ route('admin.permisos.index') }}" class="settings-btn-ghost">Abrir</a>
                    </div>
                    <div class="settings-list-item">
                        <div>
                            <strong>Roles activos</strong>
                            <span>
                                @foreach($roles->take(3) as $role)
                                    {{ $role->nombre_rol }} ({{ $role->usuarios_count }})@if(!$loop->last), @endif
                                @endforeach
                                @if($roles->count() > 3)
                                    y {{ $roles->count() - 3 }} mas.
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
