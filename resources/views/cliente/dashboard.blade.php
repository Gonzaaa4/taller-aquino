@extends('layouts.app')

@section('title', 'Mi Panel')

@section('breadcrumb')
    <li class="breadcrumb-item active">Mi Panel</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">¡Hola, {{ auth()->user()->name }}!</h4>
        <p class="text-muted small mb-0">Bienvenido/a al panel de Taller Aquino</p>
    </div>
    <a href="{{ route('cliente.turnos.solicitar') }}" class="btn btn-taller">
        <i class="bi bi-plus-circle me-1"></i> Solicitar Turno
    </a>
</div>

{{-- Próximo turno --}}
@if($proximoTurno)
<div class="card border-start border-4 border-primary mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="text-muted small mb-1"><i class="bi bi-calendar-event me-1"></i>Próximo Turno</div>
                <h5 class="fw-bold mb-1">
                    {{ $proximoTurno->fecha_hora_turno->locale('es')->isoFormat('dddd D [de] MMMM, YYYY') }}
                    a las {{ $proximoTurno->fecha_hora_turno->format('H:i') }} hs
                </h5>
                <p class="mb-0 text-muted">
                    {{ $proximoTurno->vehiculo->marca->nombre }} {{ $proximoTurno->vehiculo->modelo->nombre }}
                    – {{ $proximoTurno->vehiculo->patente }}
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="nro-seguimiento mb-2">{{ $proximoTurno->numero_seguimiento }}</div>
                <span class="badge badge-{{ $proximoTurno->estado }} rounded-pill px-3 py-2">
                    {{ $proximoTurno->etiquetaEstado() }}
                </span>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row g-3">
    {{-- Turnos recientes --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>Mis Turnos</span>
                <a href="{{ route('cliente.turnos.index') }}" class="btn btn-sm btn-outline-secondary">Ver todos</a>
            </div>
            <div class="card-body p-0">
                @forelse($turnosRecientes as $turno)
                <div class="d-flex align-items-center px-3 py-2 border-bottom">
                    <div class="me-3">
                        <div class="fw-semibold small">{{ $turno->fecha_hora_turno->format('d/m/Y') }}</div>
                        <div class="text-muted" style="font-size:.72rem">{{ $turno->fecha_hora_turno->format('H:i') }} hs</div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="small">{{ $turno->vehiculo->marca->nombre }} {{ $turno->vehiculo->modelo->nombre }}</div>
                        <div class="font-mono text-muted" style="font-size:.72rem">{{ $turno->numero_seguimiento }}</div>
                    </div>
                    <span class="badge badge-{{ $turno->estado }} rounded-pill">{{ $turno->etiquetaEstado() }}</span>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                    No tenés turnos registrados.
                    <br>
                    <a href="{{ route('cliente.turnos.solicitar') }}" class="btn btn-taller btn-sm mt-2">Solicitar primer turno</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Mis vehículos + consulta rápida --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-car-front me-2"></i>Mis Vehículos</span>
                <a href="{{ route('cliente.vehiculos.crear') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-plus"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @forelse($vehiculos as $v)
                <div class="px-3 py-2 border-bottom">
                    <div class="fw-semibold small">{{ $v->marca->nombre }} {{ $v->modelo->nombre }} {{ $v->anio }}</div>
                    <div class="text-muted" style="font-size:.72rem">Patente: {{ $v->patente }}</div>
                </div>
                @empty
                <div class="text-center text-muted py-3 small">No hay vehículos registrados.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-body text-center py-4">
                <i class="bi bi-search fs-2 text-primary d-block mb-2"></i>
                <h6 class="fw-bold">Consultar Estado</h6>
                <p class="text-muted small">Seguí el progreso de tu reparación con el número de seguimiento.</p>
                <a href="{{ route('cliente.consultar-estado') }}" class="btn btn-taller btn-sm w-100">
                    Consultar ahora
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
