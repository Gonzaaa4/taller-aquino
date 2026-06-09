@extends('layouts.app')
@section('title', 'Perfil Mecánico')
@section('topbar-title', 'Perfil de Mecánico')

@push('styles')
<style>
.rrhh-section { background:#fff; border-radius:12px; margin-bottom:18px; box-shadow:0 2px 10px rgba(0,0,0,.06); overflow:hidden; }
.rrhh-header { background:var(--light); border-bottom:1px solid var(--border); padding:12px 20px; display:flex; align-items:center; gap:10px; }
.rrhh-icon { width:30px; height:30px; background:var(--blue); border-radius:7px; display:flex; align-items:center; justify-content:center; }
.rrhh-icon i { color:white; font-size:.9rem; }
.rrhh-title { font-family:'Oswald',sans-serif; font-size:.95rem; color:var(--navy); letter-spacing:.04em; }
.rrhh-body { padding:20px; }
.field-group { display:flex; flex-direction:column; gap:4px; }
.field-group label { font-size:.76rem; font-weight:700; color:var(--muted); letter-spacing:.05em; text-transform:uppercase; }
.field-input { border:1.5px solid var(--border); border-radius:7px; padding:10px 13px; font-size:.92rem; color:var(--text); outline:none; width:100%; background:#fff; }
.field-input:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(46,141,255,.12); }
.modal-bg { display:none; position:fixed; inset:0; background:rgba(11,28,46,.65); z-index:500; align-items:center; justify-content:center; padding:20px; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">RRHH · Mecánicos</div>
            <h1 class="page-title">{{ $mecanico->nombreCompleto() }}</h1>
            <p class="page-subtitle">Mecánico · {{ $mecanico->email }}</p>
        </div>
        <a href="{{ route('admin.rrhh.index') }}" class="btn-secondary-ta">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

@if(session('success'))
<div class="ta-alert success" style="margin-bottom:18px">
    <span class="ta-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
    <div>{{ session('success') }}</div>
</div>
@endif
@if(session('error'))
<div class="ta-alert error" style="margin-bottom:18px">
    <span class="ta-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
    <div>{{ session('error') }}</div>
</div>
@endif

{{-- Filtro mes/año --}}
<div class="ta-card" style="margin-bottom:20px">
    <div class="ta-card-body" style="padding:14px 20px">
        <form method="GET" style="display:flex; gap:12px; align-items:flex-end">
            <div>
                <label class="ta-label">Mes</label>
                <select name="mes" class="ta-input ta-select" style="width:140px">
                    @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $m)
                    <option value="{{ $i+1 }}" {{ $mes == $i+1 ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="ta-label">Año</label>
                <select name="anio" class="ta-input ta-select" style="width:110px">
                    @foreach($anios as $a)
                    <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary-ta" style="height:40px"><i class="bi bi-search"></i> Ver</button>
        </form>
    </div>
</div>

{{-- KPIs del mes --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:20px">
    <div class="ta-card" style="padding:18px; border-left:4px solid var(--blue)">
        <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:5px">Trabajos</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.6rem; color:var(--navy)">{{ $trabajos->count() }}</div>
    </div>
    <div class="ta-card" style="padding:18px; border-left:4px solid var(--ok)">
        <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:5px">Horas totales</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.6rem; color:var(--ok)">{{ number_format($totalHoras, 1) }}h</div>
    </div>
    <div class="ta-card" style="padding:18px; border-left:4px solid var(--warn)">
        <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:5px">Horas extra</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.6rem; color:var(--warn)">{{ number_format($totalHorasExtra, 1) }}h</div>
    </div>
    <div class="ta-card" style="padding:18px; border-left:4px solid {{ $comisionesPendientes > 0 ? 'var(--error)' : 'var(--muted)' }}">
        <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:5px">Comisiones pendientes</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.6rem; color:{{ $comisionesPendientes > 0 ? 'var(--error)' : 'var(--muted)' }}">${{ number_format($comisionesPendientes, 0, ',', '.') }}</div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1.4fr 1fr; gap:18px; align-items:start">
    {{-- Columna izquierda --}}
    <div>
        {{-- Trabajos del mes --}}
        <div class="rrhh-section">
            <div class="rrhh-header">
                <div class="rrhh-icon"><i class="bi bi-wrench-adjustable"></i></div>
                <span class="rrhh-title">TRABAJOS DEL MES</span>
            </div>
            <div style="overflow-x:auto">
                <table class="ta-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Vehículo</th>
                            <th>Servicio</th>
                            <th style="text-align:right">M.O.</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trabajos as $t)
                        <tr>
                            <td style="font-size:.82rem; color:var(--muted); white-space:nowrap">{{ $t->created_at->format('d/m') }}</td>
                            <td style="font-size:.84rem; color:var(--navy)">
                                {{ $t->ingreso->vehiculo->marca->nombre }} {{ $t->ingreso->vehiculo->modelo->nombre }}
                                <div style="font-family:'Oswald',sans-serif; font-size:.72rem; color:var(--accent)">{{ $t->ingreso->vehiculo->patente }}</div>
                            </td>
                            <td style="font-size:.82rem; color:var(--muted)">{{ ucfirst(str_replace('_',' ',$t->tipo_servicio)) }}</td>
                            <td style="text-align:right; font-weight:600; color:var(--navy)">${{ number_format($t->costo_mano_obra, 0, ',', '.') }}</td>
                            <td><span class="ta-badge badge-{{ $t->estado }}">{{ $t->etiquetaEstado() }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:32px; color:var(--muted); font-size:.86rem">Sin trabajos este mes</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Comisiones --}}
        <div class="rrhh-section">
            <div class="rrhh-header">
                <div class="rrhh-icon" style="background:var(--warn)"><i class="bi bi-cash-coin"></i></div>
                <span class="rrhh-title">COMISIONES DEL MES</span>
                <button onclick="document.getElementById('modalComision').style.display='flex'" class="btn-primary-ta" style="margin-left:auto; padding:5px 12px; font-size:.78rem">
                    <i class="bi bi-plus"></i> Nueva
                </button>
            </div>
            <div style="overflow-x:auto">
                <table class="ta-table">
                    <thead>
                        <tr>
                            <th>Trabajo</th>
                            <th style="text-align:right">Base</th>
                            <th style="text-align:center">%</th>
                            <th style="text-align:right">Comisión</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comisiones as $c)
                        <tr>
                            <td style="font-size:.82rem; color:var(--muted)">Trabajo #{{ $c->trabajo_id }}</td>
                            <td style="text-align:right; font-size:.84rem">${{ number_format($c->monto_base, 0, ',', '.') }}</td>
                            <td style="text-align:center; font-size:.84rem">{{ $c->porcentaje }}%</td>
                            <td style="text-align:right; font-weight:600; color:var(--ok)">${{ number_format($c->monto_comision, 0, ',', '.') }}</td>
                            <td>
                                @if($c->estado === 'pendiente')
                                <span class="ta-badge badge-pendiente">Pendiente</span>
                                @else
                                <span class="ta-badge badge-finalizado">Pagada</span>
                                @endif
                            </td>
                            <td>
                                @if($c->estado === 'pendiente')
                                <form method="POST" action="{{ route('admin.rrhh.comision.pagar', $c) }}">
                                    @csrf
                                    <button type="submit" class="btn-ok-ta" style="padding:4px 10px; font-size:.76rem">
                                        Pagar
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:32px; color:var(--muted); font-size:.86rem">Sin comisiones este mes</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Columna derecha --}}
    <div>
        {{-- Horas del mes --}}
        <div class="rrhh-section">
            <div class="rrhh-header">
                <div class="rrhh-icon" style="background:var(--ok)"><i class="bi bi-clock"></i></div>
                <span class="rrhh-title">HORAS DEL MES</span>
                <button onclick="document.getElementById('modalHoras').style.display='flex'" class="btn-primary-ta" style="margin-left:auto; padding:5px 12px; font-size:.78rem">
                    <i class="bi bi-plus"></i> Registrar
                </button>
            </div>
            <div class="rrhh-body">
                @forelse($horas as $h)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 12px; background:var(--card); border-radius:8px; margin-bottom:8px">
                    <div>
                        <div style="font-size:.86rem; font-weight:600; color:var(--navy)">{{ $h->fecha->format('d/m/Y') }}</div>
                        <div style="font-size:.76rem; color:var(--muted)">
                            {{ $h->tipo === 'extra' ? 'Horas extra' : 'Jornada normal' }}
                            @if($h->observaciones) · {{ $h->observaciones }} @endif
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px">
                        <span style="font-family:'Oswald',sans-serif; font-size:1.1rem; color:{{ $h->tipo === 'extra' ? 'var(--warn)' : 'var(--navy)' }}">
                            {{ number_format($h->horas, 1) }}h
                        </span>
                        <form method="POST" action="{{ route('admin.rrhh.horas.eliminar', $h) }}">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none; border:none; cursor:pointer; color:var(--error); font-size:.9rem" onclick="return confirm('¿Eliminar este registro?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div style="text-align:center; padding:24px; color:var(--muted); font-size:.86rem">
                    Sin horas registradas este mes
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Modal registrar horas --}}
<div id="modalHoras" class="modal-bg">
    <div style="background:white; border-radius:14px; width:100%; max-width:420px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.3)">
        <div style="padding:18px 22px; border-bottom:1px solid var(--border); background:var(--light); display:flex; justify-content:space-between; align-items:center">
            <div style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--navy); letter-spacing:.04em">
                <i class="bi bi-clock" style="color:var(--ok); margin-right:8px"></i>REGISTRAR HORAS
            </div>
            <button type="button" onclick="document.getElementById('modalHoras').style.display='none'"
                style="background:none; border:none; font-size:1.3rem; cursor:pointer; color:var(--muted)">×</button>
        </div>
        <form method="POST" action="{{ route('admin.rrhh.horas', $mecanico) }}">
            @csrf
            <div style="padding:22px; display:flex; flex-direction:column; gap:14px">
                <div class="field-group">
                    <label>Fecha</label>
                    <input type="date" name="fecha" class="field-input" value="{{ now()->toDateString() }}" required>
                </div>
                <div class="field-group">
                    <label>Horas trabajadas</label>
                    <input type="number" name="horas" class="field-input" min="0.5" max="24" step="0.5" value="8" required>
                </div>
                <div class="field-group">
                    <label>Tipo</label>
                    <select name="tipo" class="field-input" required>
                        <option value="normal">Jornada normal</option>
                        <option value="extra">Horas extra</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Observaciones</label>
                    <input type="text" name="observaciones" class="field-input" placeholder="Opcional...">
                </div>
            </div>
            <div style="padding:14px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px; background:var(--card)">
                <button type="button" class="btn-secondary-ta" onclick="document.getElementById('modalHoras').style.display='none'">Cancelar</button>
                <button type="submit" class="btn-ok-ta"><i class="bi bi-check-circle"></i> Registrar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal registrar comisión --}}
<div id="modalComision" class="modal-bg">
    <div style="background:white; border-radius:14px; width:100%; max-width:440px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.3)">
        <div style="padding:18px 22px; border-bottom:1px solid var(--border); background:var(--light); display:flex; justify-content:space-between; align-items:center">
            <div style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--navy); letter-spacing:.04em">
                <i class="bi bi-cash-coin" style="color:var(--warn); margin-right:8px"></i>REGISTRAR COMISIÓN
            </div>
            <button type="button" onclick="document.getElementById('modalComision').style.display='none'"
                style="background:none; border:none; font-size:1.3rem; cursor:pointer; color:var(--muted)">×</button>
        </div>
        <form method="POST" action="{{ route('admin.rrhh.comision', $mecanico) }}">
            @csrf
            <div style="padding:22px; display:flex; flex-direction:column; gap:14px">
                <div class="field-group">
                    <label>Trabajo realizado</label>
                    <select name="trabajo_id" class="field-input" required>
                        <option value="">— Seleccioná un trabajo —</option>
                        @foreach($todosLosTrabajos as $t)
                        <option value="{{ $t->id }}">
                            {{ $t->created_at->format('d/m') }} · {{ ucfirst(str_replace('_',' ',$t->tipo_servicio)) }}
                            · ${{ number_format($t->costo_mano_obra, 0, ',', '.') }} M.O.
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="field-group">
                    <label>Porcentaje de comisión (%)</label>
                    <input type="number" name="porcentaje" class="field-input" min="1" max="100" step="0.5" value="10" required>
                    <span style="font-size:.76rem; color:var(--muted)">Se calcula sobre el costo de mano de obra</span>
                </div>
            </div>
            <div style="padding:14px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px; background:var(--card)">
                <button type="button" class="btn-secondary-ta" onclick="document.getElementById('modalComision').style.display='none'">Cancelar</button>
                <button type="submit" class="btn-ok-ta"><i class="bi bi-check-circle"></i> Registrar</button>
            </div>
        </form>
    </div>
</div>
@endsection