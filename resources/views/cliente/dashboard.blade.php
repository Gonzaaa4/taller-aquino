@extends('layouts.app')
@section('title', 'Mi Panel')
@section('topbar-title', 'Mi Panel')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Portal del Cliente</div>
            <h1 class="page-title">¡Hola, {{ auth()->user()->name }}!</h1>
            <p class="page-subtitle">Bienvenido/a al sistema de Taller Aquino</p>
        </div>
        <a href="{{ route('cliente.turnos.solicitar') }}" class="btn-primary-ta">
            <i class="bi bi-plus-circle"></i> Solicitar Turno
        </a>
    </div>
</div>

{{-- Próximo turno destacado --}}
@if($proximoTurno)
<div style="background:linear-gradient(135deg, #1255a1 0%, #0b1c2e 100%); border-radius:14px; padding:24px 28px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap">
    <div>
        <div style="font-size:.72rem; color:rgba(255,255,255,.5); text-transform:uppercase; letter-spacing:.1em; margin-bottom:6px">
            <i class="bi bi-clock" style="margin-right:5px"></i>Próximo Turno
        </div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.5rem; color:white; margin-bottom:4px">
            {{ $proximoTurno->fecha_hora_turno->locale('es')->isoFormat('dddd D [de] MMMM') }}
            a las {{ $proximoTurno->fecha_hora_turno->format('H:i') }} hs
        </div>
        <div style="font-size:.9rem; color:rgba(255,255,255,.65)">
            {{ $proximoTurno->vehiculo->marca->nombre }} {{ $proximoTurno->vehiculo->modelo->nombre }}
            · {{ $proximoTurno->vehiculo->patente }}
            · {{ ucfirst(str_replace('_',' ',$proximoTurno->tipo_servicio)) }}
        </div>
    </div>
    <div style="text-align:right">
        <div style="font-family:'Oswald',sans-serif; font-size:1.2rem; color:rgba(255,255,255,.6); letter-spacing:.08em; margin-bottom:8px">
            {{ $proximoTurno->numero_seguimiento }}
        </div>
        <span class="ta-badge badge-{{ $proximoTurno->estado }}" style="font-size:.82rem">
            {{ $proximoTurno->etiquetaEstado() }}
        
    </div>
</div>
@endif

<div style="display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start">

    {{-- Turnos recientes --}}
    <div class="ta-card">
        <div class="ta-card-header">
            <div class="ta-card-title"><i class="bi bi-clock-history"></i> Mis Turnos Recientes</div>
            <a href="{{ route('cliente.turnos.index') }}" class="btn-secondary-ta" style="font-size:.8rem; padding:6px 14px">
                Ver todos →
            </a>
        </div>

        @forelse($turnosRecientes as $turno)
        <div style="display:flex; align-items:center; gap:16px; padding:14px 20px; border-bottom:1px solid rgba(192,211,232,.4)">
            <div style="text-align:center; min-width:52px; padding:8px; background:var(--card); border-radius:8px; border:1px solid var(--border)">
                <div style="font-family:'Oswald',sans-serif; font-size:1.1rem; color:var(--navy); line-height:1">
                    {{ $turno->fecha_hora_turno->format('d') }}
                </div>
                <div style="font-size:.68rem; color:var(--muted); text-transform:uppercase">
                    {{ $turno->fecha_hora_turno->locale('es')->isoFormat('MMM') }}
                </div>
            </div>
            <div style="flex:1; min-width:0">
                <div style="font-weight:600; font-size:.9rem; color:var(--navy)">
                    {{ $turno->vehiculo->marca->nombre }} {{ $turno->vehiculo->modelo->nombre }}
                </div>
                <div style="font-size:.78rem; color:var(--muted)">
                    {{ ucfirst(str_replace('_',' ',$turno->tipo_servicio)) }}
                    · {{ $turno->fecha_hora_turno->format('H:i') }} hs
                </div>
                <div style="font-family:'Oswald',sans-serif; font-size:.72rem; color:var(--accent); letter-spacing:.06em">
                    {{ $turno->numero_seguimiento }}
                </div>
            </div>
            <span class="ta-badge badge-{{ $turno->estado }}">{{ $turno->etiquetaEstado() }}
        </div>
        @empty
        <div style="text-align:center; padding:48px 20px; color:var(--muted)">
            <i class="bi bi-calendar-x" style="font-size:2.5rem; display:block; margin-bottom:14px; opacity:.3"></i>
            <div style="font-weight:600; margin-bottom:8px">No tenés turnos registrados</div>
            <a href="{{ route('cliente.turnos.solicitar') }}" class="btn-primary-ta" style="display:inline-flex">
                <i class="bi bi-plus-circle"></i> Solicitar primer turno
            </a>
        </div>
        @endforelse
    </div>

    {{-- Sidebar cliente --}}
    <div>
        {{-- Mis vehículos --}}
        <div class="ta-card" style="margin-bottom:16px">
            <div class="ta-card-header">
                <div class="ta-card-title"><i class="bi bi-car-front"></i> Mis Vehículos</div>
                <a href="{{ route('cliente.vehiculos.crear') }}" style="color:var(--accent); text-decoration:none; font-size:.8rem">
                    + Agregar
                </a>
            </div>
            @forelse($vehiculos as $v)
            <div style="padding:12px 18px; border-bottom:1px solid rgba(192,211,232,.4)">
                <div style="font-weight:600; font-size:.88rem; color:var(--navy)">
                    {{ $v->marca->nombre }} {{ $v->modelo->nombre }} {{ $v->anio }}
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center">
                    <div style="font-family:'Oswald',sans-serif; font-size:.8rem; color:var(--accent); letter-spacing:.06em">
                        {{ $v->patente }}
                    </div>
                    <div style="font-size:.72rem; color:var(--muted)">{{ number_format($v->kilometraje) }} km</div>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:24px; color:var(--muted); font-size:.86rem">
                No hay vehículos registrados
            </div>
            @endforelse
        </div>

        {{-- Consultar estado --}}
        <div class="ta-card">
            <div style="padding:24px; text-align:center">
                <i class="bi bi-search" style="font-size:2rem; color:var(--blue); display:block; margin-bottom:10px"></i>
                <div style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--navy); letter-spacing:.04em; margin-bottom:6px">
                    CONSULTAR ESTADO
                </div>
                <p style="font-size:.84rem; color:var(--muted); margin-bottom:16px; line-height:1.5">
                    Seguí el progreso de tu reparación con el número de seguimiento.
                </p>
                <a href="{{ route('cliente.consultar-estado') }}" class="btn-primary-ta" style="width:100%; justify-content:center">
                    <i class="bi bi-search"></i> Consultar ahora
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
