@extends('layouts.app')
@section('title', 'Reporte de Stock')
@section('topbar-title', 'Reporte de Stock')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Reportes</div>
            <h1 class="page-title">Stock de Repuestos</h1>
            <p class="page-subtitle">Estado actual del inventario — {{ now()->format('d/m/Y H:i') }}</p>
        </div>
        <div style="display:flex; gap:10px">
            <a href="{{ route('admin.reportes.stock', array_merge(request()->query(), ['formato'=>'pdf'])) }}"
               class="btn-danger-ta">
                <i class="bi bi-file-pdf"></i> Descargar PDF
            </a>
            <a href="{{ route('admin.reportes.formulario') }}" class="btn-secondary-ta">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<div class="kpi-grid" style="margin-bottom:24px">
    <div class="kpi-card kpi-blue">
        <div class="kpi-icon"><i class="bi bi-box-seam"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Total Items</div>
            <div class="kpi-value">{{ $repuestos->count() }}</div>
        </div>
    </div>
    <div class="kpi-card kpi-red">
        <div class="kpi-icon"><i class="bi bi-x-circle"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Sin Stock</div>
            <div class="kpi-value">{{ $sinStock }}</div>
        </div>
    </div>
    <div class="kpi-card kpi-orange">
        <div class="kpi-icon"><i class="bi bi-exclamation-triangle"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Stock Crítico</div>
            <div class="kpi-value">{{ $alertas }}</div>
        </div>
    </div>
    <div class="kpi-card kpi-green">
        <div class="kpi-icon"><i class="bi bi-currency-dollar"></i></div>
        <div class="kpi-data">
            <div class="kpi-label">Valor Total</div>
            <div class="kpi-value" style="font-size:1.3rem">${{ number_format($valorTotal, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

@foreach($repuestos->groupBy('categoria') as $cat => $items)
<div class="section-label" style="margin-top:20px">
    <h2>{{ strtoupper($cat) }} ({{ $items->count() }})</h2>
    <div class="section-label-line"></div>
</div>
<div class="ta-card" style="margin-bottom:16px">
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th style="text-align:center">Stock</th>
                    <th style="text-align:center">Mínimo</th>
                    <th>Estado</th>
                    <th>Proveedor</th>
                    <th>Ubicación</th>
                    <th style="text-align:right">Costo Unit.</th>
                    <th style="text-align:right">Valor Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $rep)
                @php $est = $rep->estadoStock(); @endphp
                <tr style="{{ $est === 'sin_stock' ? 'background:rgba(217,48,37,.03)' : ($est === 'critico' ? 'background:rgba(230,126,0,.03)' : '') }}">
                    <td>
                        <div style="font-weight:600; color:var(--navy)">{{ $rep->nombre }}</div>
                        @if($rep->codigo)<div style="font-size:.72rem; color:var(--muted); font-family:'Courier New',monospace">{{ $rep->codigo }}</div>@endif
                    </td>
                    <td style="text-align:center">
                        <span style="font-family:'Oswald',sans-serif; font-size:1.1rem; font-weight:700;
                            color:{{ $est === 'sin_stock' ? 'var(--error)' : ($est === 'critico' ? 'var(--warn)' : 'var(--ok)') }}">
                            {{ $rep->cantidad_stock }}
                        
                    </td>
                    <td style="text-align:center; color:var(--muted)">{{ $rep->stock_minimo }}</td>
                    <td>
                        @if($est === 'sin_stock') <span class="stock-sin">Sin stock
                        @elseif($est === 'critico') <span class="stock-critico">Crítico
                        @else <span class="stock-ok">OK
                        @endif
                    </td>
                    <td style="font-size:.83rem; color:var(--muted)">{{ $rep->proveedor?->nombre ?? '—' }}</td>
                    <td style="font-size:.83rem; color:var(--muted)">{{ $rep->ubicacion_taller ?? '—' }}</td>
                    <td style="text-align:right; font-size:.88rem">${{ number_format($rep->costo, 2, ',', '.') }}</td>
                    <td style="text-align:right; font-weight:600; color:var(--navy)">
                        ${{ number_format($rep->cantidad_stock * $rep->costo, 2, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach
@endsection
