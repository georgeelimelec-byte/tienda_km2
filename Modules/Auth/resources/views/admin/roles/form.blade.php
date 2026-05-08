@extends('layouts.admin')

@section('title', $isEdit ? 'Editar rol' : 'Nuevo rol')
@section('page-title', $isEdit ? 'Editar rol' : 'Nuevo rol')
@section('page-kicker', 'Estructura de acceso')

@section('topbar-actions')
    <a href="{{ route('admin.roles.index') }}" class="btn btn-ghost" style="text-decoration:none;">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
@endsection

@push('styles')
<style>
    .form-shell { max-width: 860px; margin: 0 auto; }
    .panel-card { background: #ffffff; border: 1px solid var(--border); border-radius: 14px; box-shadow: var(--shadow); }
    .panel-head { padding: 22px 24px; border-bottom: 1px solid var(--border); }
    .panel-head h3 { margin: 0; font-size: 22px; font-weight: 900; color: #111827; }
    .panel-head p { margin: 6px 0 0; color: #64748b; font-size: 14px; }
    .panel-body { padding: 24px; }
    .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    .label { display: block; margin-bottom: 7px; color: #334155; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .03em; }
    .field { width: 100%; min-height: 46px; border: 1px solid var(--border); border-radius: 10px; background: #f8fafc; padding: 10px 13px; font-size: 14px; color: #111827; font-family: inherit; }
    .field:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(229, 140, 58, 0.12); background: #ffffff; }
    .error { margin-top: 6px; color: #dc2626; font-size: 12px; font-weight: 700; }
    .actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
    @media (max-width: 760px) { .grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="form-shell">
    <section class="panel-card">
        <div class="panel-head">
            <h3>{{ $isEdit ? 'Actualizar rol' : 'Crear rol' }}</h3>
            <p>Define un perfil reutilizable para asignarlo luego a usuarios y permisos granulares.</p>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ $isEdit ? route('admin.roles.update', $role->id_rol) : route('admin.roles.store') }}">
                @csrf
                <div class="grid">
                    <div>
                        <label class="label" for="nombre_rol">Nombre del rol</label>
                        <input class="field" id="nombre_rol" name="nombre_rol" value="{{ old('nombre_rol', $role->nombre_rol) }}" required maxlength="50">
                        @error('nombre_rol') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="label" for="nivel_acceso">Nivel de acceso</label>
                        <input class="field" id="nivel_acceso" name="nivel_acceso" type="number" min="1" max="99" value="{{ old('nivel_acceso', $role->nivel_acceso ?: 3) }}" required>
                        @error('nivel_acceso') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="label" for="estado">Estado</label>
                        <select class="field" id="estado" name="estado" required>
                            <option value="Activo" @selected(old('estado', $role->estado ?: 'Activo') === 'Activo')>Activo</option>
                            <option value="Inactivo" @selected(old('estado', $role->estado) === 'Inactivo')>Inactivo</option>
                        </select>
                        @error('estado') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="actions">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-ghost" style="text-decoration:none;">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ $isEdit ? 'Guardar cambios' : 'Crear rol' }}
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
