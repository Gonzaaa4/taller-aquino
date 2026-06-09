@extends('layouts.app')
@section('title', 'Caja')
@section('topbar-title', 'Caja Diaria')

@push('styles')
<style>
.kpi-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:20px; }
.kpi-card { background:#fff; border-radius:12px; padding:20px 22px; box-shadow:0 2px 10px rgba(0,0,0,.06); position:relative; overflow:hidden; }
.kpi-card::after { content:''; position:absolute; top:0; left:0; width:4px; height:100%; }
.kpi-card.ingreso::after { background:var(--ok); }
.kpi-card.egreso::after { background:var(--error); }
.kpi-card.saldo::after { background:var(--blue); }
.kpi-label { font-size:.74rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:8px; }
.kpi-value { font-family:'Oswald',sans-serif; font-size:1.8rem; letter-spacing:.02em; }
.kpi-card.ingreso .kpi-value { color:var(--ok); }
.kpi-card.egreso .kpi-value { color:var(--error); }
.kpi-card.saldo .kpi-value { color:var(--navy); }
.field-group { display:flex; flex-direction:column; gap:4px; }
.field-group label { font-size:.76rem; font-weight:700; color:var(--muted); letter-spacing:.05em; text-transform:uppercase; }
.field-input { border:1.5px solid var(--border); border-radius:7px; padding:10px 13px; font-size:.92rem; color:var(--text); outline:none; width:100%; background:#fff; }
.field-input:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(46,141,255,.12); }
.modal-bg { display:none; position:fixed; inset:0; background:rgba(11,28,46,.65); z-index:500; align-items:center; justify-content:center; padding:20px; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Finanzas</div>
            <h1 class="page-title">Caja Diaria</h1>
            <p class="page-subtitle">Movimientos del {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
        </div>
        <div style="display:flex; gap:10px">
            <a href="{{ route('admin.facturacion.index') }}" class="btn-secondary-ta">
                <i class="bi bi-receipt"></i> Facturas
            </a>
            <button onclick="document.getElementById('modalMov').style.display='flex'" class="btn-primary-ta">
                <i class="bi bi-plus-circle"></i> Nuevo Movimiento
            </button>
        </div>
    </div>
</div>

@if(session('success'))
<div class="ta-alert success" style="margin-bottom:18px">
    <span class="ta-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
    <div>{{ session('success') }}</div>
</div>
@endif

{{-- Selector de fecha --}}
<div class="ta-card" style="margin-bottom:20px">
    <div class="ta-card-body" style="padding:14px 20px">
        <form method="GET" style="display:flex; gap:12px; align-items:flex-end">
            <div class="field-group">
                <label>Ver caja del día</label>
                <input type="date" name="fecha" class="field-input" style="width:200px" value="{{ $fecha }}">
            </div>
            <button type="submit" class="btn-primary-ta" style="height:42px"><i class="bi bi-search"></i> Ver</button>
            @if($fecha !== now()->toDateString())
            <a href="{{ route('admin.facturacion.caja') }}" class="btn-secondary-ta" style="height:42px">Hoy</a>
            @endif
        </form>
    </div>
</div>

{{-- KPIs --}}
<div class="kpi-grid">
    <div class="kpi-card ingreso">
        <div class="kpi-label"><i class="bi bi-arrow-down-circle"></i> Ingresos</div>
        <div class="kpi-value">${{ number_format($ingresos, 0, ',', '.') }}</div>
    </div>
    <div class="kpi-card egreso">
        <div class="kpi-label"><i class="bi bi-arrow-up-circle"></i> Egresos</div>
        <div class="kpi-value">${{ number_format($egresos, 0, ',', '.') }}</div>
    </div>
    <div class="kpi-card saldo">
        <div class="kpi-label"><i class="bi bi-wallet2"></i> Saldo del día</div>
        <div class="kpi-value">${{ number_format($saldo, 0, ',', '.') }}</div>
    </div>
</div>

{{-- Movimientos --}}
<div class="ta-card">
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Tipo</th>
                    <th>Concepto</th>
                    <th>Categoría</th>
                    <th>Registrado por</th>
                    <th style="text-align:right">Monto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movimientos as $mov)
                <tr>
                    <td style="font-size:.84rem; color:var(--muted)">{{ $mov->created_at->format('H:i') }} hs</td>
                    <td>
                        @if($mov->tipo === 'ingreso')
                        <span style="display:inline-flex; align-items:center; gap:5px; font-size:.8rem; color:var(--ok); font-weight:600">
                            <i class="bi bi-arrow-down-circle-fill"></i> Ingreso
                        </span>
                        @else
                        <span style="display:inline-flex; align-items:center; gap:5px; font-size:.8rem; color:var(--error); font-weight:600">
                            <i class="bi bi-arrow-up-circle-fill"></i> Egreso
                        </span>
                        @endif
                    </td>
                    <td style="font-size:.88rem; color:var(--navy)">{{ $mov->concepto }}</td>
                    <td><span style="font-size:.78rem; color:var(--muted)">{{ $mov->etiquetaCategoria() }}</span></td>
                    <td style="font-size:.84rem; color:var(--muted)">{{ $mov->registradoPor->name }}</td>
                    <td style="text-align:right; font-family:'Oswald',sans-serif; font-size:1rem; color:{{ $mov->tipo === 'ingreso' ? 'var(--ok)' : 'var(--error)' }}">
                        {{ $mov->tipo === 'ingreso' ? '+' : '-' }}${{ number_format($mov->monto, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:56px; color:var(--muted)">
                        <i class="bi bi-cash-stack" style="font-size:2.5rem; display:block; margin-bottom:14px; opacity:.3"></i>
                        No hay movimientos en esta fecha
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal nuevo movimiento --}}
<div id="modalMov" class="modal-bg">
    <div style="background:white; border-radius:14px; width:100%; max-width:460px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.3)">
        <div style="padding:18px 22px; border-bottom:1px solid var(--border); background:var(--light); display:flex; justify-content:space-between; align-items:center">
            <div style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--navy); letter-spacing:.04em">
                <i class="bi bi-plus-circle" style="color:var(--blue); margin-right:8px"></i>NUEVO MOVIMIENTO
            </div>
            <button type="button" onclick="document.getElementById('modalMov').style.display='none'"
                style="background:none; border:none; font-size:1.3rem; cursor:pointer; color:var(--muted)">×</button>
        </div>
        <form method="POST" action="{{ route('admin.facturacion.caja.movimiento') }}">
            @csrf
            <div style="padding:22px; display:flex; flex-direction:column; gap:14px">
                <div class="field-group">
                    <label>Tipo de movimiento</label>
                    <select name="tipo" class="field-input" required>
                        <option value="egreso">Egreso (salida de dinero)</option>
                        <option value="ingreso">Ingreso (entrada de dinero)</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Monto ($)</label>
                    <input type="number" name="monto" class="field-input" min="0.01" step="0.01" required>
                </div>
                <div class="field-group">
                    <label>Concepto</label>
                    <input type="text" name="concepto" class="field-input" placeholder="Ej: Compra de aceite, pago de luz..." required>
                </div>
                <div class="field-group">
                    <label>Categoría</label>
                    <select name="categoria" class="field-input" required>
                        <option value="compra_repuestos">Compra de repuestos</option>
                        <option value="sueldos">Sueldos</option>
                        <option value="servicios">Servicios (luz, agua, etc.)</option>
                        <option value="venta">Venta</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>
            </div>
            <div style="padding:14px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px; background:var(--card)">
                <button type="button" class="btn-secondary-ta" onclick="document.getElementById('modalMov').style.display='none'">Cancelar</button>
                <button type="submit" class="btn-primary-ta"><i class="bi bi-check-circle"></i> Registrar</button>
            </div>
        </form>
    </div>
</div>
@endsection
