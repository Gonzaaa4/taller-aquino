@extends('layouts.app')
@section('title', 'Mis Turnos')
@section('topbar-title', 'Mis Turnos')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Portal del Cliente</div>
            <h1 class="page-title">Mis Turnos</h1>
            <p class="page-subtitle">Historial de todos tus turnos</p>
        </div>
        <a href="{{ route('cliente.turnos.solicitar') }}" class="btn-primary-ta">
            <i class="bi bi-plus-circle"></i> Solicitar Turno
        </a>
    </div>
</div>

@forelse($turnos as $turno)
<div class="ta-card" style="margin-bottom:14px">
    <div style="display:flex; align-items:center; gap:0; flex-wrap:wrap">

        {{-- Fecha --}}
        <div style="padding:20px 24px; border-right:1px solid var(--border); text-align:center; min-width:90px">
            <div style="font-family:'Oswald',sans-serif; font-size:1.8rem; color:var(--navy); line-height:1">
                {{ $turno->fecha_hora_turno->format('d') }}
            </div>
            <div style="font-size:.75rem; color:var(--muted); text-transform:uppercase">
                {{ $turno->fecha_hora_turno->locale('es')->isoFormat('MMM YYYY') }}
            </div>
            <div style="font-size:.8rem; color:var(--accent); font-weight:600; margin-top:2px">
                {{ $turno->fecha_hora_turno->format('H:i') }} hs
            </div>
        </div>

        {{-- Info --}}
        <div style="flex:1; padding:16px 20px; min-width:200px">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px">
                <div style="font-weight:600; color:var(--navy)">
                    {{ $turno->vehiculo->marca->nombre }} {{ $turno->vehiculo->modelo->nombre }} {{ $turno->vehiculo->anio }}
                </div>
                <span class="ta-badge badge-{{ $turno->estado }}">{{ $turno->etiquetaEstado() }}
            </div>
            <div style="font-size:.84rem; color:var(--muted); margin-bottom:4px">
                <i class="bi bi-car-front" style="margin-right:4px"></i>{{ $turno->vehiculo->patente }}
                &nbsp;·&nbsp;
                {{ ucfirst(str_replace('_',' ',$turno->tipo_servicio)) }}
            </div>
            @if($turno->mecanico)
            <div style="font-size:.8rem; color:var(--muted)">
                <i class="bi bi-person-gear" style="margin-right:4px"></i>{{ $turno->mecanico->nombreCompleto() }}
            </div>
            @endif
            @if($turno->observaciones)
            <div style="font-size:.8rem; color:var(--muted); margin-top:4px; font-style:italic">
                "{{ Str::limit($turno->observaciones, 60) }}"
            </div>
            @endif
        </div>

        {{-- Nro seguimiento + acciones --}}
        <div style="padding:16px 20px; text-align:right; border-left:1px solid var(--border)">
            <div style="font-family:'Oswald',sans-serif; font-size:.95rem; color:var(--accent); letter-spacing:.08em; margin-bottom:12px">
                {{ $turno->numero_seguimiento }}
            </div>
            <div style="display:flex; gap:8px; justify-content:flex-end; flex-wrap:wrap">
                <a href="{{ route('cliente.consultar-estado') }}?numero_seguimiento={{ $turno->numero_seguimiento }}"
                   class="btn-secondary-ta" style="padding:6px 12px; font-size:.8rem">
                    <i class="bi bi-search"></i> Consultar
                </a>
                @if($turno->puedeSerCancelado())
                <button class="btn-danger-ta" style="padding:6px 12px; font-size:.8rem"
                    onclick="abrirCancelar({{ $turno->id }}, '{{ $turno->numero_seguimiento }}', {{ $turno->cancelacionEsTardia() ? 'true' : 'false' }})">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
@empty
<div class="ta-card">
    <div style="text-align:center; padding:64px 20px; color:var(--muted)">
        <i class="bi bi-calendar-x" style="font-size:3rem; display:block; margin-bottom:16px; opacity:.3"></i>
        <div style="font-family:'Oswald',sans-serif; font-size:1.2rem; color:var(--navy); margin-bottom:8px">No tenés turnos registrados</div>
        <p style="font-size:.9rem; margin-bottom:20px">Solicitá tu primer turno para comenzar.</p>
        <a href="{{ route('cliente.turnos.solicitar') }}" class="btn-primary-ta" style="display:inline-flex">
            <i class="bi bi-plus-circle"></i> Solicitar Turno
        </a>
    </div>
</div>
@endforelse

@if($turnos->hasPages())
<div style="margin-top:16px">{{ $turnos->links() }}</div>
@endif

{{-- Modal cancelar --}}
<div id="modalCancelar" style="display:none; position:fixed; inset:0; background:rgba(11,28,46,.65); z-index:500; align-items:center; justify-content:center; padding:20px">
    <div style="background:white; border-radius:14px; width:100%; max-width:420px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.3)">
        <div style="padding:18px 22px; border-bottom:1px solid var(--border); background:rgba(217,48,37,.06)">
            <div style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--error); letter-spacing:.04em">
                <i class="bi bi-x-circle" style="margin-right:8px"></i>CANCELAR TURNO
            </div>
        </div>
        <form id="cancelarForm" method="POST">
            @csrf
            <div style="padding:22px">
                <p style="font-size:.9rem; color:var(--text); margin-bottom:12px">
                    ¿Cancelar el turno <strong id="nroSeg" style="font-family:'Oswald',sans-serif; color:var(--accent)"></strong>?
                </p>
                <div id="alertaTardia" style="display:none; background:rgba(230,126,0,.08); border:1px solid rgba(230,126,0,.25); border-radius:8px; padding:10px 14px; font-size:.84rem; color:#a85e00; margin-bottom:14px">
                    <i class="bi bi-exclamation-triangle" style="margin-right:6px"></i>
                    Esta cancelación es con <strong>menos de 48 hs</strong> de anticipación.
                </div>
                <div style="background:rgba(217,48,37,.06); border:1px solid rgba(217,48,37,.15); border-radius:8px; padding:10px 14px; font-size:.82rem; color:var(--error); margin-bottom:16px">
                    <i class="bi bi-info-circle" style="margin-right:6px"></i>
                    Recordá que tenés un máximo de <strong>2 cancelaciones por mes</strong>.
                </div>
                <label class="ta-label">Motivo (opcional)</label>
                <textarea name="motivo" class="ta-input ta-textarea" rows="2" placeholder="Indicá el motivo de la cancelación..."></textarea>
            </div>
            <div style="padding:14px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px">
                <button type="button" class="btn-secondary-ta" onclick="document.getElementById('modalCancelar').style.display='none'">
                    No, volver
                </button>
                <button type="submit" class="btn-danger-ta" style="background:var(--error); color:white; border-color:var(--error)">
                    Sí, cancelar turno
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function abrirCancelar(id, nro, tardia) {
    document.getElementById('nroSeg').textContent = nro;
    document.getElementById('cancelarForm').action = `/cliente/turnos/${id}/cancelar`;
    document.getElementById('alertaTardia').style.display = tardia ? 'block' : 'none';
    document.getElementById('modalCancelar').style.display = 'flex';
}
</script>
@endpush
@endsection
