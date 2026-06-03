@extends('layouts.app')
@section('title', 'Turnos')
@section('topbar-title', 'Gestión de <span>Turnos</span>')

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
                        </span>
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
                            <span style="font-size:.78rem; color:var(--muted); font-style:italic">Sin asignar</span>
                        @endif
                    </td>
                    <td>
                        <span class="ta-badge badge-{{ $turno->estado }}">{{ $turno->etiquetaEstado() }}</span>
                    </td>
                    <td>
                        <div style="display:flex; gap:6px">
                            <a href="{{ route('admin.turnos.show', $turno) }}"
                               class="btn-secondary-ta" style="padding:6px 12px; font-size:.8rem">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($turno->estaPendiente())
                            <form method="POST" action="{{ route('admin.turnos.confirmar', $turno) }}">
                                @csrf
                                <button type="submit" class="btn-ok-ta" style="padding:6px 12px; font-size:.8rem">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </form>
                            @endif
                            @if($turno->puedeSerCancelado())
                            <form method="POST" action="{{ route('admin.turnos.cancelar', $turno) }}"
                                onsubmit="return confirm('¿Cancelar este turno?')">
                                @csrf
                                <button type="submit" class="btn-danger-ta" style="padding:6px 12px; font-size:.8rem">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </form>
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
    <div style="padding:16px 20px; border-top:1px solid var(--border)">
        {{ $turnos->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
