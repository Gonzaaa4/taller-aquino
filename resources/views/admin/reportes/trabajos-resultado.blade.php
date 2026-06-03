@extends('layouts.app')
@section('title', 'Reporte de Trabajos')
@section('topbar-title', 'Reporte de Trabajos Realizados')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Reportes</div>
            <h1 class="page-title">Trabajos Realizados</h1>
            <p class="page-subtitle">
                {{ \Carbon\Carbon::parse($request->fecha_inicio)->format('d/m/Y') }}
                al {{ \Carbon\Carbon::parse($request->fecha_fin)->format('d/m/Y') }}
            </p>
        </div>
        <div style="display:flex; gap:10px">
            <a href="{{ route('admin.reportes.trabajos', array_merge(request()->query(), ['formato'=>'pdf'])) }}"
               class="btn-danger-ta">
                <i class="bi bi-file-pdf"></i> Descargar PDF
            </a>
            <a href="{{ route('admin.reportes.formulario') }}" class="btn-secondary-ta">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

{{-- Resumen KPIs --}}
<div class="kpi-grid" style="margin-bottom:24px">
    <div class="kpi-card kpi-blue">
        <div class="kpi-icon"><i class="bi bi-clipboard-check"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Total Trabajos</div>
            <div class="kpi-value">{{ $cantidadTrabajos }}</div>
        </div>
    </div>
    <div class="kpi-card kpi-green">
        <div class="kpi-icon"><i class="bi bi-currency-dollar"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Total Ingresos</div>
            <div class="kpi-value" style="font-size:1.4rem">${{ number_format($totalIngresos, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="kpi-card kpi-orange">
        <div class="kpi-icon"><i class="bi bi-tags"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Categorías</div>
            <div class="kpi-value">{{ $categorias->count() }}</div>
        </div>
    </div>
    @if($tecnicoDestacado)
    <div class="kpi-card kpi-blue">
        <div class="kpi-icon"><i class="bi bi-person-gear"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Técnico Destacado</div>
            <div class="kpi-value" style="font-size:1rem">{{ $tecnicoDestacado->name }}</div>
        </div>
    </div>
    @endif
</div>

{{-- Tabla de trabajos --}}
@if($trabajos->isEmpty())
<div class="ta-card">
    <div style="text-align:center; padding:56px; color:var(--muted)">
        <i class="bi bi-clipboard-x" style="font-size:2.5rem; display:block; margin-bottom:14px; opacity:.3"></i>
        No se encontraron trabajos en el período seleccionado
    </div>
</div>
@else
@foreach($trabajos->groupBy('tipo_servicio') as $tipo => $grupo)
<div class="section-label" style="margin-top:24px">
    <h2>{{ strtoupper(str_replace('_',' ',$tipo)) }} ({{ $grupo->count() }})</h2>
    <div class="section-label-line"></div>
    <span style="font-size:.84rem; color:var(--muted); white-space:nowrap">
        ${{ number_format($grupo->sum('costo_total'), 0, ',', '.') }}
    
</div>

<div class="ta-card" style="margin-bottom:16px">
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th>Descripción</th>
                    <th>Técnico</th>
                    <th>Repuestos</th>
                    <th style="text-align:right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grupo as $t)
                <tr>
                    <td style="white-space:nowrap">
                        <div style="font-size:.86rem; color:var(--navy)">{{ $t->fecha_trabajo->format('d/m/Y') }}</div>
                        <div style="font-size:.74rem; color:var(--muted)">{{ $t->fecha_trabajo->format('H:i') }} hs</div>
                    </td>
                    <td>
                        <div style="font-size:.86rem; font-weight:600; color:var(--navy)">{{ $t->ingreso->cliente->nombreCompleto() }}</div>
                        <div style="font-size:.74rem; color:var(--muted)">{{ $t->ingreso->cliente->telefono }}</div>
                    </td>
                    <td>
                        <div style="font-size:.84rem; color:var(--navy)">
                            {{ $t->ingreso->vehiculo->marca->nombre }} {{ $t->ingreso->vehiculo->modelo->nombre }}
                        </div>
                        <div style="font-family:'Oswald',sans-serif; font-size:.76rem; color:var(--accent); letter-spacing:.06em">
                            {{ $t->ingreso->vehiculo->patente }}
                        </div>
                    </td>
                    <td style="font-size:.83rem; color:var(--muted); max-width:200px">
                        {{ Str::limit($t->descripcion_trabajo, 60) }}
                    </td>
                    <td style="font-size:.84rem; color:var(--navy)">{{ $t->mecanico->name }}</td>
                    <td style="font-size:.78rem; color:var(--muted)">
                        @foreach($t->repuestos as $r)
                            {{ $r->nombre }} ×{{ $r->pivot->cantidad }}<br>
                        @endforeach
                        @if($t->repuestos->isEmpty()) — @endif
                    </td>
                    <td style="text-align:right; font-weight:700; color:var(--navy); white-space:nowrap">
                        ${{ number_format($t->costo_total, 2, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach

<div style="text-align:right; padding:16px 20px; background:white; border-radius:12px; border:1px solid var(--border);">
    <span style="font-family:'Oswald',sans-serif; font-size:1.2rem; color:var(--navy)">
        TOTAL GENERAL: ${{ number_format($totalIngresos, 2, ',', '.') }}
    
</div>
@endif
@endsection
