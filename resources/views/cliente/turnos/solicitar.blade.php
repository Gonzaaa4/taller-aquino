@extends('layouts.app')
@section('title', 'Solicitar Turno')
@section('topbar-title', 'Solicitar Turno')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Portal del Cliente</div>
            <h1 class="page-title">Solicitar Turno</h1>
            <p class="page-subtitle">Completá los datos para reservar tu turno</p>
        </div>
        <a href="{{ route('cliente.dashboard') }}" class="btn-secondary-ta">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

{{-- Barra de pasos --}}
<div style="display:flex; align-items:center; margin-bottom:28px; gap:0" id="pasos-bar">
    @foreach(['Mis Datos','Vehículo','Servicio','Fecha','Confirmar'] as $i => $paso)
    <div style="flex:1; text-align:center; position:relative">
        <div id="step-circle-{{ $i }}"
             style="width:36px; height:36px; border-radius:50%; margin:0 auto 6px;
             display:flex; align-items:center; justify-content:center;
             font-family:'Oswald',sans-serif; font-size:.85rem; font-weight:600;
             transition: all .3s;
             background:{{ $i===0 ? '#1255a1' : '#e8f0f8' }};
             color:{{ $i===0 ? 'white' : '#5a7a95' }};
             box-shadow:{{ $i===0 ? '0 4px 12px rgba(18,85,161,.35)' : 'none' }}">
            {{ $i + 1 }}
        </div>
        <div id="step-label-{{ $i }}" style="font-size:.72rem; {{ $i===0 ? 'color:var(--blue); font-weight:600' : 'color:var(--muted)' }}">
            {{ $paso }}
        </div>
    </div>
    @if($i < 4)
    <div style="height:2px; width:40px; background:var(--border); flex-shrink:0; margin-bottom:22px"></div>
    @endif
    @endforeach
</div>

<form method="POST" action="{{ route('cliente.turnos.guardar') }}" id="formTurno" style="max-width:700px">
    @csrf

    {{-- PASO 0: Mis datos --}}
    <div class="ta-card paso" id="paso-0" style="margin-bottom:16px">
        <div class="ta-card-header">
            <div class="ta-card-title"><i class="bi bi-person" style="color:var(--blue)"></i> 1. Mis Datos</div>
        </div>
        <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:14px">
            @foreach([['Nombre','name',auth()->user()->name],['Apellido','apellido',auth()->user()->apellido],['DNI','dni',auth()->user()->dni],['Teléfono','telefono',auth()->user()->telefono]] as [$label,$name,$val])
            <div>
                <label class="ta-label">{{ $label }}</label>
                <input type="text" class="ta-input" value="{{ $val }}" readonly
                    style="background:var(--card); color:var(--muted); cursor:not-allowed">
            </div>
            @endforeach
            <div style="grid-column:span 2">
                <label class="ta-label">Correo electrónico</label>
                <input type="text" class="ta-input" value="{{ auth()->user()->email }}" readonly
                    style="background:var(--card); color:var(--muted); cursor:not-allowed">
            </div>
        </div>
        <div style="padding:0 20px 20px; display:flex; justify-content:flex-end">
            <button type="button" class="btn-primary-ta" onclick="irAPaso(1)">
                Siguiente <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    {{-- PASO 1: Vehículo --}}
    <div class="ta-card paso" id="paso-1" style="display:none; margin-bottom:16px">
        <div class="ta-card-header">
            <div class="ta-card-title"><i class="bi bi-car-front" style="color:var(--blue)"></i> 2. Mi Vehículo</div>
        </div>
        <div style="padding:20px">
            @if($vehiculos->isNotEmpty())
            <div style="margin-bottom:16px">
                <label class="ta-label">Usá un vehículo registrado</label>
                <select name="vehiculo_id" id="vehiculo_id" class="ta-input ta-select" onchange="toggleNuevoVehiculo(this.value)">
                    <option value="">Agregar nuevo vehículo</option>
                    @foreach($vehiculos as $v)
                    <option value="{{ $v->id }}" {{ old('vehiculo_id') == $v->id ? 'selected' : '' }}>
                        {{ $v->marca->nombre }} {{ $v->modelo->nombre }} {{ $v->anio }} — {{ $v->patente }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div id="separador" style="display:flex; align-items:center; gap:12px; margin-bottom:16px; color:var(--muted); font-size:.8rem">
                <div style="flex:1; height:1px; background:var(--border)"></div> o ingresá uno nuevo <div style="flex:1; height:1px; background:var(--border)"></div>
            </div>
            @endif

            <div id="nuevo-vehiculo">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px">
                    <div>
                        <label class="ta-label">Marca <span class="req">*</label>
                        <select name="marca_id" id="marca_select" class="ta-input ta-select">
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
                        <select name="modelo_id" id="modelo_select" class="ta-input ta-select">
                            <option value="">Primero elegí la marca</option>
                        </select>
                    </div>
                    <div>
                        <label class="ta-label">Año <span class="req">*</label>
                        <input type="number" name="anio" class="ta-input" value="{{ old('anio') }}"
                            min="1990" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}">
                    </div>
                    <div>
                        <label class="ta-label">Patente <span class="req">*</label>
                        <input type="text" name="patente" class="ta-input" value="{{ old('patente') }}"
                            placeholder="ABC123" style="text-transform:uppercase">
                    </div>
                    <div>
                        <label class="ta-label">Kilometraje <span class="req">*</label>
                        <input type="number" name="kilometraje" class="ta-input" value="{{ old('kilometraje') }}" min="0" placeholder="Ej: 85000">
                    </div>
                </div>
            </div>
        </div>
        <div style="padding:0 20px 20px; display:flex; justify-content:space-between">
            <button type="button" class="btn-secondary-ta" onclick="irAPaso(0)">
                <i class="bi bi-chevron-left"></i> Anterior
            </button>
            <button type="button" class="btn-primary-ta" onclick="irAPaso(2)">
                Siguiente <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    {{-- PASO 2: Servicio --}}
    <div class="ta-card paso" id="paso-2" style="display:none; margin-bottom:16px">
        <div class="ta-card-header">
            <div class="ta-card-title"><i class="bi bi-tools" style="color:var(--blue)"></i> 3. Tipo de Servicio</div>
        </div>
        <div style="padding:20px">
            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:18px">
                @foreach([
                    ['mantenimiento_preventivo','bi-gear','Mantenimiento Preventivo','Cambio de aceite, filtros, revisión general'],
                    ['reparacion','bi-wrench-adjustable','Reparación','Arreglo de fallas o problemas detectados'],
                    ['diagnostico','bi-search','Diagnóstico','Revisión para detectar problemas'],
                    ['service','bi-check-all','Service','Service completo del vehículo'],
                    ['otros','bi-three-dots','Otros','Otro tipo de servicio'],
                ] as [$val,$icon,$titulo,$desc])
                <label style="cursor:pointer">
                    <input type="radio" name="tipo_servicio" value="{{ $val }}" class="d-none tipo-radio"
                        {{ old('tipo_servicio') === $val ? 'checked' : '' }}>
                    <div class="servicio-card" style="padding:14px; border:2px solid var(--border); border-radius:10px; text-align:center; transition:all .18s">
                        <i class="bi {{ $icon }}" style="font-size:1.5rem; color:var(--blue); display:block; margin-bottom:6px"></i>
                        <div style="font-weight:600; font-size:.84rem; color:var(--navy); margin-bottom:3px">{{ $titulo }}</div>
                        <div style="font-size:.72rem; color:var(--muted); line-height:1.3">{{ $desc }}</div>
                    </div>
                </label>
                @endforeach
            </div>
            <div>
                <label class="ta-label">Descripción del problema <span style="color:var(--muted); font-weight:400">(opcional)</label>
                <textarea name="observaciones" class="ta-input ta-textarea"
                    placeholder="Describí brevemente el problema o lo que necesitás que le revisen al auto...">{{ old('observaciones') }}</textarea>
            </div>
        </div>
        <div style="padding:0 20px 20px; display:flex; justify-content:space-between">
            <button type="button" class="btn-secondary-ta" onclick="irAPaso(1)">
                <i class="bi bi-chevron-left"></i> Anterior
            </button>
            <button type="button" class="btn-primary-ta" onclick="irAPaso(3)">
                Siguiente <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    {{-- PASO 3: Fecha --}}
    <div class="ta-card paso" id="paso-3" style="display:none; margin-bottom:16px">
        <div class="ta-card-header">
            <div class="ta-card-title"><i class="bi bi-calendar" style="color:var(--blue)"></i> 4. Elegí el Horario</div>
        </div>
        <div style="padding:20px">
            <div style="max-width:320px">
                <label class="ta-label">Fecha y hora del turno <span class="req">*</label>
                <input type="datetime-local" name="fecha_hora_turno" id="fecha_hora_turno"
                    class="ta-input {{ $errors->has('fecha_hora_turno') ? 'is-invalid' : '' }}"
                    value="{{ old('fecha_hora_turno') }}"
                    min="{{ now()->addHour()->format('Y-m-d\TH:i') }}" required>
                @error('fecha_hora_turno')<div class="ta-invalid-msg">{{ $message }}</div>@enderror
            </div>
            <div style="margin-top:14px; background:var(--card); border:1px solid var(--border); border-radius:9px; padding:12px 16px; font-size:.84rem; color:var(--muted)">
                <i class="bi bi-info-circle" style="margin-right:6px; color:var(--blue)"></i>
                <strong>Horarios de atención:</strong> Lunes a Viernes 8:00–18:00 hs · Sábados 8:00–13:00 hs
            </div>
        </div>
        <div style="padding:0 20px 20px; display:flex; justify-content:space-between">
            <button type="button" class="btn-secondary-ta" onclick="irAPaso(2)">
                <i class="bi bi-chevron-left"></i> Anterior
            </button>
            <button type="button" class="btn-primary-ta" onclick="irAPaso(4)">
                Siguiente <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    {{-- PASO 4: Confirmar --}}
    <div class="ta-card paso" id="paso-4" style="display:none; margin-bottom:16px">
        <div class="ta-card-header">
            <div class="ta-card-title"><i class="bi bi-check-circle" style="color:var(--ok)"></i> 5. Confirmar Turno</div>
        </div>
        <div style="padding:20px">
            <div class="ta-alert info" style="margin-bottom:16px">
                <span class="ta-alert-icon"><i class="bi bi-info-circle-fill"></i>
                <div>Revisá los datos antes de confirmar. Podés cancelar con hasta <strong>48 horas de anticipación</strong>. Tenés un máximo de <strong>2 cancelaciones por mes</strong>.</div>
            </div>
            <div id="resumen" style="background:var(--card); border:1px solid var(--border); border-radius:10px; padding:16px">
                <p style="color:var(--muted); font-size:.84rem; text-align:center">← Completá los pasos anteriores</p>
            </div>
        </div>
        <div style="padding:0 20px 20px; display:flex; justify-content:space-between">
            <button type="button" class="btn-secondary-ta" onclick="irAPaso(3)">
                <i class="bi bi-chevron-left"></i> Anterior
            </button>
            <button type="submit" class="btn-ok-ta">
                <i class="bi bi-check-circle"></i> Confirmar Turno
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
let pasoActual = 0;

function irAPaso(num) {
    document.getElementById('paso-' + pasoActual).style.display = 'none';
    document.getElementById('paso-' + num).style.display = 'block';

    for (let i = 0; i <= 4; i++) {
        const circle = document.getElementById('step-circle-' + i);
        const label  = document.getElementById('step-label-' + i);
        if (i <= num) {
            circle.style.background = '#1255a1';
            circle.style.color = 'white';
            circle.style.boxShadow = '0 4px 12px rgba(18,85,161,.35)';
            label.style.color = 'var(--blue)';
            label.style.fontWeight = '600';
        } else {
            circle.style.background = '#e8f0f8';
            circle.style.color = '#5a7a95';
            circle.style.boxShadow = 'none';
            label.style.color = 'var(--muted)';
            label.style.fontWeight = '400';
        }
    }

    if (num === 4) actualizarResumen();
    pasoActual = num;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function actualizarResumen() {
    const fecha  = document.getElementById('fecha_hora_turno')?.value?.replace('T', ' ');
    const servEl = document.querySelector('input[name="tipo_servicio"]:checked');
    const vehicId = document.getElementById('vehiculo_id')?.value;
    const vehicSel = vehicId ? document.getElementById('vehiculo_id')?.options[document.getElementById('vehiculo_id')?.selectedIndex]?.text : null;

    let html = '<table style="width:100%; font-size:.88rem; border-collapse:collapse">';
    if (fecha) html += `<tr><td style="padding:6px 0; color:var(--muted); width:40%">Fecha y hora</td><td style="padding:6px 0; font-weight:600; color:var(--navy)">${fecha}</td></tr>`;
    if (servEl) html += `<tr><td style="padding:6px 0; color:var(--muted)">Servicio</td><td style="padding:6px 0; font-weight:600; color:var(--navy)">${servEl.value.replace(/_/g,' ')}</td></tr>`;
    if (vehicSel && vehicId) html += `<tr><td style="padding:6px 0; color:var(--muted)">Vehículo</td><td style="padding:6px 0; font-weight:600; color:var(--navy)">${vehicSel}</td></tr>`;
    html += '</table>';
    document.getElementById('resumen').innerHTML = html;
}

// Mostrar selección de servicio
document.querySelectorAll('.tipo-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.servicio-card').forEach(c => {
            c.style.borderColor = 'var(--border)';
            c.style.background = 'white';
        });
        this.nextElementSibling.style.borderColor = 'var(--blue)';
        this.nextElementSibling.style.background = 'rgba(18,85,161,.05)';
    });
    if (radio.checked) {
        radio.nextElementSibling.style.borderColor = 'var(--blue)';
        radio.nextElementSibling.style.background = 'rgba(18,85,161,.05)';
    }
});

function toggleNuevoVehiculo(val) {
    const nv = document.getElementById('nuevo-vehiculo');
    const sep = document.getElementById('separador');
    nv.style.display = val ? 'none' : 'block';
    if (sep) sep.style.display = val ? 'none' : 'flex';
}

document.getElementById('marca_select')?.addEventListener('change', function () {
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
