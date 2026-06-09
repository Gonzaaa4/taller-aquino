@extends('layouts.app')
@section('title', 'Factura ' . $factura->numero)
@section('topbar-title', 'Detalle de Factura')

@push('styles')
<style>
.inv-section { background:#fff; border-radius:12px; margin-bottom:18px; box-shadow:0 2px 10px rgba(0,0,0,.06); overflow:hidden; }
.inv-header { background:var(--light); border-bottom:1px solid var(--border); padding:12px 20px; display:flex; align-items:center; gap:10px; }
.inv-icon { width:30px; height:30px; background:var(--blue); border-radius:7px; display:flex; align-items:center; justify-content:center; }
.inv-icon i { color:white; font-size:.9rem; }
.inv-title { font-family:'Oswald',sans-serif; font-size:.95rem; color:var(--navy); letter-spacing:.04em; }
.inv-body { padding:20px; }
.field-group { display:flex; flex-direction:column; gap:4px; }
.field-group label { font-size:.76rem; font-weight:700; color:var(--muted); letter-spacing:.05em; text-transform:uppercase; }
.field-input { border:1.5px solid var(--border); border-radius:7px; padding:10px 13px; font-size:.92rem; color:var(--text); outline:none; width:100%; background:#fff; }
.field-input:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(46,141,255,.12); }
.totals-box { background:linear-gradient(135deg,#0b1c2e,#1255a1); border-radius:12px; padding:22px; }
.tot-row { display:flex; justify-content:space-between; padding:7px 0; }
.tot-row.big { border-top:2px solid rgba(255,255,255,.2); margin-top:8px; padding-top:14px; }
.tot-label { font-size:.84rem; color:rgba(255,255,255,.6); }
.tot-value { font-size:.92rem; font-weight:600; color:white; }
.tot-big-label { font-family:'Oswald',sans-serif; font-size:1rem; color:white; letter-spacing:.04em; }
.tot-big-value { font-family:'Oswald',sans-serif; font-size:1.6rem; color:#4ade80; }
.pay-item { display:flex; justify-content:space-between; align-items:center; padding:12px 14px; background:var(--card); border-radius:9px; margin-bottom:8px; }
.modal-bg { display:none; position:fixed; inset:0; background:rgba(11,28,46,.65); z-index:500; align-items:center; justify-content:center; padding:20px; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Finanzas · Facturación</div>
            <h1 class="page-title">{{ $factura->numero }}</h1>
            <p class="page-subtitle">
                {{ $factura->tipo === 'presupuesto' ? 'Presupuesto' : 'Factura' }} ·
                {{ $factura->created_at->format('d/m/Y H:i') }} hs
            </p>
        </div>
        <a href="{{ route('admin.facturacion.index') }}" class="btn-secondary-ta">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

@if(session('success'))
<div class="ta-alert success" style="margin-bottom:18px">
    <span class="ta-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
    <div>{{ session('success') }}</div>
</div>
@endif
@if(session('error'))
<div class="ta-alert error" style="margin-bottom:18px">
    <span class="ta-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
    <div>{{ session('error') }}</div>
</div>
@endif

<div style="display:grid; grid-template-columns:1.6fr 1fr; gap:18px; align-items:start">

    {{-- Columna izquierda --}}
    <div>
        {{-- Cliente y vehículo --}}
        <div class="inv-section">
            <div class="inv-header">
                <div class="inv-icon"><i class="bi bi-person"></i></div>
                <span class="inv-title">CLIENTE Y VEHÍCULO</span>
            </div>
            <div class="inv-body" style="display:grid; grid-template-columns:1fr 1fr; gap:16px">
                <div>
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Cliente</div>
                    <div style="font-weight:600; color:var(--navy)">{{ $factura->cliente->nombreCompleto() }}</div>
                    <div style="font-size:.82rem; color:var(--muted)">{{ $factura->cliente->telefono }}</div>
                    @if($factura->cliente->dni)
                    <div style="font-size:.82rem; color:var(--muted)">DNI: {{ $factura->cliente->dni }}</div>
                    @endif
                </div>
                <div>
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Vehículo</div>
                    <div style="font-weight:600; color:var(--navy)">{{ $factura->ingreso->vehiculo->marca->nombre }} {{ $factura->ingreso->vehiculo->modelo->nombre }}</div>
                    <div style="font-size:.82rem; color:var(--accent); font-family:'Oswald',sans-serif">{{ $factura->ingreso->vehiculo->patente }}</div>
                </div>
            </div>
        </div>

        {{-- Detalle de la factura --}}
        <div class="inv-section">
            <div class="inv-header">
                <div class="inv-icon"><i class="bi bi-list-ul"></i></div>
                <span class="inv-title">DETALLE</span>
            </div>
            <div class="inv-body">
                <table style="width:100%; border-collapse:collapse; font-size:.88rem">
                    <tbody>
                        <tr style="border-bottom:1px solid var(--border)">
                            <td style="padding:10px 8px; color:var(--text)">Mano de obra</td>
                            <td style="padding:10px 8px; text-align:right; font-weight:600; color:var(--navy)">${{ number_format($factura->subtotal_mano_obra, 0, ',', '.') }}</td>
                        </tr>
                        <tr style="border-bottom:1px solid var(--border)">
                            <td style="padding:10px 8px; color:var(--text)">Repuestos</td>
                            <td style="padding:10px 8px; text-align:right; font-weight:600; color:var(--navy)">${{ number_format($factura->subtotal_repuestos, 0, ',', '.') }}</td>
                        </tr>
                        @if($factura->descuento > 0)
                        <tr style="border-bottom:1px solid var(--border)">
                            <td style="padding:10px 8px; color:var(--error)">Descuento</td>
                            <td style="padding:10px 8px; text-align:right; font-weight:600; color:var(--error)">-${{ number_format($factura->descuento, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                @if($factura->observaciones)
                <div style="margin-top:14px; padding:12px 14px; background:var(--card); border-radius:9px; font-size:.84rem; color:var(--text)">
                    <strong style="color:var(--muted); font-size:.74rem; text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:4px">Observaciones</strong>
                    {{ $factura->observaciones }}
                </div>
                @endif
            </div>
        </div>

        {{-- Pagos registrados --}}
        <div class="inv-section">
            <div class="inv-header">
                <div class="inv-icon" style="background:var(--ok)"><i class="bi bi-cash-coin"></i></div>
                <span class="inv-title">PAGOS REGISTRADOS</span>
            </div>
            <div class="inv-body">
                @forelse($factura->pagos as $pago)
                <div class="pay-item">
                    <div>
                        <div style="font-weight:600; color:var(--navy); font-size:.9rem">{{ $pago->etiquetaMetodo() }}</div>
                        <div style="font-size:.76rem; color:var(--muted)">
                            {{ $pago->created_at->format('d/m/Y H:i') }} hs · {{ $pago->registradoPor->name }}
                            @if($pago->referencia) · Ref: {{ $pago->referencia }} @endif
                        </div>
                    </div>
                    <div style="font-family:'Oswald',sans-serif; font-size:1.1rem; color:var(--ok)">
                        ${{ number_format($pago->monto, 0, ',', '.') }}
                    </div>
                </div>
                @empty
                <div style="text-align:center; padding:24px; color:var(--muted); font-size:.86rem">
                    <i class="bi bi-inbox" style="font-size:1.6rem; display:block; margin-bottom:8px; opacity:.3"></i>
                    Sin pagos registrados
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Columna derecha --}}
    <div>
        {{-- Totales --}}
        <div class="totals-box" style="margin-bottom:18px">
            <div style="font-size:.72rem; color:rgba(255,255,255,.5); text-transform:uppercase; letter-spacing:.08em; margin-bottom:12px">Estado de cuenta</div>
            <div class="tot-row">
                <span class="tot-label">Total factura</span>
                <span class="tot-value">${{ number_format($factura->total, 0, ',', '.') }}</span>
            </div>
            <div class="tot-row">
                <span class="tot-label">Pagado</span>
                <span class="tot-value" style="color:#4ade80">${{ number_format($factura->totalPagado(), 0, ',', '.') }}</span>
            </div>
            <div class="tot-row big">
                <span class="tot-big-label">SALDO</span>
                <span class="tot-big-value" style="{{ $factura->saldoPendiente() <= 0 ? 'color:#4ade80' : 'color:#fbbf24' }}">
                    ${{ number_format($factura->saldoPendiente(), 0, ',', '.') }}
                </span>
            </div>
            <div style="margin-top:14px; text-align:center">
                @php
                $badgeColor = match($factura->estado) {
                    'pagada' => '#4ade80',
                    'parcial' => '#fbbf24',
                    'pendiente' => 'rgba(255,255,255,.6)',
                    'anulada' => '#f87171',
                    default => 'white'
                };
                @endphp
                <span style="display:inline-block; padding:5px 16px; border-radius:20px; background:rgba(255,255,255,.12); color:{{ $badgeColor }}; font-size:.82rem; font-weight:600">
                    {{ $factura->etiquetaEstado() }}
                </span>
            </div>
        </div>

        {{-- Acciones --}}
        @if($factura->estado !== 'anulada')
            @if($factura->saldoPendiente() > 0)
            <button onclick="document.getElementById('modalPago').style.display='flex'" class="btn-ok-ta" style="width:100%; justify-content:center; margin-bottom:10px">
                <i class="bi bi-plus-circle"></i> Registrar Pago
            </button>
            @endif

            @if($factura->totalPagado() == 0)
            <form method="POST" action="{{ route('admin.facturacion.anular', $factura) }}"
                  onsubmit="return confirm('¿Seguro que querés anular esta factura?')">
                @csrf
                <button type="submit" class="btn-secondary-ta" style="width:100%; justify-content:center; color:var(--error); border-color:var(--error)">
                    <i class="bi bi-x-circle"></i> Anular Factura
                </button>
            </form>
            @endif
        @endif
    </div>
</div>

{{-- Modal registrar pago --}}
<div id="modalPago" class="modal-bg">
    <div style="background:white; border-radius:14px; width:100%; max-width:440px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.3)">
        <div style="padding:18px 22px; border-bottom:1px solid var(--border); background:var(--light); display:flex; justify-content:space-between; align-items:center">
            <div style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--navy); letter-spacing:.04em">
                <i class="bi bi-cash-coin" style="color:var(--ok); margin-right:8px"></i>REGISTRAR PAGO
            </div>
            <button type="button" onclick="document.getElementById('modalPago').style.display='none'"
                style="background:none; border:none; font-size:1.3rem; cursor:pointer; color:var(--muted)">×</button>
        </div>
        <form method="POST" action="{{ route('admin.facturacion.pago', $factura) }}">
            @csrf
            <div style="padding:22px; display:flex; flex-direction:column; gap:14px">
                <div style="background:var(--card); border-radius:9px; padding:12px 14px; text-align:center">
                    <div style="font-size:.74rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em">Saldo pendiente</div>
                    <div style="font-family:'Oswald',sans-serif; font-size:1.5rem; color:var(--warn)">${{ number_format($factura->saldoPendiente(), 0, ',', '.') }}</div>
                </div>
                <div class="field-group">
                    <label>Monto a pagar ($)</label>
                    <input type="number" name="monto" class="field-input" min="0.01" step="0.01"
                        max="{{ $factura->saldoPendiente() }}" value="{{ $factura->saldoPendiente() }}" required>
                </div>
                <div class="field-group">
                    <label>Método de pago</label>
                    <select name="metodo" class="field-input" required>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta_debito">Tarjeta de débito</option>
                        <option value="tarjeta_credito">Tarjeta de crédito</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Referencia (opcional)</label>
                    <input type="text" name="referencia" class="field-input" placeholder="N° de operación, cheque, etc.">
                </div>
            </div>
            <div style="padding:14px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px; background:var(--card)">
                <button type="button" class="btn-secondary-ta" onclick="document.getElementById('modalPago').style.display='none'">Cancelar</button>
                <button type="submit" class="btn-ok-ta"><i class="bi bi-check-circle"></i> Registrar Pago</button>
            </div>
        </form>
    </div>
</div>
@endsection