@extends('layouts.app')
@section('title', $usuario->id ? 'Editar Usuario' : 'Nuevo Usuario')
@section('topbar-title', $usuario->id ? '<span>Editar</span> Usuario' : '<span>Nuevo</span> Usuario')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Usuarios</div>
            <h1 class="page-title">{{ $usuario->id ? 'Editar Usuario' : 'Nuevo Usuario' }}</h1>
        </div>
        <a href="{{ route('admin.usuarios.index') }}" class="btn-secondary-ta">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div style="max-width:680px">
    <div class="ta-card">
        <div class="ta-card-header">
            <div class="ta-card-title"><i class="bi bi-person"></i> Datos del Usuario</div>
        </div>
        <form method="POST" action="{{ $usuario->id ? route('admin.usuarios.actualizar', $usuario) : route('admin.usuarios.guardar') }}">
            @csrf
            @if($usuario->id) @method('PUT') @endif

            <div style="padding:22px; display:grid; grid-template-columns:1fr 1fr; gap:16px">
                <div>
                    <label class="ta-label">Nombre <span class="req">*</span></label>
                    <input type="text" name="name" class="ta-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                        value="{{ old('name', $usuario->name) }}" required>
                    @error('name')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">Apellido <span class="req">*</span></label>
                    <input type="text" name="apellido" class="ta-input {{ $errors->has('apellido') ? 'is-invalid' : '' }}"
                        value="{{ old('apellido', $usuario->apellido) }}" required>
                    @error('apellido')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">DNI</label>
                    <input type="text" name="dni" class="ta-input {{ $errors->has('dni') ? 'is-invalid' : '' }}"
                        value="{{ old('dni', $usuario->dni) }}" maxlength="20">
                    @error('dni')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">Teléfono</label>
                    <input type="text" name="telefono" class="ta-input"
                        value="{{ old('telefono', $usuario->telefono) }}" placeholder="3751-000000">
                </div>
                <div style="grid-column:span 2">
                    <label class="ta-label">Correo electrónico <span class="req">*</span></label>
                    <input type="email" name="email" class="ta-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        value="{{ old('email', $usuario->email) }}" required>
                    @error('email')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">Rol <span class="req">*</span></label>
                    <select name="rol" class="ta-input ta-select" required>
                        @foreach(['admin','administrativo','mecanico','cliente'] as $r)
                            <option value="{{ $r }}" {{ old('rol', $usuario->rol) === $r ? 'selected' : '' }}>
                                {{ ucfirst($r) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    {{-- Spacer --}}
                </div>

                {{-- Contraseña --}}
                <div style="grid-column:span 2; padding-top:8px; border-top:1px solid var(--border)">
                    <div style="font-family:'Oswald',sans-serif; font-size:.85rem; color:var(--muted); letter-spacing:.06em; margin-bottom:12px">
                        CONTRASEÑA {{ $usuario->id ? '(dejá en blanco para no cambiar)' : '' }}
                    </div>
                </div>
                <div>
                    <label class="ta-label">Contraseña {{ !$usuario->id ? '*' : '' }}</label>
                    <input type="password" name="password" class="ta-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="Mínimo 8 caracteres" {{ !$usuario->id ? 'required' : '' }}>
                    @error('password')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" class="ta-input"
                        placeholder="Repetí la contraseña">
                </div>
            </div>

            <div style="padding:16px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px">
                <a href="{{ route('admin.usuarios.index') }}" class="btn-secondary-ta">Cancelar</a>
                <button type="submit" class="btn-primary-ta">
                    <i class="bi bi-check-circle"></i>
                    {{ $usuario->id ? 'Actualizar Usuario' : 'Crear Usuario' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
