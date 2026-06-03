<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        body { margin: 0; padding: 20px; color: #1a1a1a; }
        .header { background: #1255a1; color: white; padding: 15px 20px; margin: -20px -20px 20px -20px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; }
        .header p  { margin: 0; opacity: .85; font-size: 9px; }
        .header-meta { float: right; text-align: right; font-size: 9px; }
        .resumen-cards { display: flex; gap: 10px; margin-bottom: 20px; }
        .card-res { flex: 1; border: 1px solid #c0d3e8; border-radius: 6px; padding: 10px; text-align: center; }
        .card-res .valor { font-size: 20px; font-weight: bold; color: #1255a1; }
        .card-res .etiq { font-size: 8px; color: #6b7280; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #0b1c2e; color: white; padding: 6px 8px; text-align: left; font-size: 9px; }
        td { padding: 5px 8px; border-bottom: 1px solid #f0f0f0; vertical-align: top; }
        tr:nth-child(even) td { background: #f9fafb; }
        .seccion-titulo { font-size: 12px; font-weight: bold; margin: 16px 0 6px; padding-bottom: 4px; border-bottom: 2px solid #1255a1; color: #1255a1; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; background: #f3f4f6; padding: 6px 20px; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
<div class="header">
    <div class="header-meta">
        <div>Generado por: {{ $reporte->generadoPor->nombreCompleto() }}</div>
        <div>{{ $reporte->fecha_generacion ? $reporte->fecha_generacion->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }} hs</div>
        <div>
            Período:
            {{ $reporte->fecha_inicio ? \Carbon\Carbon::parse($reporte->fecha_inicio)->format('d/m/Y') : '—' }}
            –
            {{ $reporte->fecha_fin ? \Carbon\Carbon::parse($reporte->fecha_fin)->format('d/m/Y') : '—' }}
        </div>
    </div>
    <h1>Taller Aquino</h1>
    <p>REPORTE DE TRABAJOS REALIZADOS · Montecarlo, Misiones</p>
</div>

<div class="resumen-cards">
    <div class="card-res">
        <div class="valor">${{ number_format($totalIngresos, 0, ',', '.') }}</div>
        <div class="etiq">TOTAL INGRESOS</div>
    </div>
    <div class="card-res">
        <div class="valor">{{ $cantidadTrabajos }}</div>
        <div class="etiq">TOTAL TRABAJOS</div>
    </div>
    <div class="card-res">
        <div class="valor">{{ $categorias->count() }}</div>
        <div class="etiq">CATEGORÍAS</div>
    </div>
    @if($tecnicoDestacado)
    <div class="card-res">
        <div class="valor" style="font-size:12px">{{ $tecnicoDestacado->name }}</div>
        <div class="etiq">TÉCNICO DESTACADO</div>
    </div>
    @endif
</div>

@if($trabajos->isEmpty())
    <p style="text-align:center; color:#9ca3af; padding:30px">
        No se encontraron trabajos registrados en el período solicitado.
    </p>
@else
@foreach($trabajos->groupBy('tipo_servicio') as $tipo => $grupo)
<div class="seccion-titulo">
    {{ strtoupper(str_replace('_', ' ', $tipo)) }} ({{ $grupo->count() }})
    — ${{ number_format($grupo->sum('costo_total'), 0, ',', '.') }}
</div>
<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Vehículo</th>
            <th>Descripción</th>
            <th>Técnico</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($grupo as $t)
        <tr>
            <td>
                {{ $t->fecha_trabajo ? $t->fecha_trabajo->format('d/m/Y') : '—' }}<br>
                <span style="color:#9ca3af">{{ $t->fecha_trabajo ? $t->fecha_trabajo->format('H:i') : '' }}</span>
            </td>
            <td>
                {{ optional($t->ingreso->cliente)->nombreCompleto() ?? '—' }}<br>
                <span style="color:#9ca3af">{{ optional($t->ingreso->cliente)->telefono }}</span>
            </td>
            <td>
                {{ optional(optional($t->ingreso->vehiculo)->marca)->nombre }}
                {{ optional(optional($t->ingreso->vehiculo)->modelo)->nombre }}<br>
                <b>{{ optional($t->ingreso->vehiculo)->patente }}</b>
            </td>
            <td style="max-width:160px">{{ Str::limit($t->descripcion_trabajo, 70) }}</td>
            <td>{{ optional($t->mecanico)->name ?? '—' }}</td>
            <td><b>${{ number_format($t->costo_total, 2, ',', '.') }}</b></td>
        </tr>
        @endforeach
        <tr>
            <td colspan="5" style="text-align:right; font-weight:bold">
                Subtotal {{ str_replace('_',' ',$tipo) }}:
            </td>
            <td><b>${{ number_format($grupo->sum('costo_total'), 2, ',', '.') }}</b></td>
        </tr>
    </tbody>
</table>
@endforeach

<div style="margin-top:16px; text-align:right; padding:10px; background:#f0f5ff; border-radius:6px; border:1px solid #c0d3e8">
    <span style="font-size:13px; font-weight:bold; color:#0b1c2e">
        TOTAL GENERAL: ${{ number_format($totalIngresos, 2, ',', '.') }}
    </span>
</div>
@endif

<div class="footer">
    Sistema de Gestión – Taller Aquino · Montecarlo, Misiones · Reporte generado el {{ now()->format('d/m/Y H:i') }}
</div>
</body>
</html>
