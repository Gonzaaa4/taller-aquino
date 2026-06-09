@extends('layouts.app')
@section('title', 'Generar Factura')
@section('topbar-title', 'Generar Factura')

@push('styles')
<style>
.fact-section { background:#fff; border-radius:12px; margin-bottom:18px; box-shadow:0 2px 10px rgba(0,0,0,.06); overflow:hidden; }
.fact-header { background:var(--light); border-bottom:1px solid var(--border); padding:12px 20px; display:flex; align-items:center; gap:10px; }
.fact-icon { width:30px; height:30px; background:var(--blue); border-radius:7px; display:flex; align-items:center; justify-content:center; }
.fact-icon i { color:white; font-size:.9rem; }
.fact-title { font-family:'Oswald',sans-serif; font-size:.95rem; color:var(--navy); letter-spacing:.04em; }
.fact-body { padding:20px; }
.field-group { display:flex; flex-direction:column; gap:4px; }
.field-group label { font-size:.76rem; font-weight:700; color:var(--muted); letter-spacing:.05em; text-transform:uppercase; }
.req-star { color:var(--error); }
.field-input { border:1.5px solid var(--border); border-radius:7px; padding:10px 13px; font-family:'Source Sans 3',sans-serif; font-size:.92rem; color:var(--text); outline:none; width:100%; background:#fff; transition:border-color .2s; }
.field-input:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(46,141,255,.12); }
.cost-box { background:linear-gradient(135deg,#0b1c2e,#1255a1); border-radius:12px; padding:20px 22px; }
.cost-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; }
.cost-row.total { border-top:2px solid rgba(255,255,255,.2); margin-top:8px; padding-top:14px; }
.cost-label-w { font-size:.84rem; color:rgba(255,255,255,.6); }
.cost-value-w { font-size:.95rem; font-weight:600; color:white; }
.cost-total-label { font-family:'Oswald',sans-serif; font-size:1rem; color:white; letter-spacing:.04em; }
.cost-total-value { font-family:'Oswald',sans-serif; font-size:1.6rem; color:#4ade80; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Finanzas · Facturación</div>
            <h1 class="page-title">Generar Factura</h1>
            <p class="page-subtitle">{{ $ingreso->vehiculo->marca->nombre }} {{ $ingreso->vehiculo->modelo->nombre }} — {{ $ingreso->vehiculo->patente }}</p>
        </div>
        <a href="{{ route('admin.trabajos.show', $ingreso) }}" class="btn-secondary-ta">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.facturacion.guardar', $ingreso) }}" id="facturaForm">
    @csrf

    {{-- Cliente y vehículo --}}
    <div class="fact-section">
        <div class="fact-header">
            <div class="fact-icon"><i class="bi bi-person"></i></div>
            <span class="fact-title">DATOS DEL CLIENTE</span>
        </div>
        <div class="fact-body" style="display:grid; grid-template-columns:1fr 1fr; gap:16px">
            <div>
                <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Cliente</div>
                <div style="font-weight:600; color:var(--navy)">{{ $ingreso->cliente->nombreCompleto() }}</div>
                <div style="font-size:.82rem; color:var(--muted)">{{ $ingreso->cliente->telefono }}</div>
            </div>
            <div>
                <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Vehículo</div>
                <div style="font-weight:600; color:var(--navy)">{{ $ingreso->vehiculo->marca->nombre }} {{ $ingreso->vehiculo->modelo->nombre }} {{ $ingreso->vehiculo->anio }}</div>
                <div style="font-size:.82rem; color:var(--accent); font-family:'Oswald',sans-serif">{{ $ingreso->vehiculo->patente }}</div>
            </div>
        </div>
    </div>

    {{-- Trabajos realizados (referencia) --}}
    @if($ingreso->trabajos->isNotEmpty())
    <div class="fact-section">
        <div class="fact-header">
            <div class="fact-icon"><i class="bi bi-clipboard-check"></i></div>
            <span class="fact-title">TRABAJOS REALIZADOS (referencia de costos)</span>
        </div>
        <div class="fact-body">
            <table style="width:100%; border-collapse:collapse; font-size:.86rem">
                <thead>
                    <tr style="border-bottom:1px solid var(--border)">
                        <th style="text-align:left; padding:8px; color:var(--muted); font-size:.74rem; text-transform:uppercase">Trabajo</th>
                        <th style="text-align:right; padding:8px; color:var(--muted); font-size:.74rem; text-transform:uppercase">Costo M.O.</th>
                        <th style="text-align:right; padding:8px; color:var(--muted); font-size:.74rem; text-transform:uppercase">Costo Rep.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ingreso->trabajos as $t)
                    <tr style="border-bottom:1px solid var(--border)">
                        <td style="padding:8px; color:var(--text)">{{ ucfirst(str_replace('_',' ',$t->tipo_servicio)) }}</td>
                        <td style="padding:8px; text-align:right; color:var(--muted)">${{ number_format($t->costo_mano_obra, 0, ',', '.') }}</td>
                        <td style="padding:8px; text-align:right; color:var(--muted)">${{ number_format($t->costo_repuestos, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr style="font-weight:600; color:var(--navy)">
                        <td style="padding:8px">Total costos</td>
                        <td style="padding:8px; text-align:right">${{ number_format($ingreso->trabajos->sum('costo_mano_obra'), 0, ',', '.') }}</td>
                        <td style="padding:8px; text-align:right">${{ number_format($ingreso->trabajos->sum('costo_repuestos'), 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
            <div style="font-size:.78rem; color:var(--muted); margin-top:10px">
                <i class="bi bi-info-circle" style="color:var(--blue)"></i>
                Estos son los costos del taller. Definí abajo los precios de venta al cliente.
            </div>
        </div>
    </div>
    @endif

    {{-- Precios de venta --}}
    <div class="fact-section">
        <div class="fact-header">
            <div class="fact-icon" style="background:#cc5500"><i class="bi bi-tag"></i></div>
            <span class="fact-title">PRECIOS DE VENTA AL CLIENTE</span>
        </div>
        <div class="fact-body">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px">
                <div class="field-group">
                    <label>Tipo de comprobante <span class="req-star">*</span></label>
                    <select name="tipo" class="field-input" required>
                        <option value="factura">Factura</option>
                        <option value="presupuesto">Presupuesto</option>
                    </select>
                </div>
            </div>
            
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px">
                <div class="field-group">
                    <label>Mano de obra <span class="req-star">*</span></label>
                    <div style="position:relative">
                        <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--muted); font-weight:600; font-size:.9rem">$</span>
                        <input type="number" name="subtotal_mano_obra" id="mo" class="field-input" min="0" step="0.01" required
                            style="padding-left:26px"
                            value="{{ $ingreso->trabajos->sum('costo_mano_obra') }}" oninput="calcular()">
                    </div>
                </div>
                <div class="field-group">
                    <label>Repuestos <span class="req-star">*</span></label>
                    <div style="position:relative">
                        <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--muted); font-weight:600; font-size:.9rem">$</span>
                        <input type="number" name="subtotal_repuestos" id="rep" class="field-input" min="0" step="0.01" required
                            style="padding-left:26px"
                            value="{{ $ingreso->trabajos->sum('costo_repuestos') }}" oninput="calcular()">
                    </div>
                </div>
                <div class="field-group">
                    <label>Descuento</label>
                    <div style="position:relative">
                        <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--muted); font-weight:600; font-size:.9rem">$</span>
                        <input type="number" name="descuento" id="desc" class="field-input" min="0" step="0.01"
                            style="padding-left:26px"
                            value="0" oninput="calcular()">
                    </div>
                </div>
            </div>

    {{-- Resumen total --}}
    <div class="cost-box" style="margin-bottom:18px">
        <div class="cost-row">
            <span class="cost-label-w">Mano de obra</span>
            <span class="cost-value-w" id="r-mo">$0</span>
        </div>
        <div class="cost-row">
            <span class="cost-label-w">Repuestos</span>
            <span class="cost-value-w" id="r-rep">$0</span>
        </div>
        <div class="cost-row">
            <span class="cost-label-w">Descuento</span>
            <span class="cost-value-w" id="r-desc" style="color:#f87171">-$0</span>
        </div>
        <div class="cost-row total">
            <span class="cost-total-label">TOTAL A COBRAR</span>
            <span class="cost-total-value" id="r-total">$0</span>
        </div>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:10px">
        <a href="{{ route('admin.trabajos.show', $ingreso) }}" class="btn-secondary-ta">Cancelar</a>
        <button type="submit" class="btn-ok-ta"><i class="bi bi-check-circle"></i> Generar Factura</button>
    </div>
</form>

@push('scripts')
<script>
function calcular() {
    const mo   = parseFloat(document.getElementById('mo').value) || 0;
    const rep  = parseFloat(document.getElementById('rep').value) || 0;
    const desc = parseFloat(document.getElementById('desc').value) || 0;
    const total = Math.max((mo + rep) - desc, 0);

    const fmt = n => n.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    document.getElementById('r-mo').textContent   = '$ ' + fmt(mo);
    document.getElementById('r-rep').textContent  = '$ ' + fmt(rep);
    document.getElementById('r-desc').textContent = '-$ ' + fmt(desc);
    document.getElementById('r-total').textContent = '$ ' + fmt(total);
}
calcular();
</script>
@endpush
@endsection