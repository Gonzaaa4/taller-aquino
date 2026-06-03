@extends('layouts.app')
@section('title', 'Mis Turnos')

@section('breadcrumb')
    <li class="breadcrumb-item active">Mis Turnos</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Mis Turnos</h4>
    <a href="{{ route('cliente.turnos.solicitar') }}" class="btn btn-taller">
        <i class="bi bi-plus-circle me-1"></i> Solicitar Turno
    </a>
</div>

@forelse($turnos as $turno)
<div class="card mb-3">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-2 text-center border-end">
                <div class="text-muted small">{{ $turno->fecha_hora_turno->format('d MMM') }}</div>
                <div class="fw-bold fs-5">{{ $turno->fecha_hora_turno->format('d') }}</div>
                <div class="text-muted small">{{ $turno->fecha_hora_turno->format('H:i') }} hs</div>
            </div>
            <div class="col-md-5 px-4">
                <div class="fw-semibold">
                    {{ $turno->vehiculo->marca->nombre }} {{ $turno->vehiculo->modelo->nombre }} {{ $turno->vehiculo->anio }}
                </div>
                <div class="text-muted small">Patente: {{ $turno->vehiculo->patente }}</div>
                <div class="text-muted small">{{ ucfirst(str_replace('_',' ',$turno->tipo_servicio)) }}</div>
            </div>
            <div class="col-md-3">
                <div class="font-mono text-danger small mb-1">{{ $turno->numero_seguimiento }}</div>
                <span class="badge badge-{{ $turno->estado }} rounded-pill px-3">
                    {{ $turno->etiquetaEstado() }}
                </span>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ route('cliente.consultar-estado') }}?numero_seguimiento={{ $turno->numero_seguimiento }}"
                   class="btn btn-sm btn-outline-primary mb-1 w-100">
                    <i class="bi bi-search me-1"></i>Consultar
                </a>
                @if($turno->puedeSerCancelado())
                <button class="btn btn-sm btn-outline-danger w-100"
                    data-bs-toggle="modal" data-bs-target="#cancelarModal"
                    data-id="{{ $turno->id }}"
                    data-nro="{{ $turno->numero_seguimiento }}"
                    data-tardia="{{ $turno->cancelacionEsTardia() ? '1' : '0' }}">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
@empty
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i>
        <h5>No tenés turnos registrados</h5>
        <p class="text-muted">Solicitá tu primer turno para comenzar.</p>
        <a href="{{ route('cliente.turnos.solicitar') }}" class="btn btn-taller">
            <i class="bi bi-plus-circle me-1"></i> Solicitar Turno
        </a>
    </div>
</div>
@endforelse

@if($turnos->hasPages())
    <div class="mt-3">{{ $turnos->links() }}</div>
@endif

{{-- Modal cancelar --}}
<div class="modal fade" id="cancelarModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title fw-bold">Cancelar Turno</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="cancelarForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="small mb-2">¿Cancelar el turno <strong id="modalNroSeg"></strong>?</p>
                    <div id="alertaTardia" class="alert alert-warning py-2 small d-none">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Esta cancelación es con <strong>menos de 48 hs</strong> de anticipación.
                    </div>
                    <div>
                        <label class="form-label small fw-semibold">Motivo (opcional)</label>
                        <textarea name="motivo" class="form-control form-control-sm" rows="2"
                            placeholder="Indicá el motivo de la cancelación..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">No, volver</button>
                    <button type="submit" class="btn btn-sm btn-danger">Sí, cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-taller { background:#C0392B; color:#fff; border:none; }
    .btn-taller:hover { background:#96281B; color:#fff; }
    .font-mono { font-family: 'DM Mono', monospace; }
</style>

@push('scripts')
<script>
document.getElementById('cancelarModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('modalNroSeg').textContent = btn.dataset.nro;
    document.getElementById('cancelarForm').action = `/cliente/turnos/${btn.dataset.id}/cancelar`;
    document.getElementById('alertaTardia').classList.toggle('d-none', btn.dataset.tardia !== '1');
});
</script>
@endpush
@endsection
