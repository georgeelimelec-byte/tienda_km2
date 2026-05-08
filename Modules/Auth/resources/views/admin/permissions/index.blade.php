@extends('layouts.admin')

@section('title', 'Permisos')
@section('page-title', 'Permisos granulares')
@section('page-kicker', 'Control por modulo y accion')

@section('topbar-actions')
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ route('admin.configuracion.index') }}" class="btn btn-ghost" style="text-decoration:none;">
            <i class="fas fa-gear"></i> Configuracion
        </a>
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-ghost" style="text-decoration:none;">
            <i class="fas fa-users"></i> Usuarios
        </a>
    </div>
@endsection

@push('styles')
<style>
    .page-stack { display: flex; flex-direction: column; gap: 24px; }
    .panel-card { background: #ffffff; border: 1px solid var(--border); border-radius: 14px; box-shadow: var(--shadow); }
    .panel-head { padding: 22px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
    .panel-head h3 { margin: 0; color: #111827; font-size: 20px; font-weight: 900; }
    .panel-head p { margin: 4px 0 0; color: #64748b; font-size: 14px; }
    .panel-body { padding: 24px; }
    .matrix-table { width: 100%; border-collapse: collapse; }
    .matrix-table th { text-align: left; font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: .04em; padding: 0 0 12px; border-bottom: 1px solid #e5e7eb; }
    .matrix-table td { padding: 14px 0; border-bottom: 1px solid #eef2f7; color: #334155; font-size: 14px; vertical-align: middle; }
    .matrix-table tr:last-child td { border-bottom: none; }
    .perm-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    .perm-card { border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff; overflow: hidden; }
    .perm-card-head { padding: 18px 20px; border-bottom: 1px solid #eef2f7; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
    .perm-card-head h4 { margin: 0; color: #111827; font-size: 18px; font-weight: 900; }
    .perm-card-head p { margin: 4px 0 0; color: #64748b; font-size: 13px; }
    .field { width: 100%; min-height: 42px; border: 1px solid var(--border); border-radius: 10px; background: #f8fafc; padding: 10px 12px; font-size: 14px; font-family: inherit; }
    .field:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(229, 140, 58, 0.12); background: #fff; }
    .badge { display: inline-flex; align-items: center; min-height: 26px; padding: 0 10px; border-radius: 999px; font-size: 12px; font-weight: 800; background: #eff6ff; color: #1d4ed8; }
    .empty-box { padding: 28px; border: 1px dashed #cbd5e1; border-radius: 12px; text-align: center; color: #64748b; }
    @media (max-width: 1100px) { .perm-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
@php
    $actions = ['leer' => 'Leer', 'crear' => 'Crear', 'editar' => 'Editar', 'eliminar' => 'Eliminar'];
@endphp
<div class="page-stack">
    <section class="panel-card">
        <div class="panel-head">
            <div>
                <h3>Permisos por rol</h3>
                <p>Cada rol hereda acceso base por modulo. Los usuarios con nivel 1 mantienen acceso total.</p>
            </div>
        </div>
        <div class="panel-body">
            <div class="perm-grid">
                @foreach($roles as $role)
                    @php
                        $permissions = $role->permisos->keyBy('id_modulo');
                    @endphp
                    <form method="POST" action="{{ route('admin.permisos.roles.update', $role->id_rol) }}" class="perm-card">
                        @csrf
                        <div class="perm-card-head">
                            <div>
                                <h4>{{ $role->nombre_rol }}</h4>
                                <p>Nivel {{ $role->nivel_acceso }} · {{ $role->estado }}</p>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                        </div>
                        <div class="panel-body" style="padding-top: 18px;">
                            <table class="matrix-table">
                                <thead>
                                    <tr>
                                        <th>Modulo</th>
                                        @foreach($actions as $label)
                                            <th>{{ $label }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($modules as $module)
                                        @php $permission = $permissions->get($module->id_modulo); @endphp
                                        <tr>
                                            <td>
                                                <div style="font-weight:800;color:#111827;">{{ $module->nombre }}</div>
                                                <div style="color:#64748b;font-size:12px;">{{ $module->descripcion }}</div>
                                            </td>
                                            @foreach($actions as $key => $label)
                                                <td>
                                                    <label style="display:inline-flex;align-items:center;gap:8px;font-weight:700;">
                                                        <input type="checkbox" name="permissions[{{ $module->id_modulo }}][{{ $key }}]" value="1" @checked(optional($permission)->{$key})>
                                                    </label>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                @endforeach
            </div>
        </div>
    </section>

    <section class="panel-card">
        <div class="panel-head">
            <div>
                <h3>Overrides por usuario</h3>
                <p>Usa "Heredar" para respetar el rol. Usa "Permitir" o "Bloquear" para excepciones puntuales.</p>
            </div>
        </div>
        <div class="panel-body">
            <form method="GET" action="{{ route('admin.permisos.index') }}" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-bottom:18px;">
                <select name="usuario_id" class="field" style="max-width: 360px;">
                    @foreach($users as $user)
                        <option value="{{ $user->id_usuario }}" @selected(optional($selectedUser)->id_usuario === $user->id_usuario)>{{ $user->nombres }} · {{ $user->role->nombre_rol ?? 'Sin rol' }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-ghost"><i class="fas fa-arrow-rotate-right"></i> Cargar</button>
            </form>

            @if($selectedUser)
                @php $userOverrides = $selectedUser->permisosUsuario->keyBy('id_modulo'); @endphp
                <form method="POST" action="{{ route('admin.permisos.usuarios.update', $selectedUser->id_usuario) }}">
                    @csrf
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap;">
                        <span class="badge">{{ $selectedUser->nombres }}</span>
                        <span style="color:#64748b;font-size:14px;">Rol base: {{ $selectedUser->role->nombre_rol ?? 'Sin rol' }}</span>
                    </div>
                    <table class="matrix-table">
                        <thead>
                            <tr>
                                <th>Modulo</th>
                                @foreach($actions as $label)
                                    <th>{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modules as $module)
                                @php $override = $userOverrides->get($module->id_modulo); @endphp
                                <tr>
                                    <td>
                                        <div style="font-weight:800;color:#111827;">{{ $module->nombre }}</div>
                                        <div style="color:#64748b;font-size:12px;">{{ $module->descripcion }}</div>
                                    </td>
                                    @foreach($actions as $key => $label)
                                        @php
                                            $value = optional($override)->{$key};
                                            $selected = $value === null ? 'inherit' : ($value ? 'allow' : 'deny');
                                        @endphp
                                        <td>
                                            <select class="field" name="overrides[{{ $module->id_modulo }}][{{ $key }}]">
                                                <option value="inherit" @selected($selected === 'inherit')>Heredar</option>
                                                <option value="allow" @selected($selected === 'allow')>Permitir</option>
                                                <option value="deny" @selected($selected === 'deny')>Bloquear</option>
                                            </select>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="display:flex;justify-content:flex-end;margin-top:18px;">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar overrides</button>
                    </div>
                </form>
            @else
                <div class="empty-box">No hay usuarios activos para configurar permisos individuales.</div>
            @endif
        </div>
    </section>
</div>
@endsection
