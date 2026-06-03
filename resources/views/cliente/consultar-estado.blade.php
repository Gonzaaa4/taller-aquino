@extends('layouts.app')
@section('title', 'Consultar Estado')
@section('topbar-title', 'Consultar Estado')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Portal del Cliente</div>
            <h1 class="page-title">Estado de Reparación</h1>
            <p class="page-subtitle">Consultá el progreso de tu vehículo con el número de seguimiento</p>
        </div>
    </div>
</div>

<div style="max-width:680px">
    {{-- Formulario de búsqueda --}}
    <div class="ta-card" style="margin-bottom:24px">
        <div style="padding:24px">
            <form method="POST" action="{{ route(auth()->check() ? 'cliente.consultar-estado' : 'consultar.estado') }}">
                @csrf
                <label class="ta-label" style="font-size:.9rem; margin-bottom:8px">Número de seguimiento</label>
                <div style="display:flex; gap:10px">
                    <div style="flex:1; position:relative">
                        <i class="bi bi-hash" style="position:absolute; left:13px; top:50%; transform:translateY(-50%); color:var(--muted); font-size:1rem"></i>
                        <input type="text" name="numero_seguimiento"
                            class="ta-input {{ $errors->has('numero_seguimiento') ? 'is-invalid' : '' }}"
                            style="padding-left:36px; font-family:'Oswald',sans-serif; letter-spacing:.06em; font-size:1rem"
                            placeholder="TKA-00000"
                            value="{{ old('numero_seguimiento', request('numero_seguimiento', isset($turno) ? $turno->numero_seguimiento : '')) }}"
                            required>
                    </div>
                    <button type="submit" class="btn-primary-ta" style="white-space:nowrap">
                        <i class="bi bi-search"></i> Consultar
                    </button>
                </div>
                @error('numero_seguimiento')
                <div class="ta-invalid-msg" style="margin-top:6px">{{ $message }}</div>
                @enderror
            </form>

            @if(session('error'))
            <div class="ta-alert error" style="margin-top:16px; margin-bottom:0">
                <span class="ta-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i>
                <div>{{ session('error') }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Resultado --}}
    @isset($turno)
    <div class="ta-card">
        {{-- Header del resultado --}}
        <div style="padding:20px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px">
            <div>
                <div style="font-family:'Oswald',sans-serif; font-size:.72rem; color:var(--muted); letter-spacing:.1em; text-transform:uppercase; margin-bottom:4px">Vehículo</div>
                <div style="font-family:'Oswald',sans-serif; font-size:1.2rem; color:var(--navy)">
                    {{ $turno->vehiculo->marca->nombre }} {{ $turno->vehiculo->modelo->nombre }} {{ $turno->vehiculo->anio }}
                </div>
                <div style="font-size:.82rem; color:var(--muted)">Patente: <span style="font-family:'Oswald',sans-serif; color:var(--accent)">{{ $turno->vehiculo->patente }}</div>
            </div>
            <div style="text-align:right">
                <div class="nro-seguimiento" style="font-size:1.1rem; margin-bottom:6px">{{ $turno->numero_seguimiento }}</div>
                <span class="ta-badge badge-{{ $turno->estado }}">{{ $turno->etiquetaEstado() }}
            </div>
        </div>

        {{-- Estado visual del progreso --}}
        @php
        $estados = ['ingresado','en_diagnostico','en_reparacion','finalizado','entregado'];
        $etiquetas = ['Ingresado','En Diagnóstico','En Reparación','Finalizado','Entregado'];
        $estadoIngreso = $ingreso?->estado ?? null;
        $estadoActual = $estadoIngreso ? array_search($estadoIngreso, $estados) : -1;
        @endphp

        @if($ingreso)
        <div style="padding:20px 24px; border-bottom:1px solid var(--border)">
            <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:14px">Progreso de la reparación</div>
            <div style="display:flex; align-items:center; gap:0">
                @foreach($estados as $i => $est)
                <div style="flex:1; text-align:center">
                    <div style="width:32px; height:32px; border-radius:50%; margin:0 auto 6px; display:flex; align-items:center; justify-content:center; font-size:.78rem; font-weight:700; transition:all .3s;
                        background:{{ $i <= $estadoActual ? 'var(--blue)' : 'var(--light)' }};
                        color:{{ $i <= $estadoActual ? 'white' : 'var(--muted)' }}">
                        {{ $i < $estadoActual ? '✓' : ($i + 1) }}
                    </div>
                    <div style="font-size:.68rem; {{ $i === $estadoActual ? 'color:var(--blue); font-weight:600' : ($i < $estadoActual ? 'color:var(--ok)' : 'color:var(--muted)') }}; line-height:1.2">
                        {{ $etiquetas[$i] }}
                    </div>
                </div>
                @if($i < count($estados)-1)
                <div style="height:2px; width:20px; flex-shrink:0; margin-bottom:22px;
                    background:{{ $i < $estadoActual ? 'var(--blue)' : 'var(--border)' }}"></div>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        {{-- Datos del turno --}}
        <div style="padding:18px 24px; border-bottom:1px solid var(--border)">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; font-size:.87rem">
                <div>
                    <div style="color:var(--muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.07em; margin-bottom:3px">Turno programado</div>
                    <div style="font-weight:600; color:var(--navy)">{{ $turno->fecha_hora_turno->format('d/m/Y') }} {{ $turno->fecha_hora_turno->format('H:i') }} hs</div>
                </div>
                <div>
                    <div style="color:var(--muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.07em; margin-bottom:3px">Tipo de servicio</div>
                    <div style="font-weight:600; color:var(--navy)">{{ ucfirst(str_replace('_',' ',$turno->tipo_servicio)) }}</div>
                </div>
                @if($turno->mecanico)
                <div>
                    <div style="color:var(--muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.07em; margin-bottom:3px">Mecánico asignado</div>
                    <div style="font-weight:600; color:var(--navy)">{{ $turno->mecanico->nombreCompleto() }}</div>
                </div>
                @endif
                @if($ingreso)
                <div>
                    <div style="color:var(--muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.07em; margin-bottom:3px">Fecha de ingreso</div>
                    <div style="font-weight:600; color:var(--navy)">{{ $ingreso->fecha_ingreso->format('d/m/Y H:i') }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Trabajos realizados --}}
        @if($ingreso && $ingreso->trabajos->isNotEmpty())
        <div style="padding:18px 24px">
            <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:12px">Trabajos realizados</div>
            @foreach($ingreso->trabajos as $t)
            <div style="padding:14px; background:var(--card); border:1px solid var(--border); border-radius:9px; margin-bottom:10px">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px">
                    <div style="font-weight:600; font-size:.9rem; color:var(--navy)">{{ ucfirst(str_replace('_',' ',$t->tipo_servicio)) }}</div>
                    <span class="ta-badge badge-{{ $t->estado }}">{{ $t->etiquetaEstado() }}
                </div>
                <div style="font-size:.84rem; color:var(--text); margin-bottom:8px">{{ $t->descripcion_trabajo }}</div>
                @if($t->repuestos->isNotEmpty())
                <div style="font-size:.78rem; color:var(--muted)">
                    <strong>Repuestos:</strong>
                    {{ $t->repuestos->map(fn($r) => $r->nombre . ' ×' . $r->pivot->cantidad)->implode(', ') }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endisset

    {{-- Ayuda --}}
    <div style="background:rgba(46,141,255,.06); border:1px solid rgba(46,141,255,.15); border-radius:10px; padding:14px 18px; margin-top:16px; font-size:.84rem; color:var(--muted)">
        <i class="bi bi-info-circle" style="color:var(--blue); margin-right:6px"></i>
        <strong>¿Dónde encontrás tu número de seguimiento?</strong>
        Se generó al confirmar tu turno. También lo podés ver en "Mis Turnos" si tenés cuenta.
        Para consultas llamá al <strong>(376) 000-0000</strong>.
    </div>
</div>
@endsection
