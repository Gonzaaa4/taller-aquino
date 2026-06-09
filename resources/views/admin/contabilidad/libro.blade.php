@extends('layouts.app')
@section('title', 'Libro de Ingresos y Egresos')
@section('topbar-title', 'Libro Contable')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Contabilidad</div>
            <h1 class="page-title">Libro de Ingresos y Egresos</h1>
            <p class="page-subtitle">Movimientos contables del período seleccionado</p>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="ta-card" style="margin-bottom:20px">
    <div class="ta-card-body" style="padding:14px 20px">
        <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end">
            <div>
                <label class="ta-label">Mes</label>
                <select name="mes" class="ta-input ta-select" style="width:140px">
                    @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $m)
                    <option value="{{ $i+1 }}" {{ $mes == $i+1 ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="ta-label">Año</label>
                <select name="anio" class="ta-input ta-select" style="width:110px">
                    @foreach($anios as $a)
                    <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:8px; align-items:flex-end">
                <button type="submit" class="btn-primary-ta" style="height:40px"><i class="bi bi-search"></i> Ver</button>
            </div>
        </form>
    </div>
</div>

{{-- KPIs --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:20px">
    <div class="ta-card" style="padding:20px; border-left:4px solid var(--ok)">
        <div style="font-size:.74rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px"><i class="bi bi-arrow-down-circle"></i> Total Ingresos</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.8rem; color:var(--ok)">${{ number_format($ingresos, 0, ',', '.') }}</div>
    </div>
    <div class="ta-card" style="padding:20px; border-left:4px solid var(--error)">
        <div style="font-size:.74rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px"><i class="bi bi-arrow-up-circle"></i> Total Egresos</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.8rem; color:var(--error)">${{ number_format($egresos, 0, ',', '.') }}</div>
    </div>
    <div class="ta-card" style="padding:20px; border-left:4px solid {{ $resultado >= 0 ? 'var(--blue)' : 'var(--error)' }}">
        <div style="font-size:.74rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px"><i class="bi bi-wallet2"></i> Resultado</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.8rem; color:{{ $resultado >= 0 ? 'var(--navy)' : 'var(--error)' }}">
            {{ $resultado >= 0 ? '' : '-' }}${{ number_format(abs($resultado), 0, ',', '.') }}
        </div>
    </div>
</div>

{{-- Resumen por categoría --}}
@if($porCategoria->isNotEmpty())
<div class="ta-card" style="margin-bottom:20px">
    <div class="ta-card-header">
        <div class="ta-card-title"><i class="bi bi-pie-chart" style="color:var(--blue)"></i> Resumen por Categoría</div>
    </div>
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th style="text-align:right">Ingresos</th>
                    <th style="text-align:right">Egresos</th>
                    <th style="text-align:right">Resultado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($porCategoria as $cat => $datos)
                <tr>
                    <td style="font-weight:600; color:var(--navy)">
                        {{ match($cat) {
                            'venta' => 'Ventas',
                            'compra_repuestos' => 'Compra de repuestos',
                            'sueldos' => 'Sueldos',
                            'servicios' => 'Servicios',
                            default => 'Otros'
                        } }}
                    </td>
                    <td style="text-align:right; color:var(--ok); font-weight:600">
                        {{ $datos['ingresos'] > 0 ? '$'.number_format($datos['ingresos'], 0, ',', '.') : '—' }}
                    </td>
                    <td style="text-align:right; color:var(--error); font-weight:600">
                        {{ $datos['egresos'] > 0 ? '$'.number_format($datos['egresos'], 0, ',', '.') : '—' }}
                    </td>
                    <td style="text-align:right; font-weight:700; color:{{ ($datos['ingresos']-$datos['egresos']) >= 0 ? 'var(--navy)' : 'var(--error)' }}">
                        ${{ number_format($datos['ingresos'] - $datos['egresos'], 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Movimientos detallados --}}
<div class="ta-card">
    <div class="ta-card-header">
        <div class="ta-card-title"><i class="bi bi-list-ul" style="color:var(--blue)"></i> Movimientos Detallados</div>
        <div style="font-size:.82rem; color:var(--muted)">{{ $movimientos->count() }} movimientos</div>
    </div>
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>Fecha</th>
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
                    <td style="font-size:.84rem; color:var(--muted); white-space:nowrap">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
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
                    <td style="font-size:.78rem; color:var(--muted)">{{ $mov->etiquetaCategoria() }}</td>
                    <td style="font-size:.84rem; color:var(--muted)">{{ $mov->registradoPor->name }}</td>
                    <td style="text-align:right; font-family:'Oswald',sans-serif; font-size:1rem; color:{{ $mov->tipo === 'ingreso' ? 'var(--ok)' : 'var(--error)' }}; white-space:nowrap">
                        {{ $mov->tipo === 'ingreso' ? '+' : '-' }}${{ number_format($mov->monto, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:56px; color:var(--muted)">
                        <i class="bi bi-journal" style="font-size:2.5rem; display:block; margin-bottom:14px; opacity:.3"></i>
                        No hay movimientos en este período
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection