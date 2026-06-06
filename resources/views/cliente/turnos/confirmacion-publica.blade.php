<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turno Solicitado — Taller Aquino</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Source Sans 3',sans-serif; background:#f0f4f8; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .card { background:white; border-radius:16px; padding:40px; max-width:480px; width:100%; box-shadow:0 4px 24px rgba(0,0,0,.1); text-align:center; }
        .icon { width:64px; height:64px; background:#e6f7ee; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; }
        .icon i { font-size:2rem; color:#0f8a4a; }
        h1 { font-family:'Oswald',sans-serif; font-size:1.5rem; color:#0b1c2e; margin-bottom:8px; }
        p { color:#5a7a95; font-size:.95rem; margin-bottom:24px; }
        .nro { font-family:'Oswald',sans-serif; font-size:2rem; color:#1255a1; letter-spacing:.1em; background:#f0f7ff; border-radius:10px; padding:16px; margin:20px 0; }
        .detail { background:#f8fafc; border-radius:10px; padding:16px; text-align:left; margin-bottom:24px; }
        .detail-row { display:flex; justify-content:space-between; padding:7px 0; border-bottom:1px solid #e2e8f0; font-size:.88rem; }
        .detail-row:last-child { border-bottom:none; }
        .detail-label { color:#5a7a95; }
        .detail-value { font-weight:600; color:#0b1c2e; }
        .warning { background:#fff8ec; border:1px solid rgba(230,126,0,.2); border-radius:8px; padding:12px 14px; font-size:.82rem; color:#cc5500; margin-bottom:24px; text-align:left; display:flex; gap:8px; align-items:flex-start; }
        .actions { display:flex; gap:10px; justify-content:center; flex-wrap:wrap; }
        .btn { display:inline-flex; align-items:center; gap:8px; padding:11px 22px; border-radius:8px; font-size:.92rem; font-weight:600; text-decoration:none; }
        .btn-primary { background:#1255a1; color:white; }
        .btn-primary:hover { background:#1a6fcc; }
        .btn-secondary { background:#f0f4f8; color:#0b1c2e; }
        .btn-secondary:hover { background:#e2e8f0; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon"><i class="bi bi-check-circle-fill"></i></div>
    <h1>¡Turno Solicitado!</h1>
    <p>Tu solicitud fue registrada. Guardá tu número de seguimiento para rastrear el estado.</p>

    <div class="nro">{{ $turno->numero_seguimiento }}</div>

    <div class="detail">
        <div class="detail-row">
            <span class="detail-label">Vehículo</span>
            <span class="detail-value">{{ $turno->vehiculo->marca->nombre }} {{ $turno->vehiculo->modelo->nombre }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Patente</span>
            <span class="detail-value">{{ $turno->vehiculo->patente }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Fecha y hora</span>
            <span class="detail-value">{{ $turno->fecha_hora_turno->format('d/m/Y H:i') }} hs</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Servicio</span>
            <span class="detail-value">{{ ucfirst(str_replace('_',' ',$turno->tipo_servicio)) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Estado</span>
            <span class="detail-value" style="color:#e67e00">Pendiente de confirmación</span>
        </div>
    </div>

    <div class="warning">
        <i class="bi bi-exclamation-triangle-fill" style="flex-shrink:0; margin-top:2px"></i>
        El taller confirmará tu turno a la brevedad. Se permiten hasta 2 cancelaciones por mes con 48 h de anticipación.
    </div>

    <div class="actions">
        <a href="{{ route('login') }}" class="btn btn-primary">
            <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
        </a>
        <a href="{{ route('consultar.estado') }}" class="btn btn-secondary">
            <i class="bi bi-search"></i> Consultar estado
        </a>
    </div>
</div>
</body>
</html>
