@extends('layouts.app')
@section('title', 'Detalle de Turno')
@section('topbar-title', 'Detalle de Turno')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Operaciones · Turnos</div>
            <h1 class="page-title">
                Turno
                <span class="nro-seguimiento">{{ $turno->numero_seguimiento }}
            </h1>
            <p class="page-subtitle">Solicitado el {{ $turno->fecha_hora_solicitud->format('d/m/Y H:i') }}</p>
        </div>
        <div style="display:flex; gap:10px; align-items:center">
            <span class="ta-badge badge-{{ $turno->estado }}" style="font-size:.88rem; padding:7px 16px">
                {{ $turno->etiquetaEstado() }}
            
            <a href="{{ route('admin.turnos.index') }}" class="btn-secondary-ta">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start">

    {{-- Info principal --}}
    <div>
        <div class="ta-card" style="margin-bottom:20px">
            <div class="ta-card-header">
                <div class="ta-card-title"><i class="bi bi-calendar-event" style="color:var(--blue)"></i> Información del Turno</div>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:20px">
                <div>
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Fecha y hora</div>
                    <div style="font-family:'Oswald',sans-serif; font-size:1.3rem; color:var(--navy)">
                        {{ $turno->fecha_hora_turno->format('d/m/Y') }}
                    </div>
                    <div style="font-size:.9rem; color:var(--muted)">{{ $turno->fecha_hora_turno->format('H:i') }} hs</div>
                </div>
                <div>
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Tipo de servicio</div>
                    <div style="font-weight:600; color:var(--navy)">{{ ucfirst(str_replace('_',' ',$turno->tipo_servicio)) }}</div>
                    @if($turno->es_presencial)
                    <span style="font-size:.74rem; background:rgba(46,141,255,.1); color:var(--blue); padding:2px 8px; border-radius:20px; font-weight:600">Presencial
                    @endif
                </div>
                <div>
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Cliente</div>
                    <div style="font-weight:600; color:var(--navy)">{{ $turno->cliente->nombreCompleto() }}</div>
                    <div style="font-size:.82rem; color:var(--muted)">{{ $turno->cliente->telefono }}</div>
                    <div style="font-size:.82rem; color:var(--muted)">{{ $turno->cliente->email }}</div>
                </div>
                <div>
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Vehículo</div>
                    <div style="font-weight:600; color:var(--navy)">
                        {{ $turno->vehiculo->marca->nombre }} {{ $turno->vehiculo->modelo->nombre }} {{ $turno->vehiculo->anio }}
                    </div>
                    <div style="font-family:'Oswald',sans-serif; font-size:.88rem; color:var(--accent); letter-spacing:.06em">
                        {{ $turno->vehiculo->patente }}
                    </div>
                </div>
                @if($turno->observaciones)
                <div style="grid-column:span 2">
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px">Observaciones del cliente</div>
                    <div style="background:var(--card); border:1px solid var(--border); border-radius:8px; padding:12px 14px; font-size:.88rem">
                        {{ $turno->observaciones }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Trabajos realizados (si ya ingresó) --}}
        @if($turno->ingreso)
        <div class="ta-card">
            <div class="ta-card-header">
                <div class="ta-card-title"><i class="bi bi-clipboard-check" style="color:var(--blue)"></i> Trabajos Realizados</div>
                <a href="{{ route('admin.trabajos.show', $turno->ingreso) }}" class="btn-secondary-ta" style="font-size:.8rem; padding:6px 14px">
                    Ver orden completa →
                </a>
            </div>
            @forelse($turno->ingreso->trabajos as $t)
            <div style="padding:14px 20px; border-bottom:1px solid var(--border)">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px">
                    <div style="font-weight:600; font-size:.9rem; color:var(--navy)">{{ ucfirst(str_replace('_',' ',$t->tipo_servicio)) }}</div>
                    <span class="ta-badge badge-{{ $t->estado }}">{{ $t->etiquetaEstado() }}
                </div>
                <div style="font-size:.84rem; color:var(--muted)">{{ Str::limit($t->descripcion_trabajo, 80) }}</div>
                <div style="font-size:.84rem; font-weight:600; color:var(--navy); margin-top:4px">
                    Total: ${{ number_format($t->costo_total, 2, ',', '.') }}
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:30px; color:var(--muted); font-size:.88rem">
                Sin trabajos registrados aún
            </div>
            @endforelse
        </div>
        @endif
    </div>

    {{-- Panel lateral --}}
    <div>
        {{-- Asignar mecánico --}}
        @if(!$turno->estaCancelado() && !$turno->estaFinalizado())
        <div class="ta-card" style="margin-bottom:16px">
            <div class="ta-card-header">
                <div class="ta-card-title"><i class="bi bi-person-gear"></i> Mecánico Asignado</div>
            </div>
            <div style="padding:16px">
                @if($turno->mecanico)
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px; padding:10px; background:var(--card); border-radius:8px; border:1px solid var(--border)">
                    <div style="width:34px; height:34px; border-radius:50%; background:var(--light); color:var(--blue); display:flex; align-items:center; justify-content:center; font-family:'Oswald',sans-serif; font-size:.8rem; flex-shrink:0">
                        {{ strtoupper(substr($turno->mecanico->name,0,1).substr($turno->mecanico->apellido,0,1)) }}
                    </div>
                    <div>
                        <div style="font-weight:600; font-size:.88rem; color:var(--navy)">{{ $turno->mecanico->nombreCompleto() }}</div>
                        <div style="font-size:.74rem; color:var(--muted)">Mecánico asignado</div>
                    </div>
                </div>
                @endif
                <form method="POST" action="{{ route('admin.turnos.asignar-mecanico', $turno) }}">
                    @csrf
                    <label class="ta-label">{{ $turno->mecanico ? 'Cambiar mecánico' : 'Asignar mecánico' }}</label>
                    <select name="mecanico_id" class="ta-input ta-select" style="margin-bottom:10px">
                        <option value="">Sin asignar</option>
                        @foreach($mecanicos as $m)
                        <option value="{{ $m->id }}" {{ $turno->mecanico_id == $m->id ? 'selected' : '' }}>
                            {{ $m->nombreCompleto() }}
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary-ta" style="width:100%; justify-content:center">
                        <i class="bi bi-check-circle"></i> Guardar
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Acciones --}}
        <div class="ta-card">
            <div class="ta-card-header">
                <div class="ta-card-title"><i class="bi bi-lightning"></i> Acciones</div>
            </div>
            <div style="padding:16px; display:flex; flex-direction:column; gap:10px">
                @if($turno->estaPendiente())
                <form method="POST" action="{{ route('admin.turnos.confirmar', $turno) }}">
                    @csrf
                    <button type="submit" class="btn-ok-ta" style="width:100%; justify-content:center">
                        <i class="bi bi-check-circle"></i> Confirmar Turno
                    </button>
                </form>
                @endif

                @if($turno->estaConfirmado() && !$turno->ingreso)
                <a href="{{ route('admin.trabajos.index') }}" class="btn-primary-ta" style="justify-content:center">
                    <i class="bi bi-box-arrow-in-right"></i> Registrar Ingreso
                </a>
                @endif

                @if($turno->ingreso && !in_array($turno->ingreso->estado, ['finalizado','entregado']))
                <a href="{{ route('admin.trabajos.show', $turno->ingreso) }}" class="btn-primary-ta" style="justify-content:center">
                    <i class="bi bi-wrench-adjustable"></i> Ir a Orden de Trabajo
                </a>
                @endif

                @if($turno->puedeSerCancelado())
                <form method="POST" action="{{ route('admin.turnos.cancelar', $turno) }}"
                    onsubmit="return confirm('¿Cancelar este turno?')">
                    @csrf
                    <button type="submit" class="btn-danger-ta" style="width:100%; justify-content:center">
                        <i class="bi bi-x-circle"></i> Cancelar Turno
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
