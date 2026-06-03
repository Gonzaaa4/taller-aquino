@extends('layouts.app')
@section('title', isset($repuesto) && $repuesto->id ? 'Editar Repuesto' : 'Nuevo Repuesto')
@section('topbar-title', isset($repuesto) && $repuesto->id ? 'Editar Repuesto' : 'Nuevo Repuesto')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Inventario · Repuestos</div>
            <h1 class="page-title">{{ isset($repuesto) && $repuesto->id ? 'Editar Repuesto' : 'Nuevo Repuesto' }}</h1>
        </div>
        <a href="{{ route('admin.inventario.repuestos') }}" class="btn-secondary-ta">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div style="max-width:720px">
    <div class="ta-card">
        <div class="ta-card-header">
            <div class="ta-card-title"><i class="bi bi-box-seam" style="color:var(--blue)"></i> Datos del Repuesto</div>
        </div>
        <form method="POST" action="{{ isset($repuesto) && $repuesto->id ? route('admin.inventario.repuesto.actualizar', $repuesto) : route('admin.inventario.repuesto.guardar') }}">
            @csrf
            @if(isset($repuesto) && $repuesto->id) @method('PUT') @endif

            <div style="padding:22px; display:grid; grid-template-columns:1fr 1fr; gap:16px">
                <div>
                    <label class="ta-label">Código interno</label>
                    <input type="text" name="codigo" class="ta-input"
                        value="{{ old('codigo', $repuesto->codigo ?? '') }}" placeholder="Ej: REP-001">
                    @error('codigo')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">Categoría <span class="req">*</label>
                    <select name="categoria" class="ta-input ta-select" required>
                        @foreach(['motor','transmision','frenos','suspension','electrico','lubricantes','filtros','otros'] as $cat)
                            <option value="{{ $cat }}" {{ old('categoria', $repuesto->categoria ?? '') === $cat ? 'selected' : '' }}>
                                {{ ucfirst($cat) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:span 2">
                    <label class="ta-label">Nombre <span class="req">*</label>
                    <input type="text" name="nombre" class="ta-input {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
                        value="{{ old('nombre', $repuesto->nombre ?? '') }}" required placeholder="Ej: Filtro de aceite universal">
                    @error('nombre')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div style="grid-column:span 2">
                    <label class="ta-label">Descripción</label>
                    <textarea name="descripcion" class="ta-input ta-textarea"
                        placeholder="Detalles adicionales sobre el repuesto...">{{ old('descripcion', $repuesto->descripcion ?? '') }}</textarea>
                </div>
                <div>
                    <label class="ta-label">Stock actual <span class="req">*</label>
                    <input type="number" name="cantidad_stock" class="ta-input {{ $errors->has('cantidad_stock') ? 'is-invalid' : '' }}"
                        value="{{ old('cantidad_stock', $repuesto->cantidad_stock ?? 0) }}" min="0" required>
                    @error('cantidad_stock')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">Stock mínimo <span class="req">*</label>
                    <input type="number" name="stock_minimo" class="ta-input {{ $errors->has('stock_minimo') ? 'is-invalid' : '' }}"
                        value="{{ old('stock_minimo', $repuesto->stock_minimo ?? 1) }}" min="0" required>
                    @error('stock_minimo')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">Costo unitario ($) <span class="req">*</label>
                    <input type="number" name="costo" class="ta-input {{ $errors->has('costo') ? 'is-invalid' : '' }}"
                        value="{{ old('costo', $repuesto->costo ?? 0) }}" min="0" step="0.01" required>
                    @error('costo')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">Ubicación en taller</label>
                    <input type="text" name="ubicacion_taller" class="ta-input"
                        value="{{ old('ubicacion_taller', $repuesto->ubicacion_taller ?? '') }}" placeholder="Ej: Estante A3">
                </div>
                <div style="grid-column:span 2">
                    <label class="ta-label">Proveedor</label>
                    <select name="proveedor_id" class="ta-input ta-select">
                        <option value="">Sin proveedor asignado</option>
                        @foreach($proveedores as $p)
                            <option value="{{ $p->id }}" {{ old('proveedor_id', $repuesto->proveedor_id ?? '') == $p->id ? 'selected' : '' }}>
                                {{ $p->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="padding:16px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px">
                <a href="{{ route('admin.inventario.repuestos') }}" class="btn-secondary-ta">Cancelar</a>
                <button type="submit" class="btn-primary-ta">
                    <i class="bi bi-check-circle"></i>
                    {{ isset($repuesto) && $repuesto->id ? 'Actualizar Repuesto' : 'Guardar Repuesto' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
