@extends('layouts.app')
@section('title', 'Órdenes de Trabajo')
@section('topbar-title', 'Órdenes de Trabajo')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Operaciones</div>
            <h1 class="page-title">Órdenes de Trabajo</h1>
            <p class="page-subtitle">Vehículos ingresados al taller</p>
        </div>
        <button class="btn-primary-ta" onclick="document.getElementById('modalIngreso').style.display='flex'">
            <i class="bi bi-box-arrow-in-right"></i> Registrar Ingreso
        </button>
    </div>
</div>

{{-- Filtros --}}
<div class="ta-card" style="margin-bottom:20px">
    <div class="ta-card-body" style="padding:14px 20px">
        <form style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end">
            <div>
                <label class="ta-label">Estado</label>
                <select name="estado" class="ta-input ta-select" style="width:200px">
                    <option value="">Activos (sin entregados)</option>
                    <option value="ingresado"  {{ request('estado') === 'ingresado' ? 'selected' : '' }}>Ingresado</option>
                    <option value="en_proceso" {{ request('estado') === 'en_proceso' ? 'selected' : '' }}>En proceso</option>
                    <option value="finalizado" {{ request('estado') === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                    <option value="entregado"  {{ request('estado') === 'entregado' ? 'selected' : '' }}>Entregado</option>
                </select>
            </div>
            <div style="display:flex; gap:8px; align-items:flex-end">
                <button type="submit" class="btn-primary-ta" style="height:40px">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <a href="{{ route('admin.trabajos.index') }}" class="btn-secondary-ta" style="height:40px">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="ta-card">
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>Vehículo</th>
                    <th>Cliente</th>
                    <th>Ingreso</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ingresos as $ingreso)
                <tr>
                    <td>
                        <div style="font-weight:600; color:var(--navy)">
                            {{ $ingreso->vehiculo->marca->nombre }} {{ $ingreso->vehiculo->modelo->nombre }}
                        </div>
                        <div style="font-family:'Oswald',sans-serif; font-size:.8rem; color:var(--accent); letter-spacing:.06em">
                            {{ $ingreso->vehiculo->patente }}
                        </div>
                    </td>
                    <td>
                        <div style="font-size:.88rem; font-weight:600; color:var(--navy)">{{ $ingreso->cliente->nombreCompleto() }}</div>
                        <div style="font-size:.76rem; color:var(--muted)">{{ $ingreso->cliente->telefono }}</div>
                    </td>
                    <td>
                        <div style="font-size:.86rem; color:var(--navy)">{{ $ingreso->fecha_ingreso->format('d/m/Y') }}</div>
                        <div style="font-size:.74rem; color:var(--muted)">{{ $ingreso->fecha_ingreso->format('H:i') }} hs</div>
                    </td>
                    <td style="font-size:.84rem; color:var(--muted); max-width:200px">
                        {{ Str::limit($ingreso->descripcion_problema ?? '—', 50) }}
                    </td>
                    <td>
                        <span class="ta-badge badge-{{ str_replace('_','-',$ingreso->estado) }}">
                            {{ $ingreso->etiquetaEstado() }}
                        
                    </td>
                    <td>
                        <a href="{{ route('admin.trabajos.show', $ingreso) }}" class="btn-secondary-ta" style="padding:7px 14px; font-size:.82rem">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:48px; color:var(--muted)">
                        <i class="bi bi-wrench-adjustable" style="font-size:2rem; display:block; margin-bottom:12px; opacity:.3"></i>
                        No hay vehículos ingresados actualmente
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($ingresos->hasPages())
    <div style="padding:14px 20px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px">
        <div style="font-size:.82rem; color:var(--muted)">
            Mostrando {{ $ingresos->firstItem() }}–{{ $ingresos->lastItem() }} de {{ $ingresos->total() }} registros
        </div>
        <div style="display:flex; gap:6px">
            @if($ingresos->onFirstPage())
            <span style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--muted); font-size:.84rem; background:var(--card)">‹ Anterior</span>
            @else
            <a href="{{ $ingresos->previousPageUrl() }}" style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--text); font-size:.84rem; background:white; text-decoration:none">‹ Anterior</a>
            @endif

            @foreach($ingresos->getUrlRange(1, $ingresos->lastPage()) as $page => $url)
            @if($page == $ingresos->currentPage())
            <span style="padding:6px 12px; border-radius:7px; background:var(--blue); color:white; font-size:.84rem; font-weight:600">{{ $page }}</span>
            @else
            <a href="{{ $url }}" style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--text); font-size:.84rem; background:white; text-decoration:none">{{ $page }}</a>
            @endif
            @endforeach

            @if($ingresos->hasMorePages())
            <a href="{{ $ingresos->nextPageUrl() }}" style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--text); font-size:.84rem; background:white; text-decoration:none">Siguiente ›</a>
            @else
            <span style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--muted); font-size:.84rem; background:var(--card)">Siguiente ›</span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Modal registrar ingreso --}}
<div id="modalIngreso" style="display:none; position:fixed; inset:0; background:rgba(11,28,46,.65); z-index:500; align-items:center; justify-content:center; padding:20px">
    <div style="background:white; border-radius:14px; width:100%; max-width:540px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.3)">

        <div style="padding:18px 22px; border-bottom:1px solid var(--border); background:var(--light); display:flex; justify-content:space-between; align-items:center">
            <div style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--navy); letter-spacing:.04em">
                <i class="bi bi-box-arrow-in-right" style="color:var(--blue); margin-right:8px"></i>REGISTRAR INGRESO DE VEHÍCULO
            </div>
            <button type="button" onclick="cerrarModalIngreso()"
                style="background:none; border:none; font-size:1.3rem; cursor:pointer; color:var(--muted); line-height:1">×</button>
        </div>

        <form method="POST" action="{{ route('admin.trabajos.ingreso') }}" id="formIngreso">
            @csrf
            {{-- Campos hidden que se llenan automáticamente --}}
            <input type="hidden" name="cliente_id"          id="h_cliente_id">
            <input type="hidden" name="vehiculo_id"         id="h_vehiculo_id">
            <input type="hidden" name="kilometraje_ingreso" id="h_km">
            <input type="hidden" name="descripcion_problema" id="h_obs">

            <div style="padding:22px; display:flex; flex-direction:column; gap:16px">

                {{-- Solo selector de turno --}}
                <div>
                    <label class="ta-label">Turno asociado <span class="req">*</span></label>
                    <select name="turno_id" id="turnoSelectModal" class="ta-input ta-select"
                        onchange="cargarTurno(this.value)" required>
                        <option value="">— Seleccioná un turno pendiente —</option>
                        @foreach(\App\Models\Turno::whereIn('estado',['pendiente','confirmado'])
                            ->with(['cliente','vehiculo.marca','vehiculo.modelo'])
                            ->orderBy('fecha_hora_turno')->get() as $t)
                        <option value="{{ $t->id }}"
                            data-cliente-id="{{ $t->cliente_id }}"
                            data-vehiculo-id="{{ $t->vehiculo_id }}"
                            data-cliente="{{ $t->cliente->nombreCompleto() }}"
                            data-telefono="{{ $t->cliente->telefono }}"
                            data-vehiculo="{{ $t->vehiculo->marca->nombre }} {{ $t->vehiculo->modelo->nombre }} {{ $t->vehiculo->anio }}"
                            data-patente="{{ $t->vehiculo->patente }}"
                            data-km="{{ $t->vehiculo->kilometraje }}"
                            data-servicio="{{ ucfirst(str_replace('_',' ',$t->tipo_servicio)) }}"
                            data-obs="{{ $t->observaciones ?? '' }}">
                            {{ $t->numero_seguimiento }}
                            — {{ $t->cliente->nombreCompleto() }}
                            — {{ $t->vehiculo->marca->nombre }} {{ $t->vehiculo->modelo->nombre }} ({{ $t->vehiculo->patente }})
                            — {{ $t->fecha_hora_turno->format('d/m/Y H:i') }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Datos cargados automáticamente (solo lectura) --}}
                <div id="datosAuto" style="display:none; background:var(--card); border:1px solid var(--border); border-radius:10px; overflow:hidden">

                    {{-- Chip del vehículo --}}
                    <div style="background:linear-gradient(135deg,#0b1c2e,#1255a1); padding:14px 18px; display:flex; align-items:center; gap:14px">
                        <div style="width:40px; height:40px; border-radius:9px; background:rgba(255,255,255,.12); display:flex; align-items:center; justify-content:center; flex-shrink:0">
                            <i class="bi bi-car-front" style="color:white; font-size:1.2rem"></i>
                        </div>
                        <div style="flex:1">
                            <div id="da_vehiculo" style="font-family:'Oswald',sans-serif; font-size:1rem; color:white; letter-spacing:.04em"></div>
                            <div style="display:flex; gap:6px; margin-top:4px; flex-wrap:wrap">
                                <span id="da_patente" style="background:rgba(255,255,255,.2); color:white; font-size:.72rem; padding:2px 9px; border-radius:20px; font-weight:700; letter-spacing:.06em"></span>
                                <span id="da_servicio" style="background:rgba(255,255,255,.12); color:rgba(255,255,255,.85); font-size:.72rem; padding:2px 9px; border-radius:20px"></span>
                            </div>
                        </div>
                        <div style="text-align:right; flex-shrink:0">
                            <div style="font-size:.65rem; color:rgba(255,255,255,.4); text-transform:uppercase; letter-spacing:.07em; margin-bottom:2px">Cliente</div>
                            <div id="da_cliente" style="font-size:.9rem; font-weight:600; color:white"></div>
                            <div id="da_telefono" style="font-size:.76rem; color:rgba(255,255,255,.55)"></div>
                        </div>
                    </div>

                    {{-- Filas de datos --}}
                    <div style="padding:14px 18px; display:grid; grid-template-columns:1fr 1fr; gap:12px">
                        <div>
                            <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:3px">Kilometraje registrado</div>
                            <div id="da_km" style="font-weight:600; color:var(--navy)"></div>
                        </div>
                        <div id="da_obs_wrap" style="display:none">
                            <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:3px">Observaciones del cliente</div>
                            <div id="da_obs" style="font-size:.86rem; color:var(--text)"></div>
                        </div>
                    </div>

                    <div style="padding:10px 18px; border-top:1px solid var(--border); background:rgba(46,141,255,.05)">
                        <div style="font-size:.78rem; color:var(--muted)">
                            <i class="bi bi-info-circle" style="color:var(--blue); margin-right:5px"></i>
                            Todos los datos se cargan automáticamente desde el turno seleccionado.
                        </div>
                    </div>
                </div>

                {{-- Sin turnos disponibles --}}
                @if(\App\Models\Turno::whereIn('estado',['pendiente','confirmado'])->count() === 0)
                <div class="ta-alert info" style="margin:0">
                    <span class="ta-alert-icon"><i class="bi bi-info-circle-fill"></i></span>
                    <div>No hay turnos pendientes o confirmados. Primero confirmá un turno desde la sección Turnos.</div>
                </div>
                @endif
            </div>

            <div style="padding:14px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px; background:var(--card)">
                <button type="button" class="btn-secondary-ta" onclick="cerrarModalIngreso()">Cancelar</button>
                <button type="submit" class="btn-primary-ta" id="btnIngreso" disabled>
                    <i class="bi bi-check-circle"></i> Registrar Ingreso
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function cargarTurno(turnoId) {
    const btn   = document.getElementById('btnIngreso');
    const datos = document.getElementById('datosAuto');

    if (!turnoId) {
        btn.disabled = true;
        datos.style.display = 'none';
        return;
    }

    const opt = document.querySelector(`#turnoSelectModal option[value="${turnoId}"]`);
    if (!opt) return;

    // Llenar hidden inputs
    document.getElementById('h_cliente_id').value = opt.dataset.clienteId;
    document.getElementById('h_vehiculo_id').value = opt.dataset.vehiculoId;
    document.getElementById('h_km').value          = opt.dataset.km;
    document.getElementById('h_obs').value         = opt.dataset.obs;

    // Mostrar datos
    document.getElementById('da_vehiculo').textContent = opt.dataset.vehiculo;
    document.getElementById('da_patente').textContent  = opt.dataset.patente;
    document.getElementById('da_servicio').textContent = opt.dataset.servicio;
    document.getElementById('da_cliente').textContent  = opt.dataset.cliente;
    document.getElementById('da_telefono').textContent = opt.dataset.telefono;
    document.getElementById('da_km').textContent       = Number(opt.dataset.km).toLocaleString('es-AR') + ' km';

    const obs = opt.dataset.obs?.trim();
    const obsWrap = document.getElementById('da_obs_wrap');
    if (obs) {
        document.getElementById('da_obs').textContent = obs;
        obsWrap.style.display = 'block';
    } else {
        obsWrap.style.display = 'none';
    }

    datos.style.display = 'block';
    btn.disabled = false;
}

function cerrarModalIngreso() {
    document.getElementById('modalIngreso').style.display = 'none';
    document.getElementById('turnoSelectModal').value = '';
    document.getElementById('datosAuto').style.display = 'none';
    document.getElementById('btnIngreso').disabled = true;
}
</script>
@endpush
@endsection
