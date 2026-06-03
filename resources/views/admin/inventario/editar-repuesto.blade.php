@extends('layouts.app')
@section('title', 'Editar Repuesto')
@section('topbar-title', 'Editar <span>Repuesto</span>')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Inventario · Repuestos</div>
            <h1 class="page-title">Editar Repuesto</h1>
            <p class="page-subtitle">{{ $repuesto->nombre }}</p>
        </div>
        <a href="{{ route('admin.inventario.repuestos') }}" class="btn-secondary-ta">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div style="max-width:720px">
    <div class="ta-card">
        <div class="ta-card-header">
            <div class="ta-card-title"><i class="bi bi-pencil" style="color:var(--blue)"></i> Datos del Repuesto</div>
        </div>
        <form method="POST" action="{{ route('admin.inventario.repuesto.actualizar', $repuesto) }}">
            @csrf
            @method('PUT')
            <div style="padding:22px; display:grid; grid-template-columns:1fr 1fr; gap:16px">
                <div>
                    <label class="ta-label">Código interno</label>
                    <input type="text" name="codigo" class="ta-input"
                        value="{{ old('codigo', $repuesto->codigo) }}" placeholder="Ej: REP-001">
                    @error('codigo')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">Categoría <span class="req">*</span></label>
                    <select name="categoria" class="ta-input ta-select" required>
                        @foreach(['motor','transmision','frenos','suspension','electrico','lubricantes','filtros','otros'] as $cat)
                            <option value="{{ $cat }}" {{ old('categoria', $repuesto->categoria) === $cat ? 'selected' : '' }}>
                                {{ ucfirst($cat) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:span 2">
                    <label class="ta-label">Nombre <span class="req">*</span></label>
                    <input type="text" name="nombre" class="ta-input {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
                        value="{{ old('nombre', $repuesto->nombre) }}" required>
                    @error('nombre')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div style="grid-column:span 2">
                    <label class="ta-label">Descripción</label>
                    <textarea name="descripcion" class="ta-input ta-textarea">{{ old('descripcion', $repuesto->descripcion) }}</textarea>
                </div>
                <div>
                    <label class="ta-label">Stock actual <span class="req">*</span></label>
                    <input type="number" name="cantidad_stock" class="ta-input"
                        value="{{ old('cantidad_stock', $repuesto->cantidad_stock) }}" min="0" required>
                </div>
                <div>
                    <label class="ta-label">Stock mínimo <span class="req">*</span></label>
                    <input type="number" name="stock_minimo" class="ta-input"
                        value="{{ old('stock_minimo', $repuesto->stock_minimo) }}" min="0" required>
                </div>
                <div>
                    <label class="ta-label">Costo unitario ($) <span class="req">*</span></label>
                    <input type="number" name="costo" class="ta-input"
                        value="{{ old('costo', $repuesto->costo) }}" min="0" step="0.01" required>
                </div>
                <div>
                    <label class="ta-label">Ubicación en taller</label>
                    <input type="text" name="ubicacion_taller" class="ta-input"
                        value="{{ old('ubicacion_taller', $repuesto->ubicacion_taller) }}">
                </div>
                <div style="grid-column:span 2">
                    <label class="ta-label">Proveedor</label>
                    <select name="proveedor_id" class="ta-input ta-select">
                        <option value="">Sin proveedor asignado</option>
                        @foreach($proveedores as $p)
                            <option value="{{ $p->id }}" {{ old('proveedor_id', $repuesto->proveedor_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="padding:16px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px">
                <a href="{{ route('admin.inventario.repuestos') }}" class="btn-secondary-ta">Cancelar</a>
                <button type="submit" class="btn-primary-ta">
                    <i class="bi bi-check-circle"></i> Actualizar Repuesto
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
