@extends('layouts.app')
@section('title', 'Registrar Turno Presencial')
@section('topbar-title', 'Registrar Turno Presencial')

@push('styles')
<style>
/* ─── PROGRESS BAR ─── */
.steps { display:flex; gap:0; margin-bottom:28px; }
.step { flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; position:relative; }
.step::before { content:''; position:absolute; top:13px; left:50%; right:-50%; height:2px; background:var(--border); z-index:0; }
.step:last-child::before { display:none; }
.step-dot {
    width:28px; height:28px; border-radius:50%;
    background:var(--light); border:2px solid var(--border);
    display:flex; align-items:center; justify-content:center;
    font-size:.7rem; font-weight:700; color:var(--muted);
    position:relative; z-index:1; transition:all .3s;
}
.step.active .step-dot { background:var(--accent); border-color:var(--accent); color:#fff; box-shadow:0 0 12px rgba(46,141,255,.5); }
.step.done   .step-dot { background:var(--ok); border-color:var(--ok); color:#fff; }
.step-label  { font-size:.65rem; color:var(--muted); letter-spacing:.06em; text-transform:uppercase; }
.step.active .step-label { color:var(--accent); font-weight:600; }
.step.done   .step-label { color:var(--ok); }

/* ─── SECTIONS ─── */
.form-section { background:#fff; border-radius:12px; margin-bottom:18px; box-shadow:0 2px 10px rgba(0,0,0,.06); overflow:hidden; }
.section-header { background:var(--light); border-bottom:1px solid var(--border); padding:12px 20px; display:flex; align-items:center; gap:10px; }
.section-icon { width:30px; height:30px; background:var(--blue); border-radius:7px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.section-icon i { color:white; font-size:.9rem; }
.section-title { font-family:'Oswald',sans-serif; font-size:.95rem; color:var(--navy); letter-spacing:.04em; }
.section-body { padding:18px 20px; }

/* ─── FIELDS ─── */
.field-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:14px 16px; }
.field-full { grid-column:1/-1; }
.field-group { display:flex; flex-direction:column; gap:4px; }
.field-group label { font-size:.76rem; font-weight:600; color:var(--muted); letter-spacing:.05em; text-transform:uppercase; }
.req-star { color:var(--error); }
.field-input {
    border:1.5px solid var(--border); border-radius:7px; padding:9px 12px;
    font-family:'Source Sans 3',sans-serif; font-size:.92rem; color:var(--text);
    outline:none; transition:border-color .2s, box-shadow .2s; width:100%; background:#fff;
}
.field-input:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(46,141,255,.12); }
.field-input.error { border-color:var(--error); }
.field-input.valid { border-color:var(--ok); }
.field-input[readonly] { background:var(--card); color:var(--muted); cursor:default; }
.field-error { font-size:.75rem; color:var(--error); min-height:15px; }
.field-hint  { font-size:.75rem; color:var(--muted); }

/* ─── RADIO CARDS ─── */
.radio-group { display:flex; gap:12px; flex-wrap:wrap; }
.radio-card {
    flex:1; min-width:160px; display:flex; align-items:center; gap:12px;
    border:2px solid var(--border); border-radius:10px; padding:14px;
    cursor:pointer; transition:all .2s; background:#fff;
}
.radio-card:hover { border-color:var(--accent); background:#f0f7ff; }
.radio-card input[type="radio"] { display:none; }
.radio-card.selected { border-color:var(--blue); background:rgba(18,85,161,.05); }
.radio-card-icon { width:36px; height:36px; border-radius:8px; background:var(--muted); display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:background .2s; }
.radio-card.selected .radio-card-icon { background:var(--blue); }
.radio-card-icon i { color:white; font-size:1rem; }
.radio-card-text strong { display:block; font-size:.88rem; color:var(--navy); font-weight:600; }
.radio-card-text span   { font-size:.76rem; color:var(--muted); }

/* ─── CALENDAR ─── */
.calendar-wrap { background:var(--card); border-radius:10px; overflow:hidden; border:1px solid var(--border); }
.cal-header { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:var(--navy); }
.cal-header-title { font-family:'Oswald',sans-serif; font-size:.95rem; color:white; letter-spacing:.06em; }
.cal-nav-btn { background:rgba(255,255,255,.1); border:none; color:white; width:28px; height:28px; border-radius:6px; cursor:pointer; font-size:1.1rem; display:flex; align-items:center; justify-content:center; transition:background .18s; }
.cal-nav-btn:hover { background:rgba(255,255,255,.2); }
.cal-days-header { display:grid; grid-template-columns:repeat(7,1fr); text-align:center; background:rgba(18,85,161,.08); }
.cal-days-header span { font-size:.68rem; font-weight:700; color:var(--muted); letter-spacing:.06em; padding:6px 0; text-transform:uppercase; }
.cal-grid { display:grid; grid-template-columns:repeat(7,1fr); padding:8px; gap:4px; }
.cal-day { aspect-ratio:1; display:flex; align-items:center; justify-content:center; border-radius:7px; font-size:.85rem; font-weight:500; }
.cal-day.past { color:rgba(90,122,149,.3); cursor:not-allowed; }
.cal-day.available { color:var(--text); cursor:pointer; transition:all .18s; }
.cal-day.available:hover { background:rgba(46,141,255,.12); color:var(--blue); }
.cal-day.today { font-weight:700; color:var(--blue); }
.cal-day.selected { background:var(--blue); color:white !important; font-weight:700; box-shadow:0 2px 8px rgba(18,85,161,.35); }

/* ─── TIME SLOTS ─── */
.time-slots { padding:14px 16px 16px; border-top:1px solid var(--border); }
.time-slots-title { font-size:.74rem; font-weight:700; color:var(--muted); letter-spacing:.08em; text-transform:uppercase; margin-bottom:10px; }
.slots-grid { display:flex; flex-wrap:wrap; gap:8px; }
.slot-btn { padding:7px 14px; border-radius:7px; border:1.5px solid var(--border); background:#fff; color:var(--text); font-size:.86rem; font-weight:600; cursor:pointer; transition:all .18s; }
.slot-btn:hover:not(.taken) { border-color:var(--accent); color:var(--blue); background:rgba(46,141,255,.06); }
.slot-btn.selected { background:var(--accent); border-color:var(--accent); color:white; }
.slot-btn.taken { background:var(--card); color:rgba(90,122,149,.4); cursor:not-allowed; text-decoration:line-through; }
.time-placeholder { font-size:.84rem; color:var(--muted); font-style:italic; }

/* ─── CLIENTE CHIP ─── */
.cliente-chip {
    background:linear-gradient(135deg,#0b1c2e,#1255a1);
    border-radius:10px; padding:14px 18px; display:flex; align-items:center; gap:14px;
    margin-top:12px;
}
.cli-av { width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; font-family:'Oswald',sans-serif; font-size:.9rem; color:white; flex-shrink:0; }
.cli-info { flex:1; }
.cli-name { font-family:'Oswald',sans-serif; font-size:1rem; color:white; letter-spacing:.04em; }
.cli-sub { font-size:.78rem; color:rgba(255,255,255,.55); margin-top:2px; }

/* ─── SUMMARY ─── */
.summary-card { background:linear-gradient(135deg,#0b1c2e,#1255a1); border-radius:12px; padding:20px 22px; margin-bottom:18px; display:none; }
.summary-card.visible { display:block; }
.summary-title { font-family:'Oswald',sans-serif; font-size:1rem; color:white; letter-spacing:.06em; margin-bottom:14px; display:flex; align-items:center; gap:8px; }
.summary-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid rgba(255,255,255,.1); }
.summary-row:last-child { border-bottom:none; }
.summary-label { font-size:.78rem; color:rgba(255,255,255,.5); letter-spacing:.06em; text-transform:uppercase; }
.summary-value { font-size:.9rem; font-weight:600; color:white; text-align:right; }

/* ─── INSTRUCTION BANNER ─── */
.instruction-banner { background:#fff; border-left:4px solid var(--accent); border-radius:8px; padding:12px 16px; margin-bottom:18px; display:flex; align-items:flex-start; gap:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); }
.instruction-banner p { font-size:.88rem; color:var(--muted); line-height:1.55; }

/* ─── ACTIONS ─── */
.form-actions { display:flex; gap:12px; flex-wrap:wrap; justify-content:flex-end; margin-top:4px; }

/* ─── TOAST ─── */
.toast { position:fixed; bottom:24px; right:24px; z-index:9999; background:var(--navy); color:white; padding:12px 20px; border-radius:10px; font-size:.88rem; box-shadow:0 6px 20px rgba(0,0,0,.25); transform:translateX(120%); transition:transform .35s; }
.toast.show { transform:translateX(0); }
.toast.toast-ok   { background:var(--ok); }
.toast.toast-err  { background:var(--error); }
.toast.toast-warn { background:var(--warn); }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Operaciones · Turnos</div>
            <h1 class="page-title">Registrar Turno Presencial</h1>
            <p class="page-subtitle">Completá los datos para registrar un turno en el taller</p>
        </div>
        <a href="{{ route('admin.turnos.index') }}" class="btn-secondary-ta">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

{{-- Barra de pasos --}}
<div class="steps">
    <div class="step active" id="step1"><div class="step-dot">1</div><div class="step-label">Cliente</div></div>
    <div class="step" id="step2"><div class="step-dot">2</div><div class="step-label">Vehículo</div></div>
    <div class="step" id="step3"><div class="step-dot">3</div><div class="step-label">Servicio</div></div>
    <div class="step" id="step4"><div class="step-dot">4</div><div class="step-label">Agenda</div></div>
    <div class="step" id="step5"><div class="step-dot">✓</div><div class="step-label">Confirmar</div></div>
</div>

{{-- Banner informativo --}}
<div class="instruction-banner">
    <i class="bi bi-info-circle-fill" style="color:var(--accent); font-size:1.1rem; flex-shrink:0; margin-top:2px"></i>
    <p>
        <strong>Turno presencial:</strong> Buscá al cliente por DNI. Si ya tiene cuenta en el sistema sus datos
        se cargan automáticamente. Si es nuevo, completá sus datos y se creará la cuenta automáticamente.
        El turno queda <strong>confirmado</strong> de inmediato.
    </p>
</div>

<form method="POST" action="{{ route('admin.turnos.guardar') }}" id="mainForm" novalidate>
    @csrf
    <input type="hidden" name="es_presencial" value="1">
    <input type="hidden" name="fecha_hora_turno" id="fecha_hora_turno_input">
    <input type="hidden" name="marca_id"   id="marca_id_input">
    <input type="hidden" name="modelo_id"  id="modelo_id_input">

    {{-- SECCIÓN 1: DATOS DEL CLIENTE --}}
    <div class="form-section">
        <div class="section-header">
            <div class="section-icon"><i class="bi bi-person"></i></div>
            <span class="section-title">SECCIÓN 1 — DATOS DEL CLIENTE</span>
        </div>
        <div class="section-body">

            {{-- Buscador por DNI --}}
            <div style="display:flex; gap:10px; margin-bottom:16px">
                <div class="field-group" style="flex:1">
                    <label>Buscar por DNI</label>
                    <input type="text" id="buscarDni" class="field-input" placeholder="Ej: 35123456" maxlength="20"
                        oninput="this.value=this.value.replace(/\D/g,'')">
                </div>
                <div style="display:flex; align-items:flex-end">
                    <button type="button" class="btn-primary-ta" onclick="buscarCliente()" style="height:40px">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>
            </div>

            {{-- Chip si cliente encontrado --}}
            <div id="clienteEncontrado" style="display:none">
                <div class="cliente-chip">
                    <div class="cli-av" id="cli-av-initials"></div>
                    <div class="cli-info">
                        <div class="cli-name" id="cli-found-name"></div>
                        <div class="cli-sub" id="cli-found-sub"></div>
                    </div>
                    <button type="button" onclick="limpiarCliente()"
                        style="background:rgba(255,255,255,.15); border:none; color:white; border-radius:7px; padding:5px 10px; cursor:pointer; font-size:.8rem">
                        <i class="bi bi-x"></i> Cambiar
                    </button>
                </div>
            </div>

            {{-- Formulario de datos (nuevo cliente o edición) --}}
            <div id="clienteForm">
                <div class="field-grid" style="margin-top:14px">
                    <div class="field-group">
                        <label>Nombre <span class="req-star">*</span></label>
                        <input type="text" name="name" id="f_name" class="field-input" placeholder="Juan" required>
                        <span class="field-error" id="err-name"></span>
                    </div>
                    <div class="field-group">
                        <label>Apellido <span class="req-star">*</span></label>
                        <input type="text" name="apellido" id="f_apellido" class="field-input" placeholder="Pérez" required>
                        <span class="field-error" id="err-apellido"></span>
                    </div>
                    <div class="field-group">
                        <label>DNI <span class="req-star">*</span></label>
                        <input type="text" name="dni" id="f_dni" class="field-input" placeholder="35123456" required maxlength="20">
                        <span class="field-error" id="err-dni"></span>
                    </div>
                    <div class="field-group">
                        <label>Teléfono <span class="req-star">*</span></label>
                        <input type="text" name="telefono" id="f_telefono" class="field-input" placeholder="3751-000000" required>
                        <span class="field-error" id="err-tel"></span>
                    </div>
                    <div class="field-group field-full">
                        <label>Correo electrónico</label>
                        <input type="email" name="email" id="f_email" class="field-input" placeholder="correo@ejemplo.com">
                        <span class="field-hint">Si no tiene, se generará uno automáticamente</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 2: DATOS DEL VEHÍCULO --}}
    <div class="form-section">
        <div class="section-header">
            <div class="section-icon"><i class="bi bi-car-front"></i></div>
            <span class="section-title">SECCIÓN 2 — DATOS DEL VEHÍCULO</span>
        </div>
        <div class="section-body">
            <div class="field-grid">
                <div class="field-group">
                    <label>Marca <span class="req-star">*</span></label>
                    <select class="field-input" id="marca_select_form" onchange="cargarModelos(this.value)">
                        <option value="">— Seleccionar marca —</option>
                        @foreach($marcas as $marca)
                        <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                        @endforeach
                        <option value="otra">Otra (especificar)</option>
                    </select>
                    <input type="text" name="marca_nombre_custom" id="marca_custom" class="field-input"
                        placeholder="Escribí la marca..." style="display:none; margin-top:8px">
                    </select>
                    <span class="field-error" id="err-marca"></span>
                </div>
                <div class="field-group">
                    <label>Modelo <span class="req-star">*</span></label>
                    <select class="field-input" id="modelo_select_form" disabled>
                        <option value="">— Primero elegí la marca —</option>
                    <option value="otro">Otro (especificar)</option>
                </select>
                <input type="text" name="modelo_nombre_custom" id="modelo_custom" class="field-input"
                    placeholder="Escribí el modelo..." style="display:none; margin-top:8px">
                    </select>
                    <span class="field-error" id="err-modelo"></span>
                </div>
                <div class="field-group">
                    <label>Año <span class="req-star">*</span></label>
                    <input type="number" name="anio" id="anio_input" class="field-input"
                        placeholder="{{ date('Y') }}" min="1990" max="{{ date('Y') + 1 }}">
                    <span class="field-error" id="err-anio"></span>
                </div>
                <div class="field-group">
                    <label>Patente <span class="req-star">*</span></label>
                    <input type="text" name="patente" id="patente_input" class="field-input"
                        placeholder="ABC123" maxlength="8" style="text-transform:uppercase">
                    <span class="field-error" id="err-patente"></span>
                </div>
                <div class="field-group">
                    <label>Kilometraje</label>
                    <input type="number" name="kilometraje" id="km_input" class="field-input"
                        placeholder="Ej: 85000" min="0">
                    <span class="field-hint">Opcional</span>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 3: TIPO DE SERVICIO --}}
    <div class="form-section">
        <div class="section-header">
            <div class="section-icon"><i class="bi bi-tools"></i></div>
            <span class="section-title">SECCIÓN 3 — TIPO DE SERVICIO</span>
        </div>
        <div class="section-body">
            <div class="radio-group" id="serviceGroup">
                @foreach([
                    ['mantenimiento_preventivo','bi-gear','Mantenimiento Preventivo','Service, aceite, filtros'],
                    ['reparacion','bi-wrench-adjustable','Reparación','Corrección de fallas'],
                    ['diagnostico','bi-search','Diagnóstico','Revisión y detección'],
                    ['service','bi-check-all','Service Completo','Revisión integral'],
                    ['otros','bi-three-dots','Otros','Otro tipo de servicio'],
                ] as [$val,$icon,$titulo,$desc])
                <label class="radio-card">
                    <input type="radio" name="tipo_servicio" value="{{ $val }}">
                    <div class="radio-card-icon"><i class="bi {{ $icon }}"></i></div>
                    <div class="radio-card-text">
                        <strong>{{ $titulo }}</strong>
                        <span>{{ $desc }}</span>
                    </div>
                </label>
                @endforeach
            </div>
            <span class="field-error" id="err-servicio" style="margin-top:8px; display:flex"></span>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:16px">
                <div class="field-group">
                    <label>Mecánico asignado</label>
                    <select name="mecanico_id" class="field-input">
                        <option value="">Sin asignar</option>
                        @foreach($mecanicos as $m)
                        <option value="{{ $m->id }}">{{ $m->nombreCompleto() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field-group field-full" style="grid-column:1/-1">
                    <label>Observaciones</label>
                    <textarea name="observaciones" class="field-input" style="resize:vertical; min-height:70px"
                        placeholder="Notas sobre el servicio o problema reportado..."></textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 4: CALENDARIO --}}
    <div class="form-section">
        <div class="section-header">
            <div class="section-icon"><i class="bi bi-calendar3"></i></div>
            <span class="section-title">SECCIÓN 4 — FECHA Y HORARIO</span>
        </div>
        <div class="section-body" style="padding-bottom:0">
            <div class="calendar-wrap">
                <div class="cal-header">
                    <button type="button" class="cal-nav-btn" id="prevMonth">&#8249;</button>
                    <span class="cal-header-title" id="calTitle"></span>
                    <button type="button" class="cal-nav-btn" id="nextMonth">&#8250;</button>
                </div>
                <div class="cal-days-header">
                    <span>Dom</span><span>Lun</span><span>Mar</span>
                    <span>Mié</span><span>Jue</span><span>Vie</span><span>Sáb</span>
                </div>
                <div class="cal-grid" id="calGrid"></div>
                <div class="time-slots">
                    <div class="time-slots-title">Horarios disponibles</div>
                    <div class="slots-grid" id="slotsContainer">
                        <p class="time-placeholder">Seleccioná un día para ver los horarios.</p>
                    </div>
                </div>
            </div>
            <span class="field-error" id="err-fecha" style="margin-top:8px; display:flex; margin-bottom:4px"></span>
        </div>
    </div>

    {{-- RESUMEN --}}
    <div class="summary-card" id="summaryCard">
        <div class="summary-title"><i class="bi bi-star-fill" style="color:gold"></i> Resumen del Turno</div>
        <div class="summary-row"><span class="summary-label">Cliente</span><span class="summary-value" id="sum-cliente">—</span></div>
        <div class="summary-row"><span class="summary-label">Vehículo</span><span class="summary-value" id="sum-vehiculo">—</span></div>
        <div class="summary-row"><span class="summary-label">Servicio</span><span class="summary-value" id="sum-servicio">—</span></div>
        <div class="summary-row"><span class="summary-label">Fecha y Hora</span><span class="summary-value" id="sum-fecha">—</span></div>
        <div class="summary-row"><span class="summary-label">Mecánico</span><span class="summary-value" id="sum-mecanico">—</span></div>
    </div>

    {{-- ACCIONES --}}
    <div class="form-actions">
        <button type="button" class="btn-secondary-ta" id="btnLimpiar">
            <i class="bi bi-x-circle"></i> Limpiar
        </button>
        <button type="button" class="btn-primary-ta" id="btnVerificar">
            Verificar datos <i class="bi bi-arrow-right"></i>
        </button>
        <button type="submit" class="btn-ok-ta" id="btnConfirmar" style="display:none">
            <i class="bi bi-check-circle"></i> Confirmar Turno
        </button>
    </div>
</form>

<div class="toast" id="toast"></div>

@push('scripts')
<script>
const BUSINESS_HOURS = ['08:00','09:00','10:00','11:00','14:00','15:00','16:00','17:00'];
const CLOSED_DAYS = [0];
let calYear, calMonth, selectedDate = null, selectedSlot = null;
const today = new Date();

// Datos de clientes existentes para búsqueda
const clientes = {{ Js::from(\App\Models\User::where('rol','cliente')->where('activo',true)->get(['id','name','apellido','dni','telefono','email'])) }};
// ── CALENDAR ──────────────────────────────────────────────────────────
function initCalendar() {
    calYear = today.getFullYear();
    calMonth = today.getMonth();
    renderCalendar();
}

function renderCalendar() {
    const title = document.getElementById('calTitle');
    const grid  = document.getElementById('calGrid');
    const months = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    title.textContent = `${months[calMonth]} ${calYear}`;
    grid.innerHTML = '';

    const firstDay = new Date(calYear, calMonth, 1).getDay();
    const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();

    for (let i = 0; i < firstDay; i++) {
        const d = document.createElement('div');
        d.className = 'cal-day';
        grid.appendChild(d);
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const date = new Date(calYear, calMonth, d);
        const ds   = `${calYear}-${String(calMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const div  = document.createElement('div');
        div.textContent = d;

        const isPast   = date < new Date(today.getFullYear(), today.getMonth(), today.getDate());
        const isClosed = CLOSED_DAYS.includes(date.getDay());
        const isToday  = date.toDateString() === today.toDateString();
        const isSel    = ds === selectedDate;

        if (isPast || isClosed) {
            div.className = 'cal-day past';
        } else {
            div.className = 'cal-day available' + (isToday ? ' today' : '') + (isSel ? ' selected' : '');
            div.addEventListener('click', () => selectDate(ds));
        }
        grid.appendChild(div);
    }
}

function selectDate(ds) {
    selectedDate = ds;
    selectedSlot = null;
    document.getElementById('err-fecha').textContent = '';
    renderCalendar();
    renderSlots();
}

function renderSlots() {
    const container = document.getElementById('slotsContainer');
    container.innerHTML = '';
    BUSINESS_HOURS.forEach(h => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'slot-btn';
        btn.textContent = h;
        btn.addEventListener('click', () => {
            document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            selectedSlot = h;
            document.getElementById('err-fecha').textContent = '';
        });
        container.appendChild(btn);
    });
}

document.getElementById('prevMonth').addEventListener('click', () => {
    calMonth--; if (calMonth < 0) { calMonth = 11; calYear--; } renderCalendar();
});
document.getElementById('nextMonth').addEventListener('click', () => {
    calMonth++; if (calMonth > 11) { calMonth = 0; calYear++; } renderCalendar();
});

// ── BUSCAR CLIENTE ────────────────────────────────────────────────────
function buscarCliente() {
    const dni = document.getElementById('buscarDni').value.trim();
    if (!dni) { showToast('Ingresá un DNI para buscar.', 'warn'); return; }

    const found = clientes.find(c => c.dni === dni);
    if (found) {
        // Rellenar form con datos existentes
        document.getElementById('f_name').value    = found.name;
        document.getElementById('f_apellido').value = found.apellido;
        document.getElementById('f_dni').value     = found.dni;
        document.getElementById('f_telefono').value = found.telefono;
        document.getElementById('f_email').value   = found.email;

        // Marcar campos como readonly
        ['f_name','f_apellido','f_dni','f_telefono','f_email'].forEach(id => {
            document.getElementById(id).readOnly = true;
            document.getElementById(id).classList.add('valid');
        });

        // Mostrar chip
        const initials = (found.name[0] + found.apellido[0]).toUpperCase();
        document.getElementById('cli-av-initials').textContent = initials;
        document.getElementById('cli-found-name').textContent  = found.name + ' ' + found.apellido;
        document.getElementById('cli-found-sub').textContent   = 'DNI ' + found.dni + ' · ' + found.telefono;
        document.getElementById('clienteEncontrado').style.display = 'block';

        showToast('Cliente encontrado en el sistema.', 'ok');
    } else {
        // Cliente nuevo — limpiar y setear el DNI
        limpiarCliente();
        document.getElementById('f_dni').value = dni;
        document.getElementById('clienteEncontrado').style.display = 'none';
        showToast('Cliente no encontrado. Completá sus datos para registrarlo.', 'warn');
    }
}

function limpiarCliente() {
    ['f_name','f_apellido','f_dni','f_telefono','f_email'].forEach(id => {
        const el = document.getElementById(id);
        el.value = '';
        el.readOnly = false;
        el.classList.remove('valid');
    });
    document.getElementById('clienteEncontrado').style.display = 'none';
    document.getElementById('buscarDni').value = '';
}

// Enter en el buscador
document.getElementById('buscarDni').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); buscarCliente(); }
});

// ── MARCA / MODELO ────────────────────────────────────────────────────
function cargarModelos(marcaId) {
    const sel = document.getElementById('modelo_select_form');
    const marcaCustom = document.getElementById('marca_custom');

    // Si eligió "Otra" marca
    if (marcaId === 'otra') {
        marcaCustom.style.display = 'block';
        marcaCustom.required = true;
        document.getElementById('marca_id_input').value = '';
        // Mostrar modelo como texto libre también
        sel.innerHTML = '<option value="otro">Otro (especificar)</option>';
        sel.disabled = false;
        document.getElementById('modelo_custom').style.display = 'block';
        document.getElementById('modelo_custom').required = true;
        document.getElementById('modelo_id_input').value = '';
        return;
    } else {
        marcaCustom.style.display = 'none';
        marcaCustom.required = false;
    }

    document.getElementById('marca_id_input').value = marcaId;
    sel.innerHTML = '<option>Cargando...</option>';
    sel.disabled = true;

    if (!marcaId) {
        sel.innerHTML = '<option value="">— Primero elegí la marca —</option>';
        return;
    }

    fetch(`/api/marcas/${marcaId}/modelos`)
        .then(r => r.json())
        .then(modelos => {
            sel.innerHTML = '<option value="">— Seleccionar modelo —</option>';
            modelos.forEach(m => {
                const o = document.createElement('option');
                o.value = m.id; o.textContent = m.nombre;
                sel.appendChild(o);
            });
            sel.appendChild(new Option('Otro (especificar)', 'otro'));
            sel.disabled = false;
        });
}

document.getElementById('modelo_select_form').addEventListener('change', function() {
    const modeloCustom = document.getElementById('modelo_custom');
    if (this.value === 'otro') {
        modeloCustom.style.display = 'block';
        modeloCustom.required = true;
        document.getElementById('modelo_id_input').value = '';
    } else {
        modeloCustom.style.display = 'none';
        modeloCustom.required = false;
        document.getElementById('modelo_id_input').value = this.value;
    }
});

// ── SERVICE RADIO CARDS ───────────────────────────────────────────────
document.querySelectorAll('.radio-card input[type="radio"]').forEach(r => {
    r.addEventListener('change', () => {
        document.querySelectorAll('.radio-card').forEach(c => c.classList.remove('selected'));
        r.closest('.radio-card').classList.add('selected');
        document.getElementById('err-servicio').textContent = '';
    });
});

// ── VALIDACIÓN ────────────────────────────────────────────────────────
function validateAll() {
    let ok = true;

    if (!document.getElementById('marca_select_form').value) {
    document.getElementById('err-marca').textContent = 'Seleccioná la marca.'; ok = false;
    }
    if (!document.getElementById('modelo_select_form').value &&
        !document.getElementById('modelo_custom').value) {
        document.getElementById('err-modelo').textContent = 'Seleccioná el modelo.'; ok = false;
    }
    if (!document.getElementById('f_name').value.trim()) {
        document.getElementById('err-name').textContent = 'Requerido.'; ok = false;
    }
    if (!document.getElementById('f_apellido').value.trim()) {
        document.getElementById('err-apellido').textContent = 'Requerido.'; ok = false;
    }
    if (!document.getElementById('f_dni').value.trim()) {
        document.getElementById('err-dni').textContent = 'Requerido.'; ok = false;
    }
    if (!document.getElementById('f_telefono').value.trim()) {
        document.getElementById('err-tel').textContent = 'Requerido.'; ok = false;
    }
    if (!document.getElementById('marca_select_form').value) {
        document.getElementById('err-marca').textContent = 'Seleccioná la marca.'; ok = false;
    }
    if (!document.getElementById('modelo_select_form').value) {
        document.getElementById('err-modelo').textContent = 'Seleccioná el modelo.'; ok = false;
    }
    if (!document.getElementById('anio_input').value) {
        document.getElementById('err-anio').textContent = 'Ingresá el año.'; ok = false;
    }
    if (!document.getElementById('patente_input').value) {
        document.getElementById('err-patente').textContent = 'Ingresá la patente.'; ok = false;
    }
    if (!document.querySelector('input[name="tipo_servicio"]:checked')) {
        document.getElementById('err-servicio').textContent = 'Seleccioná el tipo de servicio.'; ok = false;
    }
    if (!selectedDate) {
        document.getElementById('err-fecha').textContent = 'Seleccioná un día.'; ok = false;
    } else if (!selectedSlot) {
        document.getElementById('err-fecha').textContent = 'Seleccioná un horario.'; ok = false;
    }

    return ok;
}

// ── VERIFICAR ─────────────────────────────────────────────────────────
document.getElementById('btnVerificar').addEventListener('click', () => {
    if (!validateAll()) {
        showToast('Completá todos los campos requeridos.', 'err');
        return;
    }

    // Setear fecha/hora hidden
    document.getElementById('fecha_hora_turno_input').value = `${selectedDate}T${selectedSlot}`;

    // Setear marca/modelo hidden
    const marcaVal = document.getElementById('marca_select_form').value;
    if (marcaVal !== 'otra') {
        document.getElementById('marca_id_input').value = marcaVal;
    }
    const modeloVal = document.getElementById('modelo_select_form').value;
    if (modeloVal !== 'otro') {
        document.getElementById('modelo_id_input').value = modeloVal;
    }

    // Campos del cliente como hidden (necesario para campos readonly)
    setHidden('name',      document.getElementById('f_name').value);
    setHidden('apellido',  document.getElementById('f_apellido').value);
    setHidden('dni',       document.getElementById('f_dni').value);
    setHidden('telefono',  document.getElementById('f_telefono').value);
    setHidden('email',     document.getElementById('f_email').value);

    // Vehículo
    setHidden('anio',        document.getElementById('anio_input').value);
    setHidden('patente',     document.getElementById('patente_input').value.toUpperCase());
    setHidden('kilometraje', document.getElementById('km_input').value || '0');

    // Construir resumen
    const marcaOpt = document.getElementById('marca_select_form');
    const marcaTxt = marcaOpt.value === 'otra'
        ? document.getElementById('marca_custom').value
        : marcaOpt.options[marcaOpt.selectedIndex]?.text || '';

    const modeloOpt = document.getElementById('modelo_select_form');
    const modeloTxt = modeloOpt.value === 'otro'
        ? document.getElementById('modelo_custom').value
        : modeloOpt.options[modeloOpt.selectedIndex]?.text || '';

    const srv    = document.querySelector('input[name="tipo_servicio"]:checked')?.value.replace(/_/g,' ') || '';
    const mecEl  = document.querySelector('select[name="mecanico_id"]');
    const mecTxt = mecEl.options[mecEl.selectedIndex]?.text || 'Sin asignar';
    const [yr,mo,da] = selectedDate.split('-');
    const months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

    document.getElementById('sum-cliente').textContent  = document.getElementById('f_name').value + ' ' + document.getElementById('f_apellido').value;
    document.getElementById('sum-vehiculo').textContent = `${marcaTxt} ${modeloTxt} · ${document.getElementById('patente_input').value.toUpperCase()}`;
    document.getElementById('sum-servicio').textContent = srv.replace(/\b\w/g, c => c.toUpperCase());
    document.getElementById('sum-fecha').textContent    = `${da} ${months[parseInt(mo)-1]} ${yr} a las ${selectedSlot} hs`;
    document.getElementById('sum-mecanico').textContent = mecTxt;

    document.getElementById('summaryCard').classList.add('visible');
    document.getElementById('btnConfirmar').style.display = 'flex';
    document.getElementById('btnVerificar').style.display = 'none';
    document.getElementById('step5').classList.add('active');
    document.getElementById('summaryCard').scrollIntoView({ behavior:'smooth', block:'start' });
    showToast('Revisá el resumen y confirmá el turno.', 'warn');
});

function setHidden(name, value) {
    let el = document.querySelector(`input[name="${name}"]`);
    if (!el) {
        el = document.createElement('input');
        el.type = 'hidden';
        el.name = name;
        document.getElementById('mainForm').appendChild(el);
    }
    el.value = value;
}

// ── LIMPIAR ───────────────────────────────────────────────────────────
document.getElementById('btnLimpiar').addEventListener('click', () => {
    limpiarCliente();
    document.getElementById('modelo_select_form').innerHTML = '<option value="">— Primero elegí la marca —</option>';
    document.getElementById('modelo_select_form').disabled = true;
    document.getElementById('marca_select_form').value = '';
    document.getElementById('anio_input').value = '';
    document.getElementById('patente_input').value = '';
    document.getElementById('km_input').value = '';
    document.querySelectorAll('.radio-card').forEach(c => c.classList.remove('selected'));
    document.querySelectorAll('input[name="tipo_servicio"]').forEach(r => r.checked = false);
    document.querySelectorAll('.field-error').forEach(e => e.textContent = '');
    document.getElementById('summaryCard').classList.remove('visible');
    document.getElementById('btnConfirmar').style.display = 'none';
    document.getElementById('btnVerificar').style.display = 'flex';
    selectedDate = null; selectedSlot = null;
    document.getElementById('slotsContainer').innerHTML = '<p class="time-placeholder">Seleccioná un día para ver los horarios.</p>';
    renderCalendar();
    [1,2,3,4,5].forEach(n => { const s = document.getElementById('step'+n); s.classList.remove('active','done'); });
    document.getElementById('step1').classList.add('active');
    showToast('Formulario limpiado.', 'ok');
});

// ── TOAST ─────────────────────────────────────────────────────────────
let toastTimer;
function showToast(msg, type = '') {
    const t = document.getElementById('toast');
    clearTimeout(toastTimer);
    t.className = 'toast show' + (type ? ' toast-' + type : '');
    t.textContent = msg;
    toastTimer = setTimeout(() => t.classList.remove('show'), 3400);
}

// ── INIT ──────────────────────────────────────────────────────────────
initCalendar();
</script>
@endpush
@endsection