@extends('layouts.app')
@section('title', 'Factura ' . $factura->numero)
@section('topbar-title', 'Detalle de Factura')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Portal del Cliente · Mis Facturas</div>
            <h1 class="page-title">{{ $factura->numero }}</h1>
            <p class="page-subtitle">{{ $factura->tipo === 'presupuesto' ? 'Presupuesto' : 'Factura' }} · {{ $factura->created_at->format('d/m/Y') }}</p>
        </div>
        <a href="{{ route('cliente.facturas.index') }}" class="btn-secondary-ta">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div style="display:grid; grid-template-columns:1.6fr 1fr; gap:18px; align-items:start">
    {{-- Columna izquierda --}}
    <div>
        {{-- Datos vehículo --}}
        <div class="ta-card" style="margin-bottom:18px">
            <div class="ta-card-header">
                <div class="ta-card-title"><i class="bi bi-car-front" style="color:var(--blue)"></i> Vehículo</div>
            </div>
            <div style="padding:18px 20px; display:grid; grid-template-columns:1fr 1fr; gap:16px">
                <div>
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Vehículo</div>
                    <div style="font-weight:600; color:var(--navy)">{{ $factura->ingreso->vehiculo->marca->nombre }} {{ $factura->ingreso->vehiculo->modelo->nombre }} {{ $factura->ingreso->vehiculo->anio }}</div>
                    <div style="font-family:'Oswald',sans-serif; font-size:.9rem; color:var(--accent)">{{ $factura->ingreso->vehiculo->patente }}</div>
                </div>
                <div>
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Fecha</div>
                    <div style="font-weight:600; color:var(--navy)">{{ $factura->created_at->format('d/m/Y H:i') }} hs</div>
                </div>
            </div>
        </div>

        {{-- Detalle --}}
        <div class="ta-card" style="margin-bottom:18px">
            <div class="ta-card-header">
                <div class="ta-card-title"><i class="bi bi-list-ul" style="color:var(--blue)"></i> Detalle</div>
            </div>
            <div style="padding:18px 20px">
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
                        <tr style="background:var(--light)">
                            <td style="padding:12px 8px; font-family:'Oswald',sans-serif; font-size:1rem; color:var(--navy)">TOTAL</td>
                            <td style="padding:12px 8px; text-align:right; font-family:'Oswald',sans-serif; font-size:1.2rem; color:var(--navy)">${{ number_format($factura->total, 0, ',', '.') }}</td>
                        </tr>
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

        {{-- Pagos --}}
        <div class="ta-card">
            <div class="ta-card-header">
                <div class="ta-card-title"><i class="bi bi-cash-coin" style="color:var(--ok)"></i> Pagos realizados</div>
            </div>
            <div style="padding:18px 20px">
                @forelse($factura->pagos as $pago)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 14px; background:var(--card); border-radius:9px; margin-bottom:8px">
                    <div>
                        <div style="font-weight:600; color:var(--navy); font-size:.9rem">{{ $pago->etiquetaMetodo() }}</div>
                        <div style="font-size:.76rem; color:var(--muted)">{{ $pago->created_at->format('d/m/Y H:i') }} hs</div>
                    </div>
                    <div style="font-family:'Oswald',sans-serif; font-size:1.1rem; color:var(--ok)">
                        ${{ number_format($pago->monto, 0, ',', '.') }}
                    </div>
                </div>
                @empty
                <div style="text-align:center; padding:20px; color:var(--muted); font-size:.86rem">
                    Sin pagos registrados
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Columna derecha --}}
    <div>
        {{-- Resumen de cuenta --}}
        <div style="background:linear-gradient(135deg,#0b1c2e,#1255a1); border-radius:12px; padding:22px">
            <div style="font-size:.72rem; color:rgba(255,255,255,.5); text-transform:uppercase; letter-spacing:.08em; margin-bottom:14px">Estado de cuenta</div>
            <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,.1)">
                <span style="font-size:.84rem; color:rgba(255,255,255,.6)">Total factura</span>
                <span style="font-size:.92rem; font-weight:600; color:white">${{ number_format($factura->total, 0, ',', '.') }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,.1)">
                <span style="font-size:.84rem; color:rgba(255,255,255,.6)">Pagado</span>
                <span style="font-size:.92rem; font-weight:600; color:#4ade80">${{ number_format($factura->totalPagado(), 0, ',', '.') }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:14px 0 0; margin-top:6px; border-top:2px solid rgba(255,255,255,.2)">
                <span style="font-family:'Oswald',sans-serif; font-size:1rem; color:white; letter-spacing:.04em">SALDO</span>
                <span style="font-family:'Oswald',sans-serif; font-size:1.6rem; color:{{ $factura->saldoPendiente() <= 0 ? '#4ade80' : '#fbbf24' }}">
                    ${{ number_format($factura->saldoPendiente(), 0, ',', '.') }}
                </span>
            </div>
            <div style="margin-top:16px; text-align:center">
                @php
                $badgeColor = match($factura->estado) {
                    'pagada'    => '#4ade80',
                    'parcial'   => '#fbbf24',
                    'pendiente' => 'rgba(255,255,255,.6)',
                    default     => '#f87171',
                };
                @endphp
                <span style="display:inline-block; padding:5px 16px; border-radius:20px; background:rgba(255,255,255,.12); color:{{ $badgeColor }}; font-size:.82rem; font-weight:600">
                    {{ $factura->etiquetaEstado() }}
                </span>
            </div>
        </div>

        @if($factura->saldoPendiente() > 0)
        <div style="margin-top:14px; background:rgba(230,126,0,.08); border:1px solid rgba(230,126,0,.2); border-radius:10px; padding:14px 16px; font-size:.84rem; color:#a85e00">
            <i class="bi bi-exclamation-triangle" style="margin-right:6px"></i>
            Tenés un saldo pendiente de <strong>${{ number_format($factura->saldoPendiente(), 0, ',', '.') }}</strong>. Contactá al taller para coordinar el pago.
        </div>
        @endif
    </div>
</div>
@endsection