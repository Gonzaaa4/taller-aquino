@extends('layouts.app')
@section('title', 'Solicitar Turno')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('cliente.turnos.index') }}">Mis Turnos</a></li>
    <li class="breadcrumb-item active">Solicitar Turno</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h4 class="fw-bold mb-1"><i class="bi bi-calendar-plus me-2 text-danger"></i>Solicitar Turno</h4>
        <p class="text-muted mb-4">Completá el formulario para reservar tu turno.</p>

        {{-- Barra de progreso --}}
        <div class="d-flex gap-1 mb-4" id="barra-progreso">
            @foreach(['Mis datos','Vehículo','Servicio','Agenda','Confirmar'] as $i => $paso)
            <div class="flex-grow-1 text-center" style="font-size:.75rem">
                <div class="rounded-circle mx-auto mb-1 d-flex align-items-center justify-content-center fw-bold"
                     id="step-circle-{{ $i }}"
                     style="width:32px;height:32px;background:{{ $i===0?'#C0392B':'#e5e7eb' }};color:{{ $i===0?'white':'#9ca3af' }}">
                    {{ $i + 1 }}
                </div>
                <span id="step-label-{{ $i }}" class="{{ $i===0?'text-danger fw-semibold':'text-muted' }}">
                    {{ $paso }}
                </span>
            </div>
            @if($i < 4)
            <div class="flex-grow-1 d-flex align-items-center" style="padding-bottom:20px">
                <div style="height:2px;background:#e5e7eb;width:100%"></div>
            </div>
            @endif
            @endforeach
        </div>

        <form method="POST" action="{{ route('cliente.turnos.guardar') }}" id="formTurno">
            @csrf

            {{-- PASO 1: Datos personales --}}
            <div class="card mb-3 paso" id="paso-0">
                <div class="card-header fw-semibold"><i class="bi bi-person me-2"></i>1. Mis Datos</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                            <input type="hidden" name="nombre_cliente" value="{{ auth()->user()->name }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ auth()->user()->apellido }}" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">DNI</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->dni }}" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->telefono }}" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Correo electrónico</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->email }}" readonly>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-taller" onclick="irAPaso(1)">
                            Siguiente <i class="bi bi-chevron-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- PASO 2: Vehículo --}}
            <div class="card mb-3 paso d-none" id="paso-1">
                <div class="card-header fw-semibold"><i class="bi bi-car-front me-2"></i>2. Mi Vehículo</div>
                <div class="card-body">
                    @if($vehiculos->isNotEmpty())
                    <div class="mb-3">
                        <label class="form-label fw-semibold">¿Usás un vehículo registrado?</label>
                        <select name="vehiculo_id" id="vehiculo_id" class="form-select" onchange="toggleNuevoVehiculo(this.value)">
                            <option value="">Agregar nuevo vehículo</option>
                            @foreach($vehiculos as $v)
                                <option value="{{ $v->id }}" {{ old('vehiculo_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->marca->nombre }} {{ $v->modelo->nombre }} {{ $v->anio }} – {{ $v->patente }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div id="nuevo-vehiculo">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Marca <span class="text-danger">*</span></label>
                                <select name="marca_id" id="marca_select" class="form-select">
                                    <option value="">Seleccioná la marca...</option>
                                    @foreach($marcas as $marca)
                                        <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
                                            {{ $marca->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Modelo <span class="text-danger">*</span></label>
                                <select name="modelo_id" id="modelo_select" class="form-select">
                                    <option value="">Primero seleccioná la marca</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">Año <span class="text-danger">*</span></label>
                                <input type="number" name="anio" class="form-control" value="{{ old('anio') }}"
                                    min="1990" max="{{ date('Y') + 1 }}">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">Patente <span class="text-danger">*</span></label>
                                <input type="text" name="patente" class="form-control"
                                    value="{{ old('patente') }}" placeholder="ABC123" style="text-transform:uppercase">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">Kilometraje <span class="text-danger">*</span></label>
                                <input type="number" name="kilometraje" class="form-control" value="{{ old('kilometraje') }}" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary" onclick="irAPaso(0)">
                            <i class="bi bi-chevron-left me-1"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-taller" onclick="irAPaso(2)">
                            Siguiente <i class="bi bi-chevron-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- PASO 3: Tipo de servicio --}}
            <div class="card mb-3 paso d-none" id="paso-2">
                <div class="card-header fw-semibold"><i class="bi bi-tools me-2"></i>3. Tipo de Servicio</div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        @foreach([
                            ['mantenimiento_preventivo', 'Mantenimiento Preventivo', 'bi-gear', 'primary'],
                            ['reparacion', 'Reparación', 'bi-wrench-adjustable', 'danger'],
                            ['diagnostico', 'Diagnóstico', 'bi-search', 'info'],
                            ['service', 'Service', 'bi-check-all', 'success'],
                            ['otros', 'Otros', 'bi-three-dots', 'secondary'],
                        ] as [$val, $label, $icon, $color])
                        <div class="col-sm-4">
                            <input type="radio" name="tipo_servicio" id="ts_{{ $val }}" value="{{ $val }}"
                                class="btn-check" {{ old('tipo_servicio') === $val ? 'checked' : '' }}>
                            <label class="btn btn-outline-{{ $color }} w-100 py-3" for="ts_{{ $val }}">
                                <i class="bi {{ $icon }} d-block fs-4 mb-1"></i>
                                {{ $label }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción del problema <span class="text-muted small">(opcional)</span></label>
                        <textarea name="observaciones" class="form-control" rows="3"
                            placeholder="Describí brevemente el problema o servicio que necesitás...">{{ old('observaciones') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="irAPaso(1)">
                            <i class="bi bi-chevron-left me-1"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-taller" onclick="irAPaso(3)">
                            Siguiente <i class="bi bi-chevron-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- PASO 4: Selección de fecha/hora --}}
            <div class="card mb-3 paso d-none" id="paso-3">
                <div class="card-header fw-semibold"><i class="bi bi-calendar me-2"></i>4. Elegí tu Turno</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Fecha y hora del turno <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="fecha_hora_turno" id="fecha_hora_turno"
                                class="form-control @error('fecha_hora_turno') is-invalid @enderror"
                                value="{{ old('fecha_hora_turno') }}"
                                min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                                required>
                            @error('fecha_hora_turno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="text-muted small mt-1">
                                <i class="bi bi-info-circle me-1"></i>
                                Horarios de atención: Lun–Vie 8:00–18:00, Sáb 8:00–13:00
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary" onclick="irAPaso(2)">
                            <i class="bi bi-chevron-left me-1"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-taller" onclick="irAPaso(4)">
                            Siguiente <i class="bi bi-chevron-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- PASO 5: Confirmación --}}
            <div class="card mb-3 paso d-none" id="paso-4">
                <div class="card-header fw-semibold"><i class="bi bi-check-circle me-2"></i>5. Confirmar Turno</div>
                <div class="card-body">
                    <div class="alert alert-info py-2 small">
                        <i class="bi bi-info-circle me-1"></i>
                        Revisá los datos antes de confirmar. Una vez confirmado, podés cancelar hasta con <strong>48 hs de anticipación</strong>.
                    </div>
                    <div id="resumen-turno" class="mb-3">
                        <p class="text-muted small">← Completá los pasos anteriores para ver el resumen.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="irAPaso(3)">
                            <i class="bi bi-chevron-left me-1"></i> Anterior
                        </button>
                        <button type="submit" class="btn btn-taller px-4">
                            <i class="bi bi-check-circle me-1"></i> Confirmar Turno
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .btn-taller { background:#C0392B; color:#fff; border:none; }
    .btn-taller:hover { background:#96281B; color:#fff; }
</style>

@push('scripts')
<script>
let pasoActual = 0;

function irAPaso(num) {
    document.getElementById('paso-' + pasoActual).classList.add('d-none');
    document.getElementById('paso-' + num).classList.remove('d-none');

    // Actualizar barra progreso
    for (let i = 0; i <= 4; i++) {
        const circle = document.getElementById('step-circle-' + i);
        const label  = document.getElementById('step-label-' + i);
        if (i <= num) {
            circle.style.background = '#C0392B';
            circle.style.color = 'white';
            label.className = 'text-danger fw-semibold';
        } else {
            circle.style.background = '#e5e7eb';
            circle.style.color = '#9ca3af';
            label.className = 'text-muted';
        }
    }

    if (num === 4) actualizarResumen();
    pasoActual = num;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function actualizarResumen() {
    const fecha  = document.getElementById('fecha_hora_turno')?.value;
    const servEl = document.querySelector('input[name="tipo_servicio"]:checked');
    const vehicId = document.getElementById('vehiculo_id')?.value;

    let html = '<table class="table table-sm table-borderless">';
    if (fecha) html += `<tr><td class="text-muted">Fecha/hora</td><td>${fecha.replace('T',' ')}</td></tr>`;
    if (servEl) html += `<tr><td class="text-muted">Servicio</td><td>${servEl.value.replace(/_/g,' ')}</td></tr>`;
    if (vehicId) html += `<tr><td class="text-muted">Vehículo</td><td>${document.getElementById('vehiculo_id').options[document.getElementById('vehiculo_id').selectedIndex].text}</td></tr>`;
    html += '</table>';
    document.getElementById('resumen-turno').innerHTML = html;
}

function toggleNuevoVehiculo(val) {
    document.getElementById('nuevo-vehiculo').style.display = val ? 'none' : 'block';
}

// Carga dinámica de modelos por marca
document.getElementById('marca_select')?.addEventListener('change', function () {
    const marcaId = this.value;
    const modeloSel = document.getElementById('modelo_select');
    if (!marcaId) { modeloSel.innerHTML = '<option>Primero seleccioná la marca</option>'; return; }
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
