@extends('layouts.app')
@section('title', 'Mis Facturas')
@section('topbar-title', 'Mis Facturas')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Portal del Cliente</div>
            <h1 class="page-title">Mis Facturas</h1>
            <p class="page-subtitle">Historial de facturas y presupuestos de tus servicios</p>
        </div>
    </div>
</div>

<div class="ta-card">
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>N° Factura</th>
                    <th>Vehículo</th>
                    <th>Fecha</th>
                    <th style="text-align:right">Total</th>
                    <th style="text-align:right">Pagado</th>
                    <th style="text-align:right">Saldo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($facturas as $factura)
                <tr>
                    <td>
                        <span class="nro-seguimiento" style="font-size:.9rem">{{ $factura->numero }}</span>
                        @if($factura->tipo === 'presupuesto')
                        <div style="font-size:.7rem; color:var(--warn); font-weight:600">PRESUPUESTO</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:.86rem; font-weight:600; color:var(--navy)">
                            {{ $factura->ingreso->vehiculo->marca->nombre }} {{ $factura->ingreso->vehiculo->modelo->nombre }}
                        </div>
                        <div style="font-family:'Oswald',sans-serif; font-size:.74rem; color:var(--accent); letter-spacing:.06em">
                            {{ $factura->ingreso->vehiculo->patente }}
                        </div>
                    </td>
                    <td style="font-size:.84rem; color:var(--muted)">{{ $factura->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:right; font-family:'Oswald',sans-serif; font-size:.95rem; color:var(--navy)">
                        ${{ number_format($factura->total, 0, ',', '.') }}
                    </td>
                    <td style="text-align:right; font-size:.88rem; color:var(--ok); font-weight:600">
                        ${{ number_format($factura->totalPagado(), 0, ',', '.') }}
                    </td>
                    <td style="text-align:right; font-size:.88rem; font-weight:600; color:{{ $factura->saldoPendiente() > 0 ? 'var(--warn)' : 'var(--ok)' }}">
                        ${{ number_format($factura->saldoPendiente(), 0, ',', '.') }}
                    </td>
                    <td>
                        @php
                        $badgeColor = match($factura->estado) {
                            'pagada'    => 'finalizado',
                            'parcial'   => 'en_proceso',
                            'pendiente' => 'pendiente',
                            default     => 'cancelado',
                        };
                        @endphp
                        <span class="ta-badge badge-{{ $badgeColor }}">{{ $factura->etiquetaEstado() }}</span>
                    </td>
                    <td>
                        <a href="{{ route('cliente.facturas.show', $factura) }}" class="btn-secondary-ta" style="padding:6px 12px; font-size:.8rem">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:56px; color:var(--muted)">
                        <i class="bi bi-receipt" style="font-size:2.5rem; display:block; margin-bottom:14px; opacity:.3"></i>
                        No tenés facturas registradas aún
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($facturas->hasPages())
    <div style="padding:14px 20px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px">
        <div style="font-size:.82rem; color:var(--muted)">
            Mostrando {{ $facturas->firstItem() }}–{{ $facturas->lastItem() }} de {{ $facturas->total() }} facturas
        </div>
        <div style="display:flex; gap:6px">
            @if($facturas->onFirstPage())
            <span style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--muted); font-size:.84rem; background:var(--card)">‹ Anterior</span>
            @else
            <a href="{{ $facturas->previousPageUrl() }}" style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--text); font-size:.84rem; background:white; text-decoration:none">‹ Anterior</a>
            @endif
            @foreach($facturas->getUrlRange(1, $facturas->lastPage()) as $page => $url)
            @if($page == $facturas->currentPage())
            <span style="padding:6px 12px; border-radius:7px; background:var(--blue); color:white; font-size:.84rem; font-weight:600">{{ $page }}</span>
            @else
            <a href="{{ $url }}" style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--text); font-size:.84rem; background:white; text-decoration:none">{{ $page }}</a>
            @endif
            @endforeach
            @if($facturas->hasMorePages())
            <a href="{{ $facturas->nextPageUrl() }}" style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--text); font-size:.84rem; background:white; text-decoration:none">Siguiente ›</a>
            @else
            <span style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--muted); font-size:.84rem; background:var(--card)">Siguiente ›</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection