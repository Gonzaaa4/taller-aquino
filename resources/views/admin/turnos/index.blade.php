@extends('layouts.app')
@section('title', 'Turnos')
@section('topbar-title', 'Gestión de Turnos')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Operaciones</div>
            <h1 class="page-title">Gestión de Turnos</h1>
            <p class="page-subtitle">Todos los turnos del sistema</p>
        </div>
        <div style="display:flex; gap:10px">
            <a href="{{ route('admin.turnos.agenda') }}" class="btn-secondary-ta">
                <i class="bi bi-calendar-week"></i> Ver Agenda
            </a>
            <a href="{{ route('admin.turnos.solicitar') }}" class="btn-primary-ta">
                <i class="bi bi-plus-circle"></i> Nuevo Turno
            </a>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="ta-card" style="margin-bottom:20px">
    <div class="ta-card-body" style="padding:14px 20px">
        <form style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end">
            <div>
                <label class="ta-label">Estado</label>
                <select name="estado" class="ta-input ta-select" style="width:180px">
                    <option value="">Todos los estados</option>
                    @foreach(['pendiente','confirmado','en_proceso','finalizado','cancelado'] as $e)
                        <option value="{{ $e }}" {{ request('estado') === $e ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_',' ',$e)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="ta-label">Fecha</label>
                <input type="date" name="fecha" class="ta-input" style="width:180px" value="{{ request('fecha') }}">
            </div>
            <div style="display:flex; gap:8px; align-items:flex-end">
                <button type="submit" class="btn-primary-ta" style="height:40px">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <a href="{{ route('admin.turnos.index') }}" class="btn-secondary-ta" style="height:40px">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="ta-card">
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>N° Seguimiento</th>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th>Fecha / Hora</th>
                    <th>Servicio</th>
                    <th>Mecánico</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($turnos as $turno)
                <tr>
                    <td>
                        <span class="nro-seguimiento" style="font-size:.95rem">
                            {{ $turno->numero_seguimiento }}
                        
                    </td>
                    <td>
                        <div style="font-weight:600; font-size:.88rem; color:var(--navy)">
                            {{ $turno->cliente->nombreCompleto() }}
                        </div>
                        <div style="font-size:.75rem; color:var(--muted)">{{ $turno->cliente->telefono }}</div>
                    </td>
                    <td>
                        <div style="font-size:.86rem; color:var(--navy)">
                            {{ $turno->vehiculo->marca->nombre }} {{ $turno->vehiculo->modelo->nombre }}
                        </div>
                        <div style="font-family:'Oswald',sans-serif; font-size:.76rem; color:var(--accent); letter-spacing:.06em">
                            {{ $turno->vehiculo->patente }}
                        </div>
                    </td>
                    <td>
                        <div style="font-size:.86rem; color:var(--navy)">{{ $turno->fecha_hora_turno->format('d/m/Y') }}</div>
                        <div style="font-size:.74rem; color:var(--muted)">{{ $turno->fecha_hora_turno->format('H:i') }} hs</div>
                    </td>
                    <td style="font-size:.84rem; color:var(--muted)">
                        {{ ucfirst(str_replace('_',' ',$turno->tipo_servicio)) }}
                    </td>
                    <td>
                        @if($turno->mecanico)
                            <div style="font-size:.84rem; color:var(--navy)">{{ $turno->mecanico->name }}</div>
                        @else
                            <span style="font-size:.78rem; color:var(--muted); font-style:italic">Sin asignar
                        @endif
                    </td>
                    <td>
                        <span class="ta-badge badge-{{ $turno->estado }}">{{ $turno->etiquetaEstado() }}
                    </td>
                    <td>
                        <div style="display:flex; gap:6px">
                            <a href="{{ route('admin.turnos.show', $turno) }}"
                               class="btn-secondary-ta" style="padding:6px 12px; font-size:.8rem">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($turno->estaPendiente())
                            <button type="button" class="btn-ok-ta" style="padding:6px 12px; font-size:.8rem"
                                onclick="abrirConfirmar({{ $turno->id }}, '{{ $turno->numero_seguimiento }}')">
                                <i class="bi bi-check-circle"></i>
                            </button>
                            @endif
                            @if($turno->puedeSerCancelado())
                            <button type="button" class="btn-ok-ta" style="padding:6px 12px; font-size:.8rem"
                                onclick="abrirConfirmar({{ $turno->id }}, '{{ $turno->numero_seguimiento }}', '{{ $turno->cliente->nombreCompleto() }}', '{{ $turno->vehiculo->marca->nombre }} {{ $turno->vehiculo->modelo->nombre }} ({{ $turno->vehiculo->patente }})')">
                                <i class="bi bi-check-circle"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:56px; color:var(--muted)">
                        <i class="bi bi-calendar-x" style="font-size:2.5rem; display:block; margin-bottom:14px; opacity:.3"></i>
                        No hay turnos para mostrar
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($turnos->hasPages())
    <div style="padding:14px 20px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px">
        <div style="font-size:.82rem; color:var(--muted)">
            Mostrando {{ $turnos->firstItem() }}–{{ $turnos->lastItem() }} de {{ $turnos->total() }} turnos
        </div>
        <div style="display:flex; gap:6px">
            @if($turnos->onFirstPage())
            <span style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--muted); font-size:.84rem; background:var(--card)">‹ Anterior</span>
            @else
            <a href="{{ $turnos->previousPageUrl() }}" style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--text); font-size:.84rem; background:white; text-decoration:none">‹ Anterior</a>
            @endif

            @foreach($turnos->getUrlRange(1, $turnos->lastPage()) as $page => $url)
            @if($page == $turnos->currentPage())
            <span style="padding:6px 12px; border-radius:7px; background:var(--blue); color:white; font-size:.84rem; font-weight:600">{{ $page }}</span>
            @else
            <a href="{{ $url }}" style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--text); font-size:.84rem; background:white; text-decoration:none">{{ $page }}</a>
            @endif
            @endforeach

            @if($turnos->hasMorePages())
            <a href="{{ $turnos->nextPageUrl() }}" style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--text); font-size:.84rem; background:white; text-decoration:none">Siguiente ›</a>
            @else
            <span style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--muted); font-size:.84rem; background:var(--card)">Siguiente ›</span>
            @endif
        </div>
    </div>
    @endif
</div>
{{-- Modal confirmar turno + asignar mecánico --}}
<div id="modalConfirmar" style="display:none; position:fixed; inset:0; background:rgba(11,28,46,.65); z-index:500; align-items:center; justify-content:center; padding:20px">
    <div style="background:white; border-radius:14px; width:100%; max-width:480px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.3)">
        <div style="padding:18px 22px; border-bottom:1px solid var(--border); background:var(--light); display:flex; justify-content:space-between; align-items:center">
            <div style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--navy); letter-spacing:.04em">
                <i class="bi bi-check-circle" style="color:var(--ok); margin-right:8px"></i>CONFIRMAR TURNO
            </div>
            <button type="button" onclick="document.getElementById('modalConfirmar').style.display='none'"
                style="background:none; border:none; font-size:1.3rem; cursor:pointer; color:var(--muted)">×</button>
        </div>
        <form id="formConfirmar" method="POST">
            @csrf
            <div style="padding:22px; display:flex; flex-direction:column; gap:14px">
                <div style="font-size:.88rem; color:var(--muted)">
                    Turno <strong id="conf-nro" style="font-family:'Oswald',sans-serif; color:var(--accent)"></strong>
                </div>

                <div>
                    <label class="ta-label">Asignar mecánico</label>
                    <select name="mecanico_id" id="conf-mecanico" class="ta-input ta-select">
                        <option value="">— Sin asignar —</option>
                        @foreach(\App\Models\User::where('rol','mecanico')->where('activo',true)
                            ->withCount(['trabajosComoMecanico' => fn($q) => $q->whereIn('estado',['en_proceso','pendiente'])])
                            ->orderBy('trabajos_como_mecanico_count')
                            ->get() as $m)
                        <option value="{{ $m->id }}">
                            {{ $m->nombreCompleto() }}
                            ({{ $m->trabajos_como_mecanico_count }} trabajos activos)
                        </option>
                        @endforeach
                    </select>
                    <div style="font-size:.76rem; color:var(--muted); margin-top:4px">
                        <i class="bi bi-info-circle" style="color:var(--blue)"></i>
                        Los mecánicos están ordenados por menor carga de trabajo.
                    </div>
                </div>
            </div>
            <div style="padding:14px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px">
                <button type="button" class="btn-secondary-ta"
                    onclick="document.getElementById('modalConfirmar').style.display='none'">Cancelar</button>
                <button type="submit" class="btn-ok-ta">
                    <i class="bi bi-check-circle"></i> Confirmar Turno
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal confirmar turno --}}
<div id="modalConfirmar" style="display:none; position:fixed; inset:0; background:rgba(11,28,46,.65); z-index:500; align-items:center; justify-content:center; padding:20px">
    <div style="background:white; border-radius:14px; width:100%; max-width:500px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.3)">
        <div style="padding:18px 22px; border-bottom:1px solid var(--border); background:var(--light); display:flex; justify-content:space-between; align-items:center">
            <div style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--navy); letter-spacing:.04em">
                <i class="bi bi-check-circle" style="color:var(--ok); margin-right:8px"></i>CONFIRMAR TURNO
            </div>
            <button type="button" onclick="document.getElementById('modalConfirmar').style.display='none'"
                style="background:none; border:none; font-size:1.3rem; cursor:pointer; color:var(--muted)">×</button>
        </div>
        <form id="formConfirmar" method="POST">
            @csrf
            <div style="padding:22px; display:flex; flex-direction:column; gap:16px">

                {{-- Info del turno --}}
                <div style="background:var(--card); border:1px solid var(--border); border-radius:10px; padding:14px 16px">
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:8px">Turno a confirmar</div>
                    <div style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--accent); letter-spacing:.08em; margin-bottom:4px" id="conf-nro"></div>
                    <div style="font-size:.88rem; font-weight:600; color:var(--navy)" id="conf-cliente"></div>
                    <div style="font-size:.82rem; color:var(--muted)" id="conf-vehiculo"></div>
                </div>

                {{-- Asignar mecánico --}}
                <div>
                    <label class="ta-label">
                        Asignar mecánico
                        <span style="font-weight:400; text-transform:none; letter-spacing:0; color:var(--muted); font-size:.76rem">(opcional)</span>
                    </label>
                    <select name="mecanico_id" class="ta-input ta-select">
                        <option value="">— Sin asignar —</option>
                        @foreach(\App\Models\User::where('rol','mecanico')
                            ->where('activo', true)
                            ->withCount(['trabajosComoMecanico as trabajos_activos' => fn($q) =>
                                $q->whereIn('estado',['en_proceso','pendiente'])
                            ])
                            ->orderBy('trabajos_activos')
                            ->get() as $m)
                        <option value="{{ $m->id }}">
                            {{ $m->nombreCompleto() }}
                            — {{ $m->trabajos_activos }} trabajo(s) activo(s)
                        </option>
                        @endforeach
                    </select>
                    <div style="font-size:.75rem; color:var(--muted); margin-top:4px">
                        <i class="bi bi-sort-numeric-up" style="color:var(--blue)"></i>
                        Ordenados por menor carga de trabajo actual
                    </div>
                </div>
            </div>

            <div style="padding:14px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px; background:var(--card)">
                <button type="button" class="btn-secondary-ta"
                    onclick="document.getElementById('modalConfirmar').style.display='none'">
                    Cancelar
                </button>
                <button type="submit" class="btn-ok-ta">
                    <i class="bi bi-check-circle"></i> Confirmar Turno
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')

<script>
function abrirConfirmar(id, nro, cliente, vehiculo) {
    document.getElementById('conf-nro').textContent     = nro;
    document.getElementById('conf-cliente').textContent = cliente;
    document.getElementById('conf-vehiculo').textContent = vehiculo;
    document.getElementById('formConfirmar').action     = `/admin/turnos/${id}/confirmar`;
    document.getElementById('modalConfirmar').style.display = 'flex';
}
function abrirConfirmar(turnoId, nro) {
    document.getElementById('conf-nro').textContent = nro;
    document.getElementById('formConfirmar').action = `/admin/turnos/${turnoId}/confirmar`;
    document.getElementById('modalConfirmar').style.display = 'flex';
}
</script>
@endpush
@endsection
