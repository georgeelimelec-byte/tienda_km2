@extends('layouts.admin')

@section('title', $isEdit ? 'Editar usuario' : 'Nuevo usuario')
@section('page-title', $isEdit ? 'Editar usuario' : 'Nuevo usuario')
@section('page-kicker', 'Gestion de acceso interno')

@section('topbar-actions')
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-ghost" style="text-decoration:none;">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
@endsection

@push('styles')
<style>
    .form-shell { max-width: 1040px; margin: 0 auto; }
    .panel-card { background: #ffffff; border: 1px solid var(--border); border-radius: 14px; box-shadow: var(--shadow); }
    .panel-head { padding: 22px 24px; border-bottom: 1px solid var(--border); }
    .panel-head h3 { margin: 0; font-size: 22px; font-weight: 900; color: #111827; }
    .panel-head p { margin: 6px 0 0; color: #64748b; font-size: 14px; }
    .panel-body { padding: 24px; }
    .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    .full { grid-column: 1 / -1; }
    .label { display: block; margin-bottom: 7px; color: #334155; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .03em; }
    .field { width: 100%; min-height: 46px; border: 1px solid var(--border); border-radius: 10px; background: #f8fafc; padding: 10px 13px; font-size: 14px; color: #111827; font-family: inherit; }
    .field:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(229, 140, 58, 0.12); background: #ffffff; }
    .hint { margin-top: 6px; color: #64748b; font-size: 12px; }
    .error { margin-top: 6px; color: #dc2626; font-size: 12px; font-weight: 700; }
    .form-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
    @media (max-width: 820px) { .grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="form-shell">
    <section class="panel-card">
        <div class="panel-head">
            <h3>{{ $isEdit ? 'Actualizar usuario' : 'Registrar usuario' }}</h3>
            <p>Configura rol, acceso, estado operativo y credenciales de ingreso del colaborador.</p>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ $isEdit ? route('admin.usuarios.update', $user->id_usuario) : route('admin.usuarios.store') }}">
                @csrf
                <div class="grid">
                    <div>
                        <label class="label" for="nombres">Nombres</label>
                        <input class="field" id="nombres" name="nombres" value="{{ old('nombres', $user->nombres) }}" required maxlength="100">
                        @error('nombres') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="label" for="email">Correo</label>
                        <input class="field" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" maxlength="100">
                        @error('email') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="label" for="id_rol">Rol</label>
                        <select class="field" id="id_rol" name="id_rol" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id_rol }}" @selected((string) old('id_rol', $user->id_rol) === (string) $role->id_rol)>{{ $role->nombre_rol }}</option>
                            @endforeach
                        </select>
                        @error('id_rol') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="label" for="estado">Estado</label>
                        <select class="field" id="estado" name="estado" required>
                            <option value="Activo" @selected(old('estado', $user->estado ?: 'Activo') === 'Activo')>Activo</option>
                            <option value="Inactivo" @selected(old('estado', $user->estado) === 'Inactivo')>Inactivo</option>
                        </select>
                        @error('estado') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="label" for="password">Clave</label>
                        <input class="field" id="password" name="password" type="password" {{ $isEdit ? '' : 'required' }}>
                        <div class="hint">{{ $isEdit ? 'Solo completa este campo si deseas cambiar la clave.' : 'Minimo 6 caracteres.' }}</div>
                        @error('password') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="label" for="password_confirmation">Confirmar clave</label>
                        <input class="field" id="password_confirmation" name="password_confirmation" type="password" {{ $isEdit ? '' : 'required' }}>
                    </div>
                    <div>
                        <label class="label" for="foto_url">Foto URL</label>
                        <input class="field" id="foto_url" name="foto_url" value="{{ old('foto_url', $user->foto_url) }}" maxlength="255">
                        @error('foto_url') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-ghost" style="text-decoration:none;">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ $isEdit ? 'Guardar cambios' : 'Crear usuario' }}
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
