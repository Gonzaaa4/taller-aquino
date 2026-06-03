@extends('layouts.app')
@section('title', 'Reporte de Turnos')
@section('topbar-title', 'Reporte de <span>Turnos</span>')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Reportes</div>
            <h1 class="page-title">Turnos Programados</h1>
            <p class="page-subtitle">
                {{ \Carbon\Carbon::parse($request->fecha_inicio)->format('d/m/Y') }}
                al {{ \Carbon\Carbon::parse($request->fecha_fin)->format('d/m/Y') }}
            </p>
        </div>
        <div style="display:flex; gap:10px">
            <a href="{{ route('admin.reportes.turnos', array_merge(request()->query(), ['formato'=>'pdf'])) }}"
               class="btn-danger-ta">
                <i class="bi bi-file-pdf"></i> Descargar PDF
            </a>
            <a href="{{ route('admin.reportes.formulario') }}" class="btn-secondary-ta">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<div class="kpi-grid" style="margin-bottom:24px">
    <div class="kpi-card kpi-blue">
        <div class="kpi-icon"><i class="bi bi-calendar-check"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Total Turnos</div>
            <div class="kpi-value">{{ $turnos->count() }}</div>
        </div>
    </div>
    <div class="kpi-card kpi-green">
        <div class="kpi-icon"><i class="bi bi-check-circle"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Finalizados</div>
            <div class="kpi-value">{{ $turnos->where('estado','finalizado')->count() }}</div>
        </div>
    </div>
    <div class="kpi-card kpi-red">
        <div class="kpi-icon"><i class="bi bi-x-circle"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Cancelados</div>
            <div class="kpi-value">{{ $turnos->where('estado','cancelado')->count() }}</div>
        </div>
    </div>
    <div class="kpi-card kpi-orange">
        <div class="kpi-icon"><i class="bi bi-clock-history"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Pendientes</div>
            <div class="kpi-value">{{ $turnos->where('estado','pendiente')->count() }}</div>
        </div>
    </div>
</div>

<div class="ta-card">
    @if($turnos->isEmpty())
    <div style="text-align:center; padding:56px; color:var(--muted)">
        <i class="bi bi-calendar-x" style="font-size:2.5rem; display:block; margin-bottom:14px; opacity:.3"></i>
        No hay turnos en el período seleccionado
    </div>
    @else
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>N° Seguimiento</th>
                    <th>Fecha / Hora</th>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th>Servicio</th>
                    <th>Mecánico</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($turnos as $t)
                <tr>
                    <td><span class="nro-seguimiento">{{ $t->numero_seguimiento }}</span></td>
                    <td>
                        <div style="font-size:.86rem; color:var(--navy)">{{ $t->fecha_hora_turno->format('d/m/Y') }}</div>
                        <div style="font-size:.74rem; color:var(--muted)">{{ $t->fecha_hora_turno->format('H:i') }} hs</div>
                    </td>
                    <td>
                        <div style="font-size:.86rem; font-weight:600; color:var(--navy)">{{ $t->cliente->nombreCompleto() }}</div>
                        <div style="font-size:.74rem; color:var(--muted)">{{ $t->cliente->telefono }}</div>
                    </td>
                    <td>
                        <div style="font-size:.84rem">{{ $t->vehiculo->marca->nombre }} {{ $t->vehiculo->modelo->nombre }}</div>
                        <div style="font-family:'Oswald',sans-serif; font-size:.76rem; color:var(--accent); letter-spacing:.06em">{{ $t->vehiculo->patente }}</div>
                    </td>
                    <td style="font-size:.83rem; color:var(--muted)">{{ ucfirst(str_replace('_',' ',$t->tipo_servicio)) }}</td>
                    <td style="font-size:.83rem">{{ $t->mecanico?->name ?? '—' }}</td>
                    <td><span class="ta-badge badge-{{ $t->estado }}">{{ $t->etiquetaEstado() }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
