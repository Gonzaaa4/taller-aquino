@extends('layouts.app')
@section('title', 'Rentabilidad')
@section('topbar-title', 'Rentabilidad Mensual')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Contabilidad</div>
            <h1 class="page-title">Rentabilidad Mensual</h1>
            <p class="page-subtitle">Ingresos, egresos y ganancia neta por mes</p>
        </div>
    </div>
</div>

{{-- Filtro año --}}
<div class="ta-card" style="margin-bottom:20px">
    <div class="ta-card-body" style="padding:14px 20px">
        <form method="GET" style="display:flex; gap:12px; align-items:flex-end">
            <div>
                <label class="ta-label">Año</label>
                <select name="anio" class="ta-input ta-select" style="width:120px">
                    @foreach($anios as $a)
                    <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary-ta" style="height:40px"><i class="bi bi-search"></i> Ver</button>
        </form>
    </div>
</div>

{{-- KPIs anuales --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:20px">
    <div class="ta-card" style="padding:20px; border-left:4px solid var(--ok)">
        <div style="font-size:.74rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px">Total Ingresos {{ $anio }}</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.8rem; color:var(--ok)">${{ number_format($totalIngresos, 0, ',', '.') }}</div>
    </div>
    <div class="ta-card" style="padding:20px; border-left:4px solid var(--error)">
        <div style="font-size:.74rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px">Total Egresos {{ $anio }}</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.8rem; color:var(--error)">${{ number_format($totalEgresos, 0, ',', '.') }}</div>
    </div>
    <div class="ta-card" style="padding:20px; border-left:4px solid {{ $totalGanancia >= 0 ? 'var(--blue)' : 'var(--error)' }}">
        <div style="font-size:.74rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px">Ganancia Neta {{ $anio }}</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.8rem; color:{{ $totalGanancia >= 0 ? 'var(--navy)' : 'var(--error)' }}">
            ${{ number_format($totalGanancia, 0, ',', '.') }}
        </div>
    </div>
</div>

{{-- Tabla mensual --}}
<div class="ta-card" style="margin-bottom:20px">
    <div class="ta-card-header">
        <div class="ta-card-title"><i class="bi bi-table" style="color:var(--blue)"></i> Detalle por Mes</div>
    </div>
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>Mes</th>
                    <th style="text-align:right">Ingresos</th>
                    <th style="text-align:right">Egresos</th>
                    <th style="text-align:right">Ganancia</th>
                    <th style="text-align:right">Margen</th>
                </tr>
            </thead>
            <tbody>
                @php $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; @endphp
                @foreach($datos as $d)
                <tr>
                    <td style="font-weight:600; color:var(--navy)">{{ $meses[$d['mes']-1] }}</td>
                    <td style="text-align:right; color:var(--ok); font-weight:600">
                        {{ $d['ingresos'] > 0 ? '$'.number_format($d['ingresos'], 0, ',', '.') : '—' }}
                    </td>
                    <td style="text-align:right; color:var(--error); font-weight:600">
                        {{ $d['egresos'] > 0 ? '$'.number_format($d['egresos'], 0, ',', '.') : '—' }}
                    </td>
                    <td style="text-align:right; font-weight:700; color:{{ $d['ganancia'] >= 0 ? 'var(--navy)' : 'var(--error)' }}">
                        {{ $d['ingresos'] > 0 || $d['egresos'] > 0 ? '$'.number_format($d['ganancia'], 0, ',', '.') : '—' }}
                    </td>
                    <td style="text-align:right">
                        @if($d['ingresos'] > 0)
                        @php $margen = $d['ingresos'] > 0 ? round(($d['ganancia'] / $d['ingresos']) * 100, 1) : 0; @endphp
                        <span style="color:{{ $margen >= 0 ? 'var(--ok)' : 'var(--error)' }}; font-weight:600">{{ $margen }}%</span>
                        @else
                        <span style="color:var(--muted)">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="background:var(--light)">
                <tr>
                    <td style="font-family:'Oswald',sans-serif; font-size:.88rem; color:var(--navy); padding:12px 16px">TOTAL ANUAL</td>
                    <td style="text-align:right; font-family:'Oswald',sans-serif; color:var(--ok); padding:12px 16px">${{ number_format($totalIngresos, 0, ',', '.') }}</td>
                    <td style="text-align:right; font-family:'Oswald',sans-serif; color:var(--error); padding:12px 16px">${{ number_format($totalEgresos, 0, ',', '.') }}</td>
                    <td style="text-align:right; font-family:'Oswald',sans-serif; color:{{ $totalGanancia >= 0 ? 'var(--navy)' : 'var(--error)' }}; padding:12px 16px">${{ number_format($totalGanancia, 0, ',', '.') }}</td>
                    <td style="text-align:right; padding:12px 16px">
                        @php $margenAnual = $totalIngresos > 0 ? round(($totalGanancia / $totalIngresos) * 100, 1) : 0; @endphp
                        <span style="color:{{ $margenAnual >= 0 ? 'var(--ok)' : 'var(--error)' }}; font-weight:700">{{ $margenAnual }}%</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- Gráfico de barras --}}
<div class="ta-card">
    <div class="ta-card-header">
        <div class="ta-card-title"><i class="bi bi-bar-chart" style="color:var(--blue)"></i> Gráfico Anual</div>
    </div>
    <div style="padding:20px">
        <canvas id="chartRentabilidad" style="max-height:320px"></canvas>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
const ingresos = {!! json_encode($datos->pluck('ingresos')->values()) !!};
const egresos  = {!! json_encode($datos->pluck('egresos')->values()) !!};
const ganancia = {!! json_encode($datos->pluck('ganancia')->values()) !!};

new Chart(document.getElementById('chartRentabilidad'), {
    type: 'bar',
    data: {
        labels: meses,
        datasets: [
            { label: 'Ingresos', data: ingresos, backgroundColor: 'rgba(15,138,74,.7)', borderRadius: 6 },
            { label: 'Egresos',  data: egresos,  backgroundColor: 'rgba(217,48,37,.7)', borderRadius: 6 },
            { label: 'Ganancia', data: ganancia,  backgroundColor: 'rgba(18,85,161,.7)', borderRadius: 6 },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { ticks: { callback: v => '$' + v.toLocaleString('es-AR') } }
        }
    }
});
</script>
@endpush
@endsection