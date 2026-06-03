@extends('layouts.app')
@section('title', 'Órdenes de Trabajo')
@section('topbar-title', '<span>Órdenes</span> de Trabajo')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Operaciones</div>
            <h1 class="page-title">Órdenes de Trabajo</h1>
            <p class="page-subtitle">Vehículos ingresados al taller</p>
        </div>
        <button class="btn-primary-ta" onclick="document.getElementById('modalIngreso').style.display='flex'">
            <i class="bi bi-box-arrow-in-right"></i> Registrar Ingreso
        </button>
    </div>
</div>

{{-- Filtros --}}
<div class="ta-card" style="margin-bottom:20px">
    <div class="ta-card-body" style="padding:14px 20px">
        <form style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end">
            <div>
                <label class="ta-label">Estado</label>
                <select name="estado" class="ta-input ta-select" style="width:180px">
                    <option value="">Todos los estados</option>
                    @foreach(['ingresado','en_diagnostico','en_reparacion','finalizado','entregado'] as $e)
                        <option value="{{ $e }}" {{ request('estado') === $e ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_',' ',$e)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:8px; align-items:flex-end">
                <button type="submit" class="btn-primary-ta" style="height:40px">Filtrar</button>
                <a href="{{ route('admin.trabajos.index') }}" class="btn-secondary-ta" style="height:40px">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="ta-card">
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>Vehículo</th>
                    <th>Cliente</th>
                    <th>Ingreso</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ingresos as $ingreso)
                <tr>
                    <td>
                        <div style="font-weight:600; color:var(--navy)">
                            {{ $ingreso->vehiculo->marca->nombre }} {{ $ingreso->vehiculo->modelo->nombre }}
                        </div>
                        <div style="font-family:'Oswald',sans-serif; font-size:.8rem; color:var(--accent); letter-spacing:.06em">
                            {{ $ingreso->vehiculo->patente }}
                        </div>
                    </td>
                    <td>
                        <div style="font-size:.88rem; font-weight:600; color:var(--navy)">{{ $ingreso->cliente->nombreCompleto() }}</div>
                        <div style="font-size:.76rem; color:var(--muted)">{{ $ingreso->cliente->telefono }}</div>
                    </td>
                    <td>
                        <div style="font-size:.86rem; color:var(--navy)">{{ $ingreso->fecha_ingreso->format('d/m/Y') }}</div>
                        <div style="font-size:.74rem; color:var(--muted)">{{ $ingreso->fecha_ingreso->format('H:i') }} hs</div>
                    </td>
                    <td style="font-size:.84rem; color:var(--muted); max-width:200px">
                        {{ Str::limit($ingreso->descripcion_problema ?? '—', 50) }}
                    </td>
                    <td>
                        <span class="ta-badge badge-{{ str_replace('_','-',$ingreso->estado) }}">
                            {{ $ingreso->etiquetaEstado() }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.trabajos.show', $ingreso) }}" class="btn-secondary-ta" style="padding:7px 14px; font-size:.82rem">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:48px; color:var(--muted)">
                        <i class="bi bi-wrench-adjustable" style="font-size:2rem; display:block; margin-bottom:12px; opacity:.3"></i>
                        No hay vehículos ingresados actualmente
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($ingresos->hasPages())
    <div style="padding:16px 20px; border-top:1px solid var(--border)">{{ $ingresos->withQueryString()->links() }}</div>
    @endif
</div>

{{-- Modal registrar ingreso --}}
<div id="modalIngreso" style="display:none; position:fixed; inset:0; background:rgba(11,28,46,.65); z-index:500; align-items:center; justify-content:center; padding:20px">
    <div style="background:white; border-radius:14px; width:100%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.3)">
        <div style="padding:18px 22px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; background:white; z-index:1">
            <div style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--navy); letter-spacing:.04em">
                <i class="bi bi-box-arrow-in-right" style="color:var(--blue); margin-right:8px"></i>REGISTRAR INGRESO DE VEHÍCULO
            </div>
            <button onclick="document.getElementById('modalIngreso').style.display='none'"
                style="background:none; border:none; font-size:1.3rem; cursor:pointer; color:var(--muted); line-height:1">×</button>
        </div>
        <form method="POST" action="{{ route('admin.trabajos.ingreso') }}">
            @csrf
            <div style="padding:22px; display:grid; grid-template-columns:1fr 1fr; gap:14px">
                <div style="grid-column:span 2">
                    <label class="ta-label">Turno asociado <span style="color:var(--muted); font-weight:400">(opcional)</span></label>
                    <select name="turno_id" id="turnoSelect" class="ta-input ta-select" onchange="cargarDatosTurno(this.value)">
                        <option value="">Sin turno / ingreso directo</option>
                        @foreach(\App\Models\Turno::whereIn('estado',['pendiente','confirmado'])->with(['cliente','vehiculo'])->orderBy('fecha_hora_turno')->get() as $t)
                            <option value="{{ $t->id }}" data-cliente="{{ $t->cliente_id }}" data-vehiculo="{{ $t->vehiculo_id }}">
                                {{ $t->numero_seguimiento }} — {{ $t->cliente->nombreCompleto() }} — {{ $t->fecha_hora_turno->format('d/m/Y H:i') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ta-label">Cliente <span class="req">*</span></label>
                    <select name="cliente_id" id="clienteSelect" class="ta-input ta-select" required>
                        <option value="">Seleccioná...</option>
                        @foreach(\App\Models\User::where('rol','cliente')->orderBy('name')->get() as $c)
                            <option value="{{ $c->id }}">{{ $c->nombreCompleto() }} — DNI {{ $c->dni }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ta-label">Vehículo <span class="req">*</span></label>
                    <select name="vehiculo_id" id="vehiculoSelect" class="ta-input ta-select" required>
                        <option value="">Seleccioná...</option>
                        @foreach(\App\Models\Vehiculo::with(['marca','modelo'])->get() as $v)
                            <option value="{{ $v->id }}">{{ $v->marca->nombre }} {{ $v->modelo->nombre }} — {{ $v->patente }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:span 2">
                    <label class="ta-label">Kilometraje actual <span class="req">*</span></label>
                    <input type="number" name="kilometraje_ingreso" class="ta-input" min="0" required placeholder="Ej: 85000">
                </div>
                <div style="grid-column:span 2">
                    <label class="ta-label">Descripción del problema</label>
                    <textarea name="descripcion_problema" class="ta-input ta-textarea"
                        placeholder="Describí el problema reportado por el cliente..."></textarea>
                </div>
            </div>
            <div style="padding:16px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px">
                <button type="button" class="btn-secondary-ta" onclick="document.getElementById('modalIngreso').style.display='none'">Cancelar</button>
                <button type="submit" class="btn-primary-ta"><i class="bi bi-check-circle"></i> Registrar Ingreso</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function cargarDatosTurno(id) {
    if (!id) return;
    const opt = document.querySelector(`#turnoSelect option[value="${id}"]`);
    if (opt) {
        document.getElementById('clienteSelect').value = opt.dataset.cliente;
        document.getElementById('vehiculoSelect').value = opt.dataset.vehiculo;
    }
}
</script>
@endpush
@endsection
