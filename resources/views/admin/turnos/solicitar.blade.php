@extends('layouts.app')
@section('title', 'Nuevo Turno Presencial')
@section('topbar-title', 'Registrar Turno Presencial')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Operaciones · Turnos</div>
            <h1 class="page-title">Registrar Turno Presencial</h1>
            <p class="page-subtitle">Ingresá los datos del cliente y el vehículo</p>
        </div>
        <a href="{{ route('admin.turnos.index') }}" class="btn-secondary-ta">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.turnos.guardar') }}" style="max-width:820px">
    @csrf
    <input type="hidden" name="es_presencial" value="1">

    {{-- Datos del cliente --}}
    <div class="ta-card" style="margin-bottom:20px">
        <div class="ta-card-header">
            <div class="ta-card-title"><i class="bi bi-person" style="color:var(--blue)"></i> Datos del Cliente</div>
        </div>
        <div style="padding:22px; display:grid; grid-template-columns:1fr 1fr; gap:16px">
            <div>
                <label class="ta-label">Nombre <span class="req">*</label>
                <input type="text" name="name" class="ta-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name') }}" placeholder="Juan" required>
                @error('name')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="ta-label">Apellido <span class="req">*</label>
                <input type="text" name="apellido" class="ta-input {{ $errors->has('apellido') ? 'is-invalid' : '' }}"
                    value="{{ old('apellido') }}" placeholder="Pérez" required>
                @error('apellido')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="ta-label">DNI <span class="req">*</label>
                <input type="text" name="dni" class="ta-input {{ $errors->has('dni') ? 'is-invalid' : '' }}"
                    value="{{ old('dni') }}" placeholder="35123456" required maxlength="20">
                @error('dni')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="ta-label">Teléfono <span class="req">*</label>
                <input type="text" name="telefono" class="ta-input {{ $errors->has('telefono') ? 'is-invalid' : '' }}"
                    value="{{ old('telefono') }}" placeholder="3751-000000" required>
                @error('telefono')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
            </div>
            <div style="grid-column:span 2">
                <label class="ta-label">Correo electrónico</label>
                <input type="email" name="email" class="ta-input"
                    value="{{ old('email') }}" placeholder="correo@ejemplo.com">
            </div>
        </div>
    </div>

    {{-- Datos del vehículo --}}
    <div class="ta-card" style="margin-bottom:20px">
        <div class="ta-card-header">
            <div class="ta-card-title"><i class="bi bi-car-front" style="color:var(--blue)"></i> Datos del Vehículo</div>
        </div>
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
            </div>
            <div>
                <label class="ta-label">Modelo <span class="req">*</label>
                <select name="modelo_id" id="modelo_select" class="ta-input ta-select" required>
                    <option value="">Primero seleccioná la marca</option>
                </select>
            </div>
            <div>
                <label class="ta-label">Año <span class="req">*</label>
                <input type="number" name="anio" class="ta-input" value="{{ old('anio') }}"
                    min="1990" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}" required>
            </div>
            <div>
                <label class="ta-label">Patente <span class="req">*</label>
                <input type="text" name="patente" class="ta-input" value="{{ old('patente') }}"
                    placeholder="ABC123" required style="text-transform:uppercase">
            </div>
            <div>
                <label class="ta-label">Kilometraje <span class="req">*</label>
                <input type="number" name="kilometraje" class="ta-input" value="{{ old('kilometraje') }}"
                    min="0" placeholder="Ej: 85000" required>
            </div>
        </div>
    </div>

    {{-- Datos del turno --}}
    <div class="ta-card" style="margin-bottom:24px">
        <div class="ta-card-header">
            <div class="ta-card-title"><i class="bi bi-calendar-check" style="color:var(--blue)"></i> Datos del Turno</div>
        </div>
        <div style="padding:22px; display:grid; grid-template-columns:1fr 1fr; gap:16px">
            <div>
                <label class="ta-label">Fecha y hora <span class="req">*</label>
                <input type="datetime-local" name="fecha_hora_turno"
                    class="ta-input {{ $errors->has('fecha_hora_turno') ? 'is-invalid' : '' }}"
                    value="{{ old('fecha_hora_turno') }}"
                    min="{{ now()->addHour()->format('Y-m-d\TH:i') }}" required>
                @error('fecha_hora_turno')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="ta-label">Tipo de servicio <span class="req">*</label>
                <select name="tipo_servicio" class="ta-input ta-select" required>
                    <option value="">Seleccioná...</option>
                    <option value="mantenimiento_preventivo" {{ old('tipo_servicio') === 'mantenimiento_preventivo' ? 'selected' : '' }}>Mantenimiento Preventivo</option>
                    <option value="reparacion" {{ old('tipo_servicio') === 'reparacion' ? 'selected' : '' }}>Reparación</option>
                    <option value="diagnostico" {{ old('tipo_servicio') === 'diagnostico' ? 'selected' : '' }}>Diagnóstico</option>
                    <option value="service" {{ old('tipo_servicio') === 'service' ? 'selected' : '' }}>Service</option>
                    <option value="otros" {{ old('tipo_servicio') === 'otros' ? 'selected' : '' }}>Otros</option>
                </select>
            </div>
            <div>
                <label class="ta-label">Mecánico asignado</label>
                <select name="mecanico_id" class="ta-input ta-select">
                    <option value="">Sin asignar</option>
                    @foreach($mecanicos as $m)
                        <option value="{{ $m->id }}" {{ old('mecanico_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->nombreCompleto() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="grid-column:span 2">
                <label class="ta-label">Observaciones del cliente</label>
                <textarea name="observaciones" class="ta-input ta-textarea"
                    placeholder="Descripción del problema o servicio requerido...">{{ old('observaciones') }}</textarea>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:12px">
        <button type="submit" class="btn-primary-ta" style="padding:11px 28px">
            <i class="bi bi-check-circle"></i> Registrar Turno
        </button>
        <a href="{{ route('admin.turnos.index') }}" class="btn-secondary-ta" style="padding:11px 20px">
            Cancelar
        </a>
    </div>
</form>

@push('scripts')
<script>
document.getElementById('marca_select').addEventListener('change', function () {
    const marcaId = this.value;
    const modeloSel = document.getElementById('modelo_select');
    if (!marcaId) {
        modeloSel.innerHTML = '<option value="">Primero seleccioná la marca</option>';
        return;
    }
    modeloSel.innerHTML = '<option>Cargando...</option>';
    fetch(`/api/marcas/${marcaId}/modelos`)
        .then(r => r.json())
        .then(modelos => {
            modeloSel.innerHTML = '<option value="">Seleccioná el modelo...</option>';
            modelos.forEach(m => {
                const o = document.createElement('option');
                o.value = m.id;
                o.textContent = m.nombre;
                modeloSel.appendChild(o);
            });
        });
});
</script>
@endpush
@endsection
