@extends('layouts.app')
@section('title', 'Facturación')
@section('topbar-title', 'Facturación')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Finanzas</div>
            <h1 class="page-title">Facturación</h1>
            <p class="page-subtitle">Facturas y presupuestos generados</p>
        </div>
        <a href="{{ route('admin.facturacion.caja') }}" class="btn-primary-ta">
            <i class="bi bi-cash-stack"></i> Ver Caja
        </a>
    </div>
</div>

{{-- Filtros --}}
<div class="ta-card" style="margin-bottom:20px">
    <div class="ta-card-body" style="padding:14px 20px">
        <form style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end">
            <div>
                <label class="ta-label">Estado</label>
                <select name="estado" class="ta-input ta-select" style="width:180px">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="parcial"   {{ request('estado') === 'parcial' ? 'selected' : '' }}>Pago parcial</option>
                    <option value="pagada"    {{ request('estado') === 'pagada' ? 'selected' : '' }}>Pagada</option>
                    <option value="anulada"   {{ request('estado') === 'anulada' ? 'selected' : '' }}>Anulada</option>
                </select>
            </div>
            <div style="display:flex; gap:8px; align-items:flex-end">
                <button type="submit" class="btn-primary-ta" style="height:40px"><i class="bi bi-funnel"></i> Filtrar</button>
                <a href="{{ route('admin.facturacion.index') }}" class="btn-secondary-ta" style="height:40px">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="ta-card">
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>N° Factura</th>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Pagado</th>
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
                        <div style="font-weight:600; font-size:.88rem; color:var(--navy)">{{ $factura->cliente->nombreCompleto() }}</div>
                    </td>
                    <td>
                        <div style="font-size:.84rem; color:var(--navy)">{{ $factura->ingreso->vehiculo->marca->nombre }} {{ $factura->ingreso->vehiculo->modelo->nombre }}</div>
                        <div style="font-family:'Oswald',sans-serif; font-size:.74rem; color:var(--accent); letter-spacing:.06em">{{ $factura->ingreso->vehiculo->patente }}</div>
                    </td>
                    <td style="font-size:.84rem; color:var(--muted)">{{ $factura->created_at->format('d/m/Y') }}</td>
                    <td style="font-family:'Oswald',sans-serif; font-size:.95rem; color:var(--navy)">${{ number_format($factura->total, 0, ',', '.') }}</td>
                    <td style="font-size:.86rem; color:var(--ok); font-weight:600">${{ number_format($factura->totalPagado(), 0, ',', '.') }}</td>
                    <td>
                        @php
                        $badgeColor = match($factura->estado) {
                            'pagada' => 'finalizado',
                            'parcial' => 'en_proceso',
                            'pendiente' => 'pendiente',
                            'anulada' => 'cancelado',
                            default => 'pendiente'
                        };
                        @endphp
                        <span class="ta-badge badge-{{ $badgeColor }}">{{ $factura->etiquetaEstado() }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.facturacion.show', $factura) }}" class="btn-secondary-ta" style="padding:6px 12px; font-size:.8rem">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:56px; color:var(--muted)">
                        <i class="bi bi-receipt" style="font-size:2.5rem; display:block; margin-bottom:14px; opacity:.3"></i>
                        No hay facturas generadas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($facturas->hasPages())
    <div style="padding:16px 20px; border-top:1px solid var(--border)">{{ $facturas->withQueryString()->links() }}</div>
    @endif
</div>
@endsection