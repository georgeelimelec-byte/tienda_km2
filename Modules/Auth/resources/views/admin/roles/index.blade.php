@extends('layouts.admin')

@section('title', 'Roles')
@section('page-title', 'Roles y perfiles')
@section('page-kicker', 'Catalogo base de acceso')

@section('topbar-actions')
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary" style="text-decoration:none;">
        <i class="fas fa-plus"></i> Nuevo rol
    </a>
@endsection

@push('styles')
<style>
    .layout-stack { display: flex; flex-direction: column; gap: 24px; }
    .panel-card { background: #ffffff; border: 1px solid var(--border); border-radius: 14px; box-shadow: var(--shadow); }
    .panel-head { padding: 22px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
    .panel-head h3 { margin: 0; color: #111827; font-size: 20px; font-weight: 900; }
    .panel-head p { margin: 4px 0 0; color: #64748b; font-size: 14px; }
    .panel-body { padding: 24px; }
    .role-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .role-card { border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff; padding: 18px; }
    .role-top { display: flex; justify-content: space-between; align-items: center; gap: 10px; }
    .role-title { margin: 0; color: #111827; font-size: 18px; font-weight: 900; }
    .role-level { color: #64748b; font-size: 13px; margin-top: 6px; }
    .badge { display: inline-flex; align-items: center; min-height: 26px; padding: 0 10px; border-radius: 999px; font-size: 12px; font-weight: 800; }
    .badge.ok { background: #ecfdf5; color: #047857; }
    .badge.off { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
    .muted { color: #64748b; font-size: 14px; }
    .actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 16px; }
    @media (max-width: 1040px) { .role-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="layout-stack">
    <section class="panel-card">
        <div class="panel-head">
            <div>
                <h3>Catalogo de roles</h3>
                <p>La asignacion por usuario se hace desde la pantalla de usuarios. Aqui se mantiene el perfil base y su nivel.</p>
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
                        <p class="muted" style="margin: 14px 0 0;">Usa este rol para agrupar permisos de lectura, creacion, edicion y eliminacion.</p>
                        <div class="actions">
                            <a href="{{ route('admin.roles.edit', $role->id_rol) }}" class="btn btn-ghost" style="text-decoration:none;">
                                <i class="fas fa-pen"></i> Editar
                            </a>
                            <a href="{{ route('admin.permisos.index') }}" class="btn btn-ghost" style="text-decoration:none;">
                                <i class="fas fa-key"></i> Permisos
                            </a>
                            @if($role->id_rol != 1)
                                <form method="POST" action="{{ route('admin.roles.delete', $role->id_rol) }}" onsubmit="return confirm('Eliminar este rol?');">
                                    @csrf
                                    <button type="submit" class="btn btn-ghost" style="color:#dc2626;border-color:#fecaca;">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection
