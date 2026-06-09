@extends('layouts.app')
@section('title', 'Margen por Trabajo')
@section('topbar-title', 'Margen de Ganancia')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Contabilidad</div>
            <h1 class="page-title">Margen de Ganancia por Trabajo</h1>
            <p class="page-subtitle">Comparación costo vs precio de venta por factura</p>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="ta-card" style="margin-bottom:20px">
    <div class="ta-card-body" style="padding:14px 20px">
        <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end">
            <div>
                <label class="ta-label">Desde</label>
                <input type="date" name="desde" class="ta-input" style="width:170px" value="{{ $desde }}">
            </div>
            <div>
                <label class="ta-label">Hasta</label>
                <input type="date" name="hasta" class="ta-input" style="width:170px" value="{{ $hasta }}">
            </div>
            <button type="submit" class="btn-primary-ta" style="height:40px"><i class="bi bi-search"></i> Ver</button>
        </form>
    </div>
</div>

{{-- KPIs --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:20px">
    <div class="ta-card" style="padding:20px; border-left:4px solid var(--blue)">
        <div style="font-size:.74rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px">Total Ventas</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.6rem; color:var(--navy)">${{ number_format($totalVentas, 0, ',', '.') }}</div>
    </div>
    <div class="ta-card" style="padding:20px; border-left:4px solid var(--warn)">
        <div style="font-size:.74rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px">Total Costos</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.6rem; color:var(--warn)">${{ number_format($totalCostos, 0, ',', '.') }}</div>
    </div>
    <div class="ta-card" style="padding:20px; border-left:4px solid var(--ok)">
        <div style="font-size:.74rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px">Ganancia Total</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.6rem; color:var(--ok)">${{ number_format($totalGanancia, 0, ',', '.') }}</div>
    </div>
    <div class="ta-card" style="padding:20px; border-left:4px solid {{ $margenPromedio >= 30 ? 'var(--ok)' : ($margenPromedio >= 15 ? 'var(--warn)' : 'var(--error)') }}">
        <div style="font-size:.74rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px">Margen Promedio</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.6rem; color:{{ $margenPromedio >= 30 ? 'var(--ok)' : ($margenPromedio >= 15 ? 'var(--warn)' : 'var(--error)') }}">
            {{ $margenPromedio }}%
        </div>
    </div>
</div>

{{-- Tabla de margen por factura --}}
<div class="ta-card">
    <div class="ta-card-header">
        <div class="ta-card-title"><i class="bi bi-percent" style="color:var(--blue)"></i> Detalle por Factura</div>
        <div style="font-size:.82rem; color:var(--muted)">{{ $facturas->count() }} facturas</div>
    </div>
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>Factura</th>
                    <th>Cliente / Vehículo</th>
                    <th style="text-align:right">Venta</th>
                    <th style="text-align:right">Costo M.O.</th>
                    <th style="text-align:right">Costo Rep.</th>
                    <th style="text-align:right">Ganancia</th>
                    <th style="text-align:right">Margen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($facturas as $f)
                <tr>
                    <td>
                        <a href="{{ route('admin.facturacion.show', $f['factura']) }}" style="font-family:'Oswald',sans-serif; color:var(--accent); font-size:.9rem; text-decoration:none">
                            {{ $f['factura']->numero }}
                        </a>
                        <div style="font-size:.74rem; color:var(--muted)">{{ $f['factura']->created_at->format('d/m/Y') }}</div>
                    </td>
                    <td>
                        <div style="font-size:.86rem; font-weight:600; color:var(--navy)">{{ $f['factura']->cliente->nombreCompleto() }}</div>
                        <div style="font-size:.76rem; color:var(--muted)">{{ $f['factura']->ingreso->vehiculo->marca->nombre }} {{ $f['factura']->ingreso->vehiculo->modelo->nombre }}</div>
                    </td>
                    <td style="text-align:right; font-weight:600; color:var(--navy)">${{ number_format($f['factura']->total, 0, ',', '.') }}</td>
                    <td style="text-align:right; color:var(--muted)">${{ number_format($f['costo_mo'], 0, ',', '.') }}</td>
                    <td style="text-align:right; color:var(--muted)">${{ number_format($f['costo_rep'], 0, ',', '.') }}</td>
                    <td style="text-align:right; font-weight:700; color:{{ $f['ganancia'] >= 0 ? 'var(--ok)' : 'var(--error)' }}">
                        ${{ number_format($f['ganancia'], 0, ',', '.') }}
                    </td>
                    <td style="text-align:right">
                        @php $color = $f['margen'] >= 30 ? 'var(--ok)' : ($f['margen'] >= 15 ? 'var(--warn)' : 'var(--error)'); @endphp
                        <span style="font-weight:700; color:{{ $color }}">{{ $f['margen'] }}%</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:56px; color:var(--muted)">
                        <i class="bi bi-graph-up" style="font-size:2.5rem; display:block; margin-bottom:14px; opacity:.3"></i>
                        No hay facturas en este período
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection