@extends('layouts.admin')

@section('title', 'Usuarios y accesos')
@section('page-title', 'Usuarios y accesos')
@section('page-kicker', 'Staff, roles y control interno')

@section('topbar-actions')
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ route('admin.configuracion.index') }}" class="btn btn-ghost" style="text-decoration:none;">
            <i class="fas fa-gear"></i> Configuracion
        </a>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-ghost" style="text-decoration:none;">
            <i class="fas fa-user-shield"></i> Nuevo rol
        </a>
        <a href="{{ route('admin.permisos.index') }}" class="btn btn-ghost" style="text-decoration:none;">
            <i class="fas fa-key"></i> Permisos
        </a>
        <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary" style="text-decoration:none;">
            <i class="fas fa-plus"></i> Nuevo usuario
        </a>
    </div>
@endsection

@push('styles')
<style>
    .admin-stack { display: flex; flex-direction: column; gap: 24px; }
    .summary-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
    .summary-card, .panel-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--shadow);
    }
    .summary-card { padding: 18px; }
    .summary-label { color: #64748b; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; }
    .summary-value { margin-top: 6px; color: #111827; font-size: 28px; font-weight: 900; letter-spacing: -.04em; }
    .panel-head { padding: 22px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
    .panel-head h3 { margin: 0; font-size: 20px; font-weight: 900; color: #111827; }
    .panel-head p { margin: 4px 0 0; color: #64748b; font-size: 14px; }
    .panel-body { padding: 24px; }
    .filters-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr auto; gap: 12px; }
    .field { width: 100%; min-height: 44px; border: 1px solid var(--border); border-radius: 10px; background: #f8fafc; padding: 10px 13px; font-size: 14px; color: #111827; font-family: inherit; }
    .field:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(229, 140, 58, 0.12); background: #ffffff; }
    .table-wrap { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; }
    .table th { text-align: left; font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: .04em; padding: 0 0 12px; border-bottom: 1px solid #e5e7eb; }
    .table td { padding: 16px 0; border-bottom: 1px solid #eef2f7; color: #334155; font-size: 14px; vertical-align: middle; }
    .table tr:last-child td { border-bottom: none; }
    .badge { display: inline-flex; align-items: center; min-height: 26px; padding: 0 10px; border-radius: 999px; font-size: 12px; font-weight: 800; }
    .badge.role { background: #eff6ff; color: #1d4ed8; }
    .badge.ok { background: #ecfdf5; color: #047857; }
    .badge.off { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
    .actions { display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }
    .empty-state { text-align: center; padding: 48px 20px; color: #64748b; }
    .user-name { font-weight: 800; color: #111827; }
    .user-mail { color: #64748b; font-size: 13px; margin-top: 4px; }
    .role-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .role-card { border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff; padding: 18px; }
    .role-top { display: flex; justify-content: space-between; align-items: center; gap: 10px; }
    .role-title { margin: 0; color: #111827; font-size: 18px; font-weight: 900; }
    .role-level { color: #64748b; font-size: 13px; margin-top: 6px; }
    @media (max-width: 980px) {
        .summary-grid, .filters-grid { grid-template-columns: 1fr; }
        .role-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@php
    $totalUsers = $users->total();
    $inactiveUsers = max(0, $totalUsers - $activeUsers);
@endphp
<div class="admin-stack">
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Usuarios visibles</div>
            <div class="summary-value">{{ $totalUsers }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Usuarios activos</div>
            <div class="summary-value">{{ $activeUsers }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Usuarios inactivos</div>
            <div class="summary-value">{{ $inactiveUsers }}</div>
        </div>
    </div>

    <section class="panel-card">
        <div class="panel-head">
            <div>
                <h3>Directorio interno</h3>
                <p>Consulta, filtra y administra cuentas del personal, correo, rol y estado operativo.</p>
            </div>
        </div>
        <div class="panel-body">
            <form method="GET" action="{{ route('admin.usuarios.index') }}" class="filters-grid" style="margin-bottom: 22px;">
                <input class="field" type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o correo">
                <select class="field" name="rol">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id_rol }}" @selected((string) request('rol') === (string) $role->id_rol)>{{ $role->nombre_rol }}</option>
                    @endforeach
                </select>
                <select class="field" name="estado">
                    <option value="">Todos los estados</option>
                    <option value="Activo" @selected(request('estado') === 'Activo')>Activo</option>
                    <option value="Inactivo" @selected(request('estado') === 'Inactivo')>Inactivo</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
            </form>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Registro</th>
                            <th style="text-align:right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="user-name">{{ $user->nombres }}</div>
                                    <div class="user-mail">{{ $user->email ?: 'Sin correo asignado' }}</div>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.roles.assign') }}" style="display:flex;gap:8px;align-items:center;min-width:230px;">
                                        @csrf
                                        <input type="hidden" name="id_usuario" value="{{ $user->id_usuario }}">
                                        <select class="field" name="id_rol" style="min-height:40px;">
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id_rol }}" @selected((int) $user->id_rol === (int) $role->id_rol)>{{ $role->nombre_rol }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-ghost" title="Guardar rol">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <span class="badge {{ $user->estado === 'Activo' ? 'ok' : 'off' }}">{{ $user->estado }}</span>
                                </td>
                                <td>{{ optional($user->fecha_registro)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('admin.usuarios.edit', $user->id_usuario) }}" class="btn btn-ghost" style="text-decoration:none;">
                                            <i class="fas fa-pen"></i> Editar
                                        </a>
                                        <form method="POST" action="{{ route('admin.usuarios.delete', $user->id_usuario) }}" onsubmit="return confirm('Eliminar este usuario?');">
                                            @csrf
                                            <button type="submit" class="btn btn-ghost" style="color:#dc2626;border-color:#fecaca;">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <div style="font-size: 42px; margin-bottom: 10px;"><i class="fas fa-users"></i></div>
                                        No se encontraron usuarios con los filtros actuales.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 18px;">
                {{ $users->links() }}
            </div>
        </div>
    </section>

    <section class="panel-card">
        <div class="panel-head">
            <div>
                <h3>Catalogo de roles</h3>
                <p>La asignacion se hace desde la tabla de usuarios. Aqui se mantienen los perfiles base del sistema.</p>
            </div>
        </div>
        <div class="panel-body">
            <div class="role-grid">
                @foreach($roles as $role)
                    <article class="role-card">
                        <div class="role-top">
                            <div>
                                <h4 class="role-title">{{ $role->nombre_rol }}</h4>
                                <div class="role-level">Nivel {{ $role->nivel_acceso }} - {{ $role->usuarios_count }} usuario(s)</div>
                            </div>
                            <span class="badge {{ $role->estado === 'Activo' ? 'ok' : 'off' }}">{{ $role->estado }}</span>
                        </div>
                        <p class="user-mail" style="margin-top:14px;">Define el perfil operativo y luego ajusta sus permisos desde seguridad granular.</p>
                        <div class="actions" style="justify-content:flex-start;margin-top:16px;">
                            <a href="{{ route('admin.roles.edit', $role->id_rol) }}" class="btn btn-ghost" style="text-decoration:none;">
                                <i class="fas fa-pen"></i> Editar
                            </a>
                            <a href="{{ route('admin.permisos.index') }}" class="btn btn-ghost" style="text-decoration:none;">
                                <i class="fas fa-key"></i> Permisos
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection
