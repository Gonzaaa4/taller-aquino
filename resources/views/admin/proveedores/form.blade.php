@extends('layouts.app')
@section('title', $proveedor->id ? 'Editar Proveedor' : 'Nuevo Proveedor')
@section('topbar-title', $proveedor->id ? '<span>Editar</span> Proveedor' : '<span>Nuevo</span> Proveedor')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Proveedores</div>
            <h1 class="page-title">{{ $proveedor->id ? 'Editar Proveedor' : 'Nuevo Proveedor' }}</h1>
        </div>
        <a href="{{ route('admin.proveedores.index') }}" class="btn-secondary-ta">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div style="max-width:680px">
    <div class="ta-card">
        <div class="ta-card-header">
            <div class="ta-card-title"><i class="bi bi-truck"></i> Datos del Proveedor</div>
        </div>
        <form method="POST" action="{{ $proveedor->id ? route('admin.proveedores.actualizar', $proveedor) : route('admin.proveedores.guardar') }}">
            @csrf
            @if($proveedor->id) @method('PUT') @endif

            <div style="padding:22px; display:grid; grid-template-columns:1fr 1fr; gap:16px">
                <div style="grid-column:span 2">
                    <label class="ta-label">Nombre <span class="req">*</span></label>
                    <input type="text" name="nombre" class="ta-input {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
                        value="{{ old('nombre', $proveedor->nombre) }}" required placeholder="Razón social o nombre">
                    @error('nombre')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">Teléfono</label>
                    <input type="text" name="telefono" class="ta-input"
                        value="{{ old('telefono', $proveedor->telefono) }}" placeholder="Ej: 3751-000000">
                </div>
                <div>
                    <label class="ta-label">Correo electrónico</label>
                    <input type="email" name="email" class="ta-input"
                        value="{{ old('email', $proveedor->email) }}" placeholder="contacto@proveedor.com">
                </div>
                <div style="grid-column:span 2">
                    <label class="ta-label">Dirección</label>
                    <input type="text" name="direccion" class="ta-input"
                        value="{{ old('direccion', $proveedor->direccion) }}" placeholder="Ciudad, Provincia">
                </div>
                <div>
                    <label class="ta-label">Categoría</label>
                    <select name="categoria" class="ta-input ta-select">
                        <option value="">Sin categoría</option>
                        @foreach(['repuestos','lubricantes','herramientas','electricidad','otros'] as $c)
                            <option value="{{ $c }}" {{ old('categoria', $proveedor->categoria) === $c ? 'selected' : '' }}>
                                {{ ucfirst($c) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ta-label">Estado</label>
                    <select name="activo" class="ta-input ta-select">
                        <option value="1" {{ old('activo', $proveedor->activo ?? 1) ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ !old('activo', $proveedor->activo ?? 1) ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div style="grid-column:span 2">
                    <label class="ta-label">Notas</label>
                    <textarea name="notas" class="ta-input ta-textarea" placeholder="Condiciones de pago, plazos de entrega, etc.">{{ old('notas', $proveedor->notas) }}</textarea>
                </div>
            </div>

            <div style="padding:16px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px">
                <a href="{{ route('admin.proveedores.index') }}" class="btn-secondary-ta">Cancelar</a>
                <button type="submit" class="btn-primary-ta">
                    <i class="bi bi-check-circle"></i>
                    {{ $proveedor->id ? 'Actualizar' : 'Guardar Proveedor' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
