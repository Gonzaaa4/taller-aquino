{{-- AGENDA --}}
@extends('layouts.app')
@section('title', 'Agenda Semanal')
@section('topbar-title', '<span>Agenda</span> Semanal')
@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Operaciones</div>
            <h1 class="page-title">Agenda Semanal</h1>
            <p class="page-subtitle">{{ $semana->locale('es')->isoFormat('D [de] MMMM') }} — {{ $semana->copy()->endOfWeek()->locale('es')->isoFormat('D [de] MMMM, YYYY') }}</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap">
            <a href="{{ route('admin.turnos.agenda', ['semana' => $semana->copy()->subWeek()->format('Y-m-d')]) }}" class="btn-secondary-ta">
                <i class="bi bi-chevron-left"></i> Anterior
            </a>
            <a href="{{ route('admin.turnos.agenda') }}" class="btn-secondary-ta">Hoy</a>
            <a href="{{ route('admin.turnos.agenda', ['semana' => $semana->copy()->addWeek()->format('Y-m-d')]) }}" class="btn-secondary-ta">
                Siguiente <i class="bi bi-chevron-right"></i>
            </a>
            <a href="{{ route('admin.turnos.solicitar') }}" class="btn-primary-ta">
                <i class="bi bi-plus-circle"></i> Nuevo Turno
            </a>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(7,1fr); gap:12px">
    @php
        for ($i = 0; $i < 7; $i++) {
            $dia = $semana->copy()->addDays($i);
            $clave = $dia->format('Y-m-d');
            $turnosDia = $turnos[$clave] ?? collect();
            $esHoy = $dia->isToday();
    @endphp
    <div class="ta-card" style="{{ $esHoy ? 'border-color:var(--accent); box-shadow:0 0 0 2px rgba(46,141,255,.15)' : '' }}">
        <div class="ta-card-header" style="{{ $esHoy ? 'background:var(--blue); color:white' : '' }}">
            <div>
                <div style="font-family:'Oswald',sans-serif; font-size:.82rem; letter-spacing:.06em; {{ $esHoy ? 'color:white' : 'color:var(--navy)' }}">
                    {{ strtoupper($dia->locale('es')->isoFormat('ddd')) }}
                </div>
                <div style="font-size:1.4rem; font-family:'Oswald',sans-serif; line-height:1; {{ $esHoy ? 'color:white' : 'color:var(--navy)' }}">
                    {{ $dia->format('d') }}
                </div>
            </div>
            @if($turnosDia->count() > 0)
            <span style="background:{{ $esHoy ? 'rgba(255,255,255,.25)' : 'var(--accent)' }}; color:white; border-radius:20px; padding:2px 8px; font-size:.7rem; font-weight:700">
                {{ $turnosDia->count() }}
            </span>
            @endif
        </div>
        <div>
            @forelse($turnosDia as $turno)
            <a href="{{ route('admin.turnos.show', $turno) }}"
               style="display:block; padding:10px 14px; border-bottom:1px solid rgba(192,211,232,.4); text-decoration:none; transition:background .15s"
               onmouseover="this.style.background='var(--card)'" onmouseout="this.style.background=''">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:4px; margin-bottom:3px">
                    <span style="font-family:'Oswald',sans-serif; font-size:.85rem; color:var(--navy)">{{ $turno->fecha_hora_turno->format('H:i') }}</span>
                    <span class="ta-badge badge-{{ $turno->estado }}" style="font-size:.6rem; padding:2px 7px">{{ $turno->etiquetaEstado() }}</span>
                </div>
                <div style="font-size:.78rem; font-weight:600; color:var(--text); overflow:hidden; white-space:nowrap; text-overflow:ellipsis">
                    {{ $turno->cliente->nombreCompleto() }}
                </div>
                <div style="font-size:.7rem; color:var(--muted)">
                    {{ $turno->vehiculo->patente }}
                </div>
            </a>
            @empty
            <div style="padding:20px 14px; text-align:center; color:var(--muted); font-size:.78rem; opacity:.6">
                Sin turnos
            </div>
            @endforelse
        </div>
    </div>
    @php } @endphp
</div>
@endsection
