@extends('layouts.app')
@section('title', 'Turno Confirmado')
@section('topbar-title', 'Turno Confirmado')

@section('content')
<div style="max-width:600px; margin:0 auto">

    {{-- Icono de éxito --}}
    <div style="text-align:center; padding:40px 20px 32px">
        <div style="width:80px; height:80px; border-radius:50%; background:rgba(15,138,74,.12); display:flex; align-items:center; justify-content:center; margin:0 auto 20px">
            <i class="bi bi-check-circle-fill" style="font-size:2.8rem; color:var(--ok)"></i>
        </div>
        <h1 style="font-family:'Oswald',sans-serif; font-size:1.8rem; color:var(--navy); letter-spacing:.04em; margin-bottom:8px">
            ¡TURNO SOLICITADO!
        </h1>
        <p style="font-size:.95rem; color:var(--muted)">
            Tu solicitud fue registrada correctamente. Te confirmamos el turno a la brevedad.
        </p>
    </div>

    {{-- Card con número de seguimiento --}}
    <div class="ta-card" style="margin-bottom:20px">
        <div style="padding:28px; text-align:center; border-bottom:1px solid var(--border)">
            <div style="font-size:.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:.1em; margin-bottom:10px">
                Tu número de seguimiento
            </div>
            <div style="font-family:'Oswald',sans-serif; font-size:2.2rem; color:var(--accent); letter-spacing:.1em;
                background:rgba(46,141,255,.08); border:2px dashed rgba(46,141,255,.3);
                border-radius:12px; padding:16px 28px; display:inline-block; margin-bottom:10px">
                {{ $turno->numero_seguimiento }}
            </div>
            <p style="font-size:.82rem; color:var(--muted); margin:0">
                Guardá este número para consultar el estado de tu reparación
            </p>
        </div>

        {{-- Detalle del turno --}}
        <div style="padding:22px">
            <div style="font-family:'Oswald',sans-serif; font-size:.75rem; color:var(--muted); letter-spacing:.1em; text-transform:uppercase; margin-bottom:14px">
                Detalle del turno
            </div>
            <div style="display:grid; gap:12px">
                <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 14px; background:var(--card); border-radius:8px; border:1px solid var(--border)">
                    <div style="display:flex; align-items:center; gap:10px; color:var(--muted); font-size:.86rem">
                        <i class="bi bi-calendar-event" style="color:var(--blue)"></i> Fecha y hora
                    </div>
                    <div style="font-weight:600; color:var(--navy); font-size:.9rem">
                        {{ $turno->fecha_hora_turno->locale('es')->isoFormat('dddd D [de] MMMM, YYYY') }}
                        a las {{ $turno->fecha_hora_turno->format('H:i') }} hs
                    </div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 14px; background:var(--card); border-radius:8px; border:1px solid var(--border)">
                    <div style="display:flex; align-items:center; gap:10px; color:var(--muted); font-size:.86rem">
                        <i class="bi bi-car-front" style="color:var(--blue)"></i> Vehículo
                    </div>
                    <div style="font-weight:600; color:var(--navy); font-size:.9rem">
                        {{ $turno->vehiculo->marca->nombre }} {{ $turno->vehiculo->modelo->nombre }}
                        <span style="font-family:'Oswald',sans-serif; color:var(--accent); letter-spacing:.06em">
                            {{ $turno->vehiculo->patente }}
                        </span>
                    </div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 14px; background:var(--card); border-radius:8px; border:1px solid var(--border)">
                    <div style="display:flex; align-items:center; gap:10px; color:var(--muted); font-size:.86rem">
                        <i class="bi bi-tools" style="color:var(--blue)"></i> Servicio
                    </div>
                    <div style="font-weight:600; color:var(--navy); font-size:.9rem">
                        {{ ucfirst(str_replace('_', ' ', $turno->tipo_servicio)) }}
                    </div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 14px; background:var(--card); border-radius:8px; border:1px solid var(--border)">
                    <div style="display:flex; align-items:center; gap:10px; color:var(--muted); font-size:.86rem">
                        <i class="bi bi-clock" style="color:var(--blue)"></i> Estado
                    </div>
                    <span class="ta-badge badge-{{ $turno->estado }}">{{ $turno->etiquetaEstado() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Aviso de cancelación --}}
    <div style="background:rgba(230,126,0,.07); border:1px solid rgba(230,126,0,.2); border-radius:10px; padding:14px 18px; margin-bottom:24px; font-size:.84rem; color:#a85e00">
        <i class="bi bi-exclamation-triangle" style="margin-right:6px"></i>
        <strong>Recordá:</strong> Podés cancelar tu turno hasta <strong>48 horas antes</strong>.
        Tenés un máximo de <strong>2 cancelaciones por mes</strong>.
    </div>

    {{-- Botones --}}
    <div style="display:flex; gap:12px; flex-wrap:wrap">
        <a href="{{ route('cliente.turnos.index') }}" class="btn-primary-ta" style="flex:1; justify-content:center">
            <i class="bi bi-calendar-check"></i> Ver mis turnos
        </a>
        <a href="{{ route('cliente.consultar-estado') }}?numero_seguimiento={{ $turno->numero_seguimiento }}"
           class="btn-secondary-ta" style="flex:1; justify-content:center">
            <i class="bi bi-search"></i> Consultar estado
        </a>
        <a href="{{ route('cliente.dashboard') }}" class="btn-secondary-ta" style="flex:1; justify-content:center">
            <i class="bi bi-house"></i> Volver al inicio
        </a>
    </div>
</div>
@endsection