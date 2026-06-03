@extends('layouts.app')

@section('title', 'Consultar Estado de Reparación')

@section('breadcrumb')
    <li class="breadcrumb-item active">Consultar Estado</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <h4 class="fw-bold mb-1"><i class="bi bi-search me-2 text-primary"></i>Estado de Reparación</h4>
        <p class="text-muted mb-4">Ingresá tu número de seguimiento para ver el estado de tu vehículo.</p>

        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="{{ route(auth()->check() ? 'cliente.consultar-estado' : 'consultar.estado') }}">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-hash"></i></span>
                        <input type="text"
                               name="numero_seguimiento"
                               class="form-control form-control-lg @error('numero_seguimiento') is-invalid @enderror"
                               placeholder="Ej: TKA-04821"
                               value="{{ old('numero_seguimiento', request('numero_seguimiento', $turno->numero_seguimiento ?? '')) }}"
                               style="font-family: 'DM Mono', monospace; letter-spacing:.05em"
                               required>
                        <button type="submit" class="btn btn-taller px-4">
                            <i class="bi bi-search me-1"></i> Consultar
                        </button>
                    </div>
                    @error('numero_seguimiento')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </form>

                @if(session('error'))
                    <div class="alert alert-danger mt-3 mb-0 py-2 small">
                        <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Resultado --}}
        @isset($turno)
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="fw-bold">
                    <i class="bi bi-car-front me-2"></i>
                    {{ $turno->vehiculo->marca->nombre }} {{ $turno->vehiculo->modelo->nombre }}
                    {{ $turno->vehiculo->anio }}
                </span>
                <span class="nro-seguimiento">{{ $turno->numero_seguimiento }}</span>
            </div>
            <div class="card-body">

                {{-- Estado actual --}}
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        @php
                            $estadoIngreso = $ingreso?->estado ?? 'sin_ingresar';
                            $iconos = [
                                'ingresado'      => ['bi-box-arrow-in-right', 'text-secondary'],
                                'en_diagnostico' => ['bi-search',             'text-info'],
                                'en_reparacion'  => ['bi-wrench-adjustable',  'text-warning'],
                                'finalizado'     => ['bi-check-circle',       'text-success'],
                                'entregado'      => ['bi-car-front',          'text-success'],
                                'sin_ingresar'   => ['bi-clock',              'text-muted'],
                            ];
                            [$icono, $color] = $iconos[$estadoIngreso] ?? ['bi-question-circle', 'text-muted'];
                        @endphp
                        <i class="bi {{ $icono }} {{ $color }} fs-1"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Estado actual</div>
                        <h5 class="fw-bold mb-0">
                            @if($ingreso)
                                {{ $ingreso->etiquetaEstado() }}
                            @else
                                Turno {{ $turno->etiquetaEstado() }} – Vehículo aún no ingresado
                            @endif
                        </h5>
                        @if($ingreso && $ingreso->estado !== 'entregado')
                            <div class="text-muted small">Ingresó el {{ $ingreso->fecha_ingreso->format('d/m/Y H:i') }}</div>
                        @endif
                    </div>
                </div>

                {{-- Datos del turno --}}
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted">Turno</td>
                        <td>{{ $turno->fecha_hora_turno->format('d/m/Y H:i') }} hs</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tipo de servicio</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $turno->tipo_servicio)) }}</td>
                    </tr>
                    @if($turno->mecanico)
                    <tr>
                        <td class="text-muted">Mecánico asignado</td>
                        <td>{{ $turno->mecanico->nombreCompleto() }}</td>
                    </tr>
                    @endif
                    @if($turno->observaciones)
                    <tr>
                        <td class="text-muted">Observaciones</td>
                        <td>{{ $turno->observaciones }}</td>
                    </tr>
                    @endif
                </table>

                {{-- Trabajos realizados --}}
                @if($ingreso && $ingreso->trabajos->isNotEmpty())
                <hr>
                <h6 class="fw-bold mb-3"><i class="bi bi-clipboard-check me-2"></i>Trabajos Realizados</h6>
                @foreach($ingreso->trabajos as $trabajo)
                <div class="border rounded-3 p-3 mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-semibold small">{{ ucfirst(str_replace('_', ' ', $trabajo->tipo_servicio)) }}</span>
                        <span class="badge badge-{{ $trabajo->estado }} rounded-pill">{{ $trabajo->etiquetaEstado() }}</span>
                    </div>
                    <p class="text-muted small mb-2">{{ $trabajo->descripcion_trabajo }}</p>
                    @if($trabajo->repuestos->isNotEmpty())
                    <div class="text-muted small">
                        <strong>Repuestos:</strong>
                        {{ $trabajo->repuestos->map(fn($r) => $r->nombre . ' ×' . $r->pivot->cantidad)->implode(', ') }}
                    </div>
                    @endif
                </div>
                @endforeach
                @endif

            </div>
        </div>
        @endisset

        {{-- Ayuda --}}
        <div class="card mt-3 bg-light border-0">
            <div class="card-body small text-muted">
                <i class="bi bi-info-circle me-1"></i>
                <strong>¿Dónde encuentro mi número de seguimiento?</strong>
                Te lo enviamos al confirmar tu turno. También podés verlo en "Mis Turnos" si tenés cuenta.
                Para consultas, llamá al <strong>(376) 000-0000</strong>.
            </div>
        </div>
    </div>
</div>

<style>
    .nro-seguimiento { font-family: 'DM Mono', monospace; font-size: 1.1rem; color: #C0392B; }
    .btn-taller { background:#C0392B; color:#fff; border:none; }
    .btn-taller:hover { background:#96281B; color:#fff; }
</style>
@endsection
