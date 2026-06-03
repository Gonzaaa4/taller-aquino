@extends('layouts.app')
@section('title', 'Dashboard')
@section('topbar-title', 'Dashboard — Resumen Operativo')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Panel Administrativo</div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Resumen operativo en tiempo real del taller</p>
        </div>
        <a href="{{ route('admin.turnos.solicitar') }}" class="btn-primary-ta">
            <i class="bi bi-plus-circle"></i> Nuevo Turno
        </a>
    </div>
</div>

{{-- KPIs --}}
<div class="kpi-grid">
    <div class="kpi-card kpi-orange">
        <div class="kpi-icon"><i class="bi bi-clock-history"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Turnos Pendientes</div>
            <div class="kpi-value">{{ $turnosPendientes }}</div>
        </div>
    </div>
    <div class="kpi-card kpi-blue">
        <div class="kpi-icon"><i class="bi bi-wrench-adjustable"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Vehículos en Taller</div>
            <div class="kpi-value">{{ $vehiculosEnTaller }}</div>
        </div>
    </div>
    <div class="kpi-card kpi-green">
        <div class="kpi-icon"><i class="bi bi-check-circle"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Trabajos este Mes</div>
            <div class="kpi-value">{{ $trabajosMes }}</div>
        </div>
    </div>
    <div class="kpi-card kpi-red">
        <div class="kpi-icon"><i class="bi bi-exclamation-triangle"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Alertas de Stock</div>
            <div class="kpi-value">{{ $alertasStock }}</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 380px; gap:20px; align-items:start">

    {{-- Turnos del día --}}
    <div class="ta-card">
        <div class="ta-card-header">
            <div class="ta-card-title">
                <i class="bi bi-calendar-day"></i> Turnos de Hoy
            </div>
            <a href="{{ route('admin.turnos.agenda') }}" class="btn-secondary-ta" style="font-size:.8rem; padding:6px 14px">
                Ver agenda completa
            </a>
        </div>

        @forelse($turnosHoy as $turno)
        <div style="display:flex; align-items:center; gap:16px; padding:14px 20px; border-bottom:1px solid rgba(192,211,232,.4);">
            <div style="text-align:center; min-width:52px;">
                <div style="font-family:'Oswald',sans-serif; font-size:1.1rem; color:var(--navy); line-height:1">
                    {{ $turno->fecha_hora_turno->format('H:i') }}
                </div>
                <div style="font-size:.68rem; color:var(--muted); text-transform:uppercase">hs</div>
            </div>
            <div style="width:3px; height:36px; background:var(--border); border-radius:2px; flex-shrink:0"></div>
            <div style="flex:1; min-width:0">
                <div style="font-weight:600; font-size:.9rem; color:var(--navy)">
                    {{ $turno->cliente->nombreCompleto() }}
                </div>
                <div style="font-size:.78rem; color:var(--muted)">
                    {{ $turno->vehiculo->marca->nombre }} {{ $turno->vehiculo->modelo->nombre }}
                    · <span style="font-family:'Oswald',sans-serif; letter-spacing:.05em">{{ $turno->vehiculo->patente }}
                </div>
            </div>
            @if($turno->mecanico)
            <div style="font-size:.76rem; color:var(--muted); text-align:right; flex-shrink:0">
                <i class="bi bi-person-gear"></i> {{ $turno->mecanico->name }}
            </div>
            @endif
            <span class="ta-badge badge-{{ $turno->estado }}">{{ $turno->etiquetaEstado() }}
        </div>
        @empty
        <div style="text-align:center; padding:48px 20px; color:var(--muted)">
            <i class="bi bi-calendar-x" style="font-size:2rem; display:block; margin-bottom:10px; opacity:.4"></i>
            No hay turnos programados para hoy
        </div>
        @endforelse
    </div>

    {{-- Stock crítico --}}
    <div class="ta-card">
        <div class="ta-card-header">
            <div class="ta-card-title" style="color:var(--error)">
                <i class="bi bi-exclamation-triangle"></i> Stock Crítico
            </div>
            <a href="{{ route('admin.inventario.repuestos', ['stock_bajo' => 1]) }}"
               style="font-size:.76rem; color:var(--accent); text-decoration:none">Ver todos →</a>
        </div>

        @forelse($repuestosCriticos as $rep)
        @php $est = $rep->estadoStock(); @endphp
        <div style="display:flex; align-items:center; gap:12px; padding:12px 18px; border-bottom:1px solid rgba(192,211,232,.4);
            background: {{ $est === 'sin_stock' ? 'rgba(217,48,37,.04)' : 'rgba(230,126,0,.03)' }}">
            <div style="flex:1; min-width:0">
                <div style="font-size:.86rem; font-weight:600; color:var(--navy)">{{ $rep->nombre }}</div>
                <div style="font-size:.72rem; color:var(--muted)">{{ ucfirst($rep->categoria) }}</div>
            </div>
            <div style="text-align:right; flex-shrink:0">
                <div class="{{ $est === 'sin_stock' ? 'stock-sin' : 'stock-critico' }}">
                    {{ $rep->cantidad_stock }} ud.
                </div>
                <div style="font-size:.68rem; color:var(--muted); margin-top:2px">mín: {{ $rep->stock_minimo }}</div>
            </div>
        </div>
        @empty
        <div style="text-align:center; padding:36px 20px; color:var(--muted)">
            <i class="bi bi-check-circle" style="font-size:1.8rem; color:var(--ok); display:block; margin-bottom:8px"></i>
            Todo el stock en orden
        </div>
        @endforelse
    </div>
</div>
@endsection
