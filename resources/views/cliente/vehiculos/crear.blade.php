@extends('layouts.app')
@section('title', 'Agregar Vehículo')
@section('topbar-title', 'Agregar Vehículo')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Mis Vehículos</div>
            <h1 class="page-title">Agregar Vehículo</h1>
        </div>
        <a href="{{ route('cliente.vehiculos.index') }}" class="btn-secondary-ta">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div style="max-width:580px">
    <div class="ta-card">
        <div class="ta-card-header">
            <div class="ta-card-title"><i class="bi bi-car-front" style="color:var(--blue)"></i> Datos del Vehículo</div>
        </div>
        <form method="POST" action="{{ route('cliente.vehiculos.guardar') }}">
            @csrf
            <div style="padding:22px; display:grid; grid-template-columns:1fr 1fr; gap:16px">
                <div>
                    <label class="ta-label">Marca <span class="req">*</label>
                    <select name="marca_id" id="marca_select" class="ta-input ta-select" required>
                        <option value="">Seleccioná la marca...</option>
                        @foreach($marcas as $marca)
                        <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
                            {{ $marca->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('marca_id')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">Modelo <span class="req">*</label>
                    <select name="modelo_id" id="modelo_select" class="ta-input ta-select" required>
                        <option value="">Primero elegí la marca</option>
                    </select>
                    @error('modelo_id')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">Año <span class="req">*</label>
                    <input type="number" name="anio" class="ta-input {{ $errors->has('anio') ? 'is-invalid' : '' }}"
                        value="{{ old('anio') }}" min="1990" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}" required>
                    @error('anio')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">Patente <span class="req">*</label>
                    <input type="text" name="patente" class="ta-input {{ $errors->has('patente') ? 'is-invalid' : '' }}"
                        value="{{ old('patente') }}" placeholder="ABC123" required style="text-transform:uppercase">
                    @error('patente')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">Kilometraje <span class="req">*</label>
                    <input type="number" name="kilometraje" class="ta-input {{ $errors->has('kilometraje') ? 'is-invalid' : '' }}"
                        value="{{ old('kilometraje') }}" min="0" placeholder="Ej: 85000" required>
                    @error('kilometraje')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="ta-label">Color</label>
                    <input type="text" name="color" class="ta-input"
                        value="{{ old('color') }}" placeholder="Ej: Blanco">
                </div>
            </div>
            <div style="padding:16px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px">
                <a href="{{ route('cliente.vehiculos.index') }}" class="btn-secondary-ta">Cancelar</a>
                <button type="submit" class="btn-primary-ta">
                    <i class="bi bi-check-circle"></i> Guardar Vehículo
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('marca_select').addEventListener('change', function () {
    const marcaId = this.value;
    const modeloSel = document.getElementById('modelo_select');
    if (!marcaId) { modeloSel.innerHTML = '<option>Primero elegí la marca</option>'; return; }
    modeloSel.innerHTML = '<option>Cargando...</option>';
    fetch(`/api/marcas/${marcaId}/modelos`)
        .then(r => r.json())
        .then(modelos => {
            modeloSel.innerHTML = '<option value="">Seleccioná el modelo...</option>';
            modelos.forEach(m => {
                const o = document.createElement('option');
                o.value = m.id; o.textContent = m.nombre;
                modeloSel.appendChild(o);
            });
        });
});
</script>
@endpush
@endsection
