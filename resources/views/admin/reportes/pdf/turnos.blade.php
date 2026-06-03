<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        body { margin: 0; padding: 20px; }
        .header { background: #C0392B; color: white; padding: 15px 20px; margin: -20px -20px 20px -20px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; }
        .header-meta { float: right; text-align: right; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #2C3E50; color: white; padding: 6px 8px; font-size: 9px; text-align: left; }
        td { padding: 5px 8px; border-bottom: 1px solid #f0f0f0; }
        tr:nth-child(even) td { background: #f9fafb; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; background: #f3f4f6; padding: 6px 20px; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
<div class="header">
    <div class="header-meta">
        <div>{{ now()->format('d/m/Y H:i') }} hs</div>
        <div>Período: {{ \Carbon\Carbon::parse($request->fecha_inicio)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($request->fecha_fin)->format('d/m/Y') }}</div>
    </div>
    <h1>Taller Aquino</h1>
    <p>REPORTE DE TURNOS PROGRAMADOS · Montecarlo, Misiones</p>
</div>

<table>
    <thead>
        <tr>
            <th>N° Seguimiento</th>
            <th>Fecha / Hora</th>
            <th>Cliente</th>
            <th>Vehículo / Patente</th>
            <th>Servicio</th>
            <th>Mecánico</th>
            <th>Estado</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($turnos as $t)
        <tr>
            <td><b>{{ $t->numero_seguimiento }}</b></td>
            <td>{{ $t->fecha_hora_turno->format('d/m/Y') }}<br>{{ $t->fecha_hora_turno->format('H:i') }} hs</td>
            <td>{{ $t->cliente->nombreCompleto() }}<br>
                <span style="color:#9ca3af">{{ $t->cliente->telefono }}</span></td>
            <td>{{ $t->vehiculo->marca->nombre }} {{ $t->vehiculo->modelo->nombre }}<br>
                <b>{{ $t->vehiculo->patente }}</b></td>
            <td>{{ ucfirst(str_replace('_',' ',$t->tipo_servicio)) }}</td>
            <td>{{ $t->mecanico?->name ?? 'Sin asignar' }}</td>
            <td>{{ $t->etiquetaEstado() }}</td>
            <td style="max-width:120px">{{ Str::limit($t->observaciones ?? '–', 60) }}</td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;color:#9ca3af;padding:20px">Sin turnos en este período.</td></tr>
        @endforelse
        <tr>
            <td colspan="7" style="text-align:right; font-weight:bold">Total de turnos:</td>
            <td><b>{{ $turnos->count() }}</b></td>
        </tr>
    </tbody>
</table>
<div class="footer">Sistema de Gestión – Taller Aquino · Reporte generado el {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
