<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        body { margin: 0; padding: 20px; color: #1a1a1a; }
        .header { background: #C0392B; color: white; padding: 15px 20px; margin: -20px -20px 20px -20px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; }
        .header p { margin: 0; opacity: .85; font-size: 9px; }
        .header-meta { float: right; text-align: right; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #2C3E50; color: white; padding: 6px 8px; text-align: left; font-size: 9px; }
        td { padding: 5px 8px; border-bottom: 1px solid #f0f0f0; }
        tr:nth-child(even) td { background: #f9fafb; }
        .sin-stock td { background: #fee2e2 !important; }
        .stock-bajo td { background: #fef9c3 !important; }
        .seccion { font-size: 12px; font-weight: bold; margin: 16px 0 6px; padding-bottom: 4px; border-bottom: 2px solid #C0392B; color: #C0392B; }
        .resumen { display: flex; gap: 10px; margin-bottom: 20px; }
        .card-res { flex: 1; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; text-align: center; }
        .card-res .valor { font-size: 18px; font-weight: bold; color: #C0392B; }
        .card-res .etiq { font-size: 8px; color: #6b7280; margin-top: 2px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; background: #f3f4f6; padding: 6px 20px; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
<div class="header">
    <div class="header-meta">
        <div>Generado por: {{ auth()->user()->nombreCompleto() }}</div>
        <div>{{ now()->format('d/m/Y H:i') }} hs</div>
    </div>
    <h1>Taller Aquino</h1>
    <p>REPORTE DE STOCK DE REPUESTOS · Montecarlo, Misiones</p>
</div>

<div class="resumen">
    <div class="card-res">
        <div class="valor">{{ $repuestos->count() }}</div>
        <div class="etiq">TOTAL ITEMS</div>
    </div>
    <div class="card-res">
        <div class="valor" style="color:#dc2626">{{ $sinStock }}</div>
        <div class="etiq">SIN STOCK</div>
    </div>
    <div class="card-res">
        <div class="valor" style="color:#d97706">{{ $alertas }}</div>
        <div class="etiq">CRÍTICOS</div>
    </div>
    <div class="card-res">
        <div class="valor">${{ number_format($valorTotal, 0, ',', '.') }}</div>
        <div class="etiq">VALOR TOTAL</div>
    </div>
</div>

@foreach($repuestos->groupBy('categoria') as $cat => $items)
<div class="seccion">{{ strtoupper($cat) }} ({{ $items->count() }})</div>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th style="text-align:center">Stock</th>
            <th style="text-align:center">Mín.</th>
            <th>Estado</th>
            <th>Proveedor</th>
            <th>Ubicación</th>
            <th>Costo Unit.</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $rep)
        @php $estado = $rep->estadoStock(); @endphp
        <tr class="{{ $estado === 'sin_stock' ? 'sin-stock' : ($estado === 'critico' ? 'stock-bajo' : '') }}">
            <td>{{ $rep->id }}</td>
            <td><b>{{ $rep->nombre }}</b>
                @if($rep->codigo)<br><span style="color:#9ca3af">{{ $rep->codigo }}</span>@endif
            </td>
            <td style="text-align:center; font-weight:bold;
                color:{{ $estado !== 'disponible' ? '#dc2626' : '#059669' }}">
                {{ $rep->cantidad_stock }}
            </td>
            <td style="text-align:center">{{ $rep->stock_minimo }}</td>
            <td>{{ $estado === 'sin_stock' ? 'SIN STOCK' : ($estado === 'critico' ? 'CRÍTICO' : 'OK') }}</td>
            <td>{{ $rep->proveedor?->nombre ?? '–' }}</td>
            <td>{{ $rep->ubicacion_taller ?? '–' }}</td>
            <td>${{ number_format($rep->costo, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach

<div class="footer">
    Sistema de Gestión – Taller Aquino · Reporte generado el {{ now()->format('d/m/Y H:i') }}
</div>
</body>
</html>
