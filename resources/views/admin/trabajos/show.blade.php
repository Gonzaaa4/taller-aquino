@extends('layouts.app')
@section('title', 'Orden de Trabajo')
@section('topbar-title', 'Orden de Trabajo')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Operaciones · Órdenes de Trabajo</div>
            <h1 class="page-title">
                {{ $ingreso->vehiculo->marca->nombre }} {{ $ingreso->vehiculo->modelo->nombre }}
                <span style="font-size:1.2rem; color:var(--accent); font-family:'Oswald',sans-serif; letter-spacing:.06em">
                    {{ $ingreso->vehiculo->patente }}
                
            </h1>
            <p class="page-subtitle">Ingresó el {{ $ingreso->fecha_ingreso->format('d/m/Y H:i') }} hs</p>
        </div>
        <div style="display:flex; gap:10px; align-items:center">
            <span class="ta-badge badge-{{ str_replace('_','-',$ingreso->estado) }}" style="font-size:.85rem; padding:6px 14px">
                {{ $ingreso->etiquetaEstado() }}
            
            <a href="{{ route('admin.trabajos.index') }}" class="btn-secondary-ta">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:start">

    {{-- Columna principal --}}
    <div>
        {{-- Info del cliente y vehículo --}}
        <div class="ta-card" style="margin-bottom:20px">
            <div class="ta-card-header">
                <div class="ta-card-title"><i class="bi bi-info-circle" style="color:var(--blue)"></i> Datos del Ingreso</div>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px">
                <div>
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Cliente</div>
                    <div style="font-weight:600; color:var(--navy)">{{ $ingreso->cliente->nombreCompleto() }}</div>
                    <div style="font-size:.82rem; color:var(--muted)">{{ $ingreso->cliente->telefono }}</div>
                    <div style="font-size:.82rem; color:var(--muted)">{{ $ingreso->cliente->email }}</div>
                </div>
                <div>
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Vehículo</div>
                    <div style="font-weight:600; color:var(--navy)">{{ $ingreso->vehiculo->marca->nombre }} {{ $ingreso->vehiculo->modelo->nombre }} {{ $ingreso->vehiculo->anio }}</div>
                    <div style="font-size:.82rem; color:var(--muted)">Patente: <span style="font-family:'Oswald',sans-serif; color:var(--accent)">{{ $ingreso->vehiculo->patente }}</div>
                    <div style="font-size:.82rem; color:var(--muted)">KM ingreso: {{ number_format($ingreso->kilometraje_ingreso) }}</div>
                </div>
                <div>
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Turno asociado</div>
                    @if($ingreso->turno)
                    <div style="font-family:'Oswald',sans-serif; color:var(--accent); font-size:.95rem; letter-spacing:.06em">{{ $ingreso->turno->numero_seguimiento }}</div>
                    <div style="font-size:.82rem; color:var(--muted)">{{ ucfirst(str_replace('_',' ',$ingreso->turno->tipo_servicio)) }}</div>
                    @else
                    <div style="font-size:.84rem; color:var(--muted); font-style:italic">Ingreso directo (sin turno)</div>
                    @endif
                </div>
            </div>
            @if($ingreso->descripcion_problema)
            <div style="padding:0 20px 18px">
                <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px">Problema reportado</div>
                <div style="background:var(--card); border:1px solid var(--border); border-radius:8px; padding:12px 14px; font-size:.88rem; color:var(--text)">
                    {{ $ingreso->descripcion_problema }}
                </div>
            </div>
            @endif
        </div>

        {{-- Trabajos realizados --}}
        <div class="ta-card" style="margin-bottom:20px">
            <div class="ta-card-header">
                <div class="ta-card-title"><i class="bi bi-clipboard-check" style="color:var(--blue)"></i> Trabajos Registrados</div>
            </div>

            @forelse($ingreso->trabajos as $trabajo)
            <div style="padding:18px 20px; border-bottom:1px solid var(--border)">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px">
                    <div>
                        <div style="font-weight:600; color:var(--navy)">{{ ucfirst(str_replace('_',' ',$trabajo->tipo_servicio)) }}</div>
                        <div style="font-size:.8rem; color:var(--muted)">
                            <i class="bi bi-person-gear"></i> {{ $trabajo->mecanico->nombreCompleto() }}
                            · {{ $trabajo->fecha_trabajo->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <span class="ta-badge badge-{{ $trabajo->estado }}">{{ $trabajo->etiquetaEstado() }}
                </div>

                <div style="font-size:.88rem; color:var(--text); margin-bottom:12px; padding:10px 12px; background:var(--card); border-radius:7px; border:1px solid var(--border)">
                    {{ $trabajo->descripcion_trabajo }}
                </div>

                @if($trabajo->repuestos->isNotEmpty())
                <div style="margin-bottom:10px">
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px">Repuestos utilizados</div>
                    <div style="display:flex; flex-wrap:wrap; gap:6px">
                        @foreach($trabajo->repuestos as $rep)
                        <span style="background:rgba(46,141,255,.1); color:var(--blue); padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:600">
                            {{ $rep->nombre }} ×{{ $rep->pivot->cantidad }}
                            <span style="opacity:.7">${{ number_format($rep->pivot->subtotal, 0, ',', '.') }}
                        
                        @endforeach
                    </div>
                </div>
                @endif

                <div style="display:flex; gap:20px; font-size:.84rem">
                    <div>Mano de obra: <strong>${{ number_format($trabajo->costo_mano_obra, 2, ',', '.') }}</strong></div>
                    <div>Repuestos: <strong>${{ number_format($trabajo->costo_repuestos, 2, ',', '.') }}</strong></div>
                    <div style="color:var(--navy); font-weight:700">Total: ${{ number_format($trabajo->costo_total, 2, ',', '.') }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:40px; color:var(--muted)">
                <i class="bi bi-clipboard" style="font-size:2rem; display:block; margin-bottom:10px; opacity:.3"></i>
                No hay trabajos registrados aún
            </div>
            @endforelse

            {{-- Resumen total --}}
            @if($ingreso->trabajos->isNotEmpty())
            <div style="padding:14px 20px; background:var(--light); display:flex; justify-content:flex-end; gap:24px; font-size:.9rem">
                <div>Total mano de obra: <strong>${{ number_format($ingreso->trabajos->sum('costo_mano_obra'), 2, ',', '.') }}</strong></div>
                <div>Total repuestos: <strong>${{ number_format($ingreso->trabajos->sum('costo_repuestos'), 2, ',', '.') }}</strong></div>
                <div style="font-family:'Oswald',sans-serif; font-size:1.1rem; color:var(--navy)">
                    TOTAL: ${{ number_format($ingreso->trabajos->sum('costo_total'), 2, ',', '.') }}
                </div>
            </div>
            @endif
        </div>

        {{-- Formulario: registrar nuevo trabajo --}}
        @if(!in_array($ingreso->estado, ['finalizado','entregado']))
        <div class="ta-card" style="margin-bottom:20px">
            <div class="ta-card-header">
                <div class="ta-card-title"><i class="bi bi-plus-circle" style="color:var(--ok)"></i> Registrar Trabajo</div>
            </div>
            <form method="POST" action="{{ route('admin.trabajos.guardar-trabajo', $ingreso) }}" id="formTrabajo">
                @csrf
                <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:16px">
                    <div>
                        <label class="ta-label">Tipo de servicio <span class="req">*</label>
                        <select name="tipo_servicio" class="ta-input ta-select" required>
                            <option value="">Seleccioná...</option>
                            @foreach(['mantenimiento_preventivo'=>'Mantenimiento Preventivo','reparacion'=>'Reparación','diagnostico'=>'Diagnóstico','service'=>'Service','otros'=>'Otros'] as $val=>$label)
                            <option value="{{ $val }}" {{ ($ingreso->turno?->tipo_servicio === $val || old('tipo_servicio') === $val) ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        @if($ingreso->turno)
                        <div style="font-size:.74rem; color:var(--blue); margin-top:3px">
                            <i class="bi bi-info-circle"></i> Cargado desde el turno del cliente
                        </div>
                        @endif
                    </div>
                    <div>
                        <label class="ta-label">Estado <span class="req">*</label>
                        <select name="estado" class="ta-input ta-select" required>
                            <option value="en_proceso">En proceso</option>
                            <option value="finalizado">Finalizado</option>
                        </select>
                    </div>
                    <div>
                        <label class="ta-label">Costo mano de obra ($) <span class="req">*</label>
                        <input type="number" name="costo_mano_obra" class="ta-input" min="0" step="0.01" required placeholder="Ej: 5000">
                    </div>
                    <div style="grid-column:span 2">
                        <label class="ta-label">Descripción del trabajo <span class="req">* <span style="color:var(--muted); font-weight:400">(mínimo 20 caracteres)</label>
                        <textarea name="descripcion_trabajo" class="ta-input ta-textarea" minlength="20" required
                            placeholder="Describí detalladamente el trabajo realizado..."></textarea>
                    </div>
                </div>

                {{-- Repuestos utilizados --}}
                <div style="padding:0 20px 16px">
                    <div style="font-size:.82rem; color:var(--navy); font-weight:600; margin-bottom:10px">
                        Repuestos utilizados <span style="color:var(--muted); font-weight:400">(opcional)
                    </div>
                    <div id="repuestosContainer">
                        <div class="repuesto-row" style="display:grid; grid-template-columns:1fr 100px auto; gap:8px; margin-bottom:8px">
                            <select name="repuestos[0][id]" class="ta-input ta-select">
                                <option value="">Sin repuesto</option>
                                @foreach($repuestos as $rep)
                                <option value="{{ $rep->id }}">{{ $rep->nombre }} (Stock: {{ $rep->cantidad_stock }})</option>
                                @endforeach
                            </select>
                            <input type="number" name="repuestos[0][cantidad]" class="ta-input" min="1" placeholder="Cant." value="1">
                            <button type="button" onclick="this.closest('.repuesto-row').remove()"
                                style="background:none; border:1.5px solid var(--border); border-radius:8px; padding:0 10px; cursor:pointer; color:var(--error); font-size:1rem">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" onclick="agregarRepuesto()" class="btn-secondary-ta" style="font-size:.82rem; padding:6px 14px; margin-top:4px">
                        <i class="bi bi-plus"></i> Agregar repuesto
                    </button>
                </div>

                <div style="padding:14px 20px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px">
                    <button type="submit" class="btn-primary-ta">
                        <i class="bi bi-check-circle"></i> Guardar Trabajo
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>

    {{-- Columna lateral --}}
    <div>
        {{-- Acciones --}}
        <div class="ta-card" style="margin-bottom:16px">
            <div class="ta-card-header">
                <div class="ta-card-title"><i class="bi bi-gear"></i> Acciones</div>
            </div>
            <div style="padding:16px; display:flex; flex-direction:column; gap:10px">

                @if($ingreso->estado === 'finalizado' && !$ingreso->egreso)
                {{-- Registrar egreso --}}
                <button class="btn-ok-ta" style="width:100%; justify-content:center"
                    onclick="document.getElementById('modalEgreso').style.display='flex'">
                    <i class="bi bi-box-arrow-right"></i> Registrar Entrega
                </button>
                @endif

                @if($ingreso->egreso)
                <div style="text-align:center; padding:10px; background:rgba(15,138,74,.08); border-radius:8px; border:1px solid rgba(15,138,74,.2)">
                    <i class="bi bi-check-circle" style="color:var(--ok); font-size:1.3rem; display:block; margin-bottom:4px"></i>
                    <div style="font-size:.82rem; font-weight:600; color:var(--ok)">Vehículo Entregado</div>
                    <div style="font-size:.76rem; color:var(--muted)">{{ $ingreso->egreso->fecha_egreso->format('d/m/Y H:i') }}</div>
                </div>
                @endif

                @if($ingreso->turno)
                <a href="{{ route('admin.turnos.show', $ingreso->turno) }}" class="btn-secondary-ta" style="justify-content:center">
                    <i class="bi bi-calendar-check"></i> Ver Turno
                </a>
                @endif
            </div>
        </div>

        {{-- Timeline de estados --}}
        <div class="ta-card">
            <div class="ta-card-header">
                <div class="ta-card-title"><i class="bi bi-clock-history"></i> Progreso</div>
            </div>
            <div style="padding:16px">
                @php
                $estados = ['ingresado','en_diagnostico','en_reparacion','finalizado','entregado'];
                $estadoActual = array_search($ingreso->estado, $estados);
                $etiquetas = ['Ingresado','En Diagnóstico','En Reparación','Finalizado','Entregado'];
                @endphp
                @foreach($estados as $i => $est)
                <div style="display:flex; align-items:center; gap:12px; padding:8px 0; {{ $i < count($estados)-1 ? 'border-bottom:1px solid var(--border)' : '' }}">
                    <div style="width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;
                        background:{{ $i <= $estadoActual ? 'var(--blue)' : 'var(--light)' }};
                        color:{{ $i <= $estadoActual ? 'white' : 'var(--muted)' }};
                        font-size:.75rem; font-weight:700">
                        {{ $i < $estadoActual ? '✓' : ($i + 1) }}
                    </div>
                    <div style="font-size:.84rem;
                        color:{{ $i === $estadoActual ? 'var(--navy)' : ($i < $estadoActual ? 'var(--ok)' : 'var(--muted)') }};
                        font-weight:{{ $i === $estadoActual ? '600' : '400' }}">
                        {{ $etiquetas[$i] }}
                        @if($i === $estadoActual)
                        <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:var(--accent); margin-left:6px; vertical-align:middle">
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Modal egreso --}}
<div id="modalEgreso" style="display:none; position:fixed; inset:0; background:rgba(11,28,46,.65); z-index:500; align-items:center; justify-content:center; padding:20px">
    <div style="background:white; border-radius:14px; width:100%; max-width:480px; box-shadow:0 20px 60px rgba(0,0,0,.3); overflow:hidden">
        <div style="padding:18px 22px; border-bottom:1px solid var(--border); background:var(--light)">
            <div style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--navy); letter-spacing:.04em">
                <i class="bi bi-box-arrow-right" style="color:var(--ok); margin-right:8px"></i>REGISTRAR ENTREGA DEL VEHÍCULO
            </div>
        </div>
        <form method="POST" action="{{ route('admin.trabajos.egreso', $ingreso) }}">
            @csrf
            <div style="padding:22px; display:flex; flex-direction:column; gap:14px">
                <div>
                    <label class="ta-label">Kilometraje de egreso</label>
                    <input type="number" name="kilometraje_egreso" class="ta-input" min="{{ $ingreso->kilometraje_ingreso }}"
                        placeholder="{{ $ingreso->kilometraje_ingreso }}" value="{{ $ingreso->kilometraje_ingreso }}">
                </div>
                <div>
                    <label class="ta-label">Observaciones de entrega</label>
                    <textarea name="observaciones" class="ta-input ta-textarea"
                        placeholder="Notas sobre la entrega del vehículo..."></textarea>
                </div>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; padding:12px; background:var(--card); border-radius:8px; border:1px solid var(--border)">
                    <input type="checkbox" name="firma_conformidad" value="1" checked style="accent-color:var(--ok); width:16px; height:16px">
                    <div>
                        <div style="font-size:.88rem; font-weight:600; color:var(--navy)">Firma de conformidad</div>
                        <div style="font-size:.76rem; color:var(--muted)">El cliente aceptó y firmó conformidad con el trabajo realizado</div>
                    </div>
                </label>
            </div>
            <div style="padding:14px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px">
                <button type="button" class="btn-secondary-ta" onclick="document.getElementById('modalEgreso').style.display='none'">Cancelar</button>
                <button type="submit" class="btn-ok-ta">
                    <i class="bi bi-check-circle"></i> Confirmar Entrega
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let repuestoIdx = 1;
const repuestosOpts = `@foreach($repuestos as $rep)<option value="{{ $rep->id }}">{{ addslashes($rep->nombre) }} (Stock: {{ $rep->cantidad_stock }})</option>@endforeach`;

function agregarRepuesto() {
    const container = document.getElementById('repuestosContainer');
    const div = document.createElement('div');
    div.className = 'repuesto-row';
    div.style.cssText = 'display:grid; grid-template-columns:1fr 100px auto; gap:8px; margin-bottom:8px';
    div.innerHTML = `
        <select name="repuestos[${repuestoIdx}][id]" class="ta-input ta-select">
            <option value="">Sin repuesto</option>
            ${repuestosOpts}
        </select>
        <input type="number" name="repuestos[${repuestoIdx}][cantidad]" class="ta-input" min="1" placeholder="Cant." value="1">
        <button type="button" onclick="this.closest('.repuesto-row').remove()"
            style="background:none; border:1.5px solid var(--border); border-radius:8px; padding:0 10px; cursor:pointer; color:var(--error); font-size:1rem">
            <i class="bi bi-trash"></i>
        </button>`;
    container.appendChild(div);
    repuestoIdx++;
}
</script>
@endpush
@endsection
