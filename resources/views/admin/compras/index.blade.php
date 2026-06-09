@extends('layouts.app')
@section('title', 'Órdenes de Compra')
@section('topbar-title', 'Órdenes de Compra')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Finanzas · Compras</div>
            <h1 class="page-title">Órdenes de Compra</h1>
            <p class="page-subtitle">Compras a proveedores y recepción de mercadería</p>
        </div>
        <a href="{{ route('admin.compras.crear') }}" class="btn-primary-ta">
            <i class="bi bi-plus-circle"></i> Nueva Orden
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
                    <option value="enviada"          {{ request('estado') === 'enviada' ? 'selected' : '' }}>Enviada</option>
                    <option value="recibida_parcial" {{ request('estado') === 'recibida_parcial' ? 'selected' : '' }}>Recibida parcial</option>
                    <option value="recibida"         {{ request('estado') === 'recibida' ? 'selected' : '' }}>Recibida</option>
                    <option value="cancelada"        {{ request('estado') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div>
                <label class="ta-label">Proveedor</label>
                <select name="proveedor_id" class="ta-input ta-select" style="width:200px">
                    <option value="">Todos los proveedores</option>
                    @foreach($proveedores as $p)
                    <option value="{{ $p->id }}" {{ request('proveedor_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:8px; align-items:flex-end">
                <button type="submit" class="btn-primary-ta" style="height:40px"><i class="bi bi-funnel"></i> Filtrar</button>
                <a href="{{ route('admin.compras.index') }}" class="btn-secondary-ta" style="height:40px">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="ta-card">
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>N° Orden</th>
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Fecha esperada</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordenes as $orden)
                <tr>
                    <td>
                        <span class="nro-seguimiento" style="font-size:.9rem">{{ $orden->numero }}</span>
                    </td>
                    <td>
                        <div style="font-weight:600; font-size:.88rem; color:var(--navy)">{{ $orden->proveedor->nombre }}</div>
                        <div style="font-size:.76rem; color:var(--muted)">{{ $orden->proveedor->telefono }}</div>
                    </td>
                    <td style="font-size:.84rem; color:var(--muted)">{{ $orden->created_at->format('d/m/Y') }}</td>
                    <td style="font-size:.84rem; color:var(--muted)">
                        {{ $orden->fecha_esperada ? $orden->fecha_esperada->format('d/m/Y') : '—' }}
                    </td>
                    <td style="font-family:'Oswald',sans-serif; font-size:.95rem; color:var(--navy)">
                        ${{ number_format($orden->total, 0, ',', '.') }}
                    </td>
                    <td>
                        @php
                        $badgeColor = match($orden->estado) {
                            'enviada'          => 'confirmado',
                            'recibida_parcial' => 'en_proceso',
                            'recibida'         => 'finalizado',
                            'cancelada'        => 'cancelado',
                            default            => 'pendiente',
                        };
                        @endphp
                        <span class="ta-badge badge-{{ $badgeColor }}">{{ $orden->etiquetaEstado() }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.compras.show', $orden) }}" class="btn-secondary-ta" style="padding:6px 12px; font-size:.8rem">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:56px; color:var(--muted)">
                        <i class="bi bi-cart" style="font-size:2.5rem; display:block; margin-bottom:14px; opacity:.3"></i>
                        No hay órdenes de compra
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($ordenes->hasPages())
    <div style="padding:14px 20px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px">
        <div style="font-size:.82rem; color:var(--muted)">
            Mostrando {{ $ordenes->firstItem() }}–{{ $ordenes->lastItem() }} de {{ $ordenes->total() }} órdenes
        </div>
        <div style="display:flex; gap:6px">
            @if($ordenes->onFirstPage())
            <span style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--muted); font-size:.84rem; background:var(--card)">‹ Anterior</span>
            @else
            <a href="{{ $ordenes->previousPageUrl() }}" style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--text); font-size:.84rem; background:white; text-decoration:none">‹ Anterior</a>
            @endif
            @foreach($ordenes->getUrlRange(1, $ordenes->lastPage()) as $page => $url)
            @if($page == $ordenes->currentPage())
            <span style="padding:6px 12px; border-radius:7px; background:var(--blue); color:white; font-size:.84rem; font-weight:600">{{ $page }}</span>
            @else
            <a href="{{ $url }}" style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--text); font-size:.84rem; background:white; text-decoration:none">{{ $page }}</a>
            @endif
            @endforeach
            @if($ordenes->hasMorePages())
            <a href="{{ $ordenes->nextPageUrl() }}" style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--text); font-size:.84rem; background:white; text-decoration:none">Siguiente ›</a>
            @else
            <span style="padding:6px 12px; border-radius:7px; border:1.5px solid var(--border); color:var(--muted); font-size:.84rem; background:var(--card)">Siguiente ›</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection