@extends('layouts.app')
@section('title', 'Orden ' . $orden->numero)
@section('topbar-title', 'Detalle de Orden de Compra')

@push('styles')
<style>
.comp-section { background:#fff; border-radius:12px; margin-bottom:18px; box-shadow:0 2px 10px rgba(0,0,0,.06); overflow:hidden; }
.comp-header { background:var(--light); border-bottom:1px solid var(--border); padding:12px 20px; display:flex; align-items:center; gap:10px; }
.comp-icon { width:30px; height:30px; background:var(--blue); border-radius:7px; display:flex; align-items:center; justify-content:center; }
.comp-icon i { color:white; font-size:.9rem; }
.comp-title { font-family:'Oswald',sans-serif; font-size:.95rem; color:var(--navy); letter-spacing:.04em; }
.comp-body { padding:20px; }
.field-group { display:flex; flex-direction:column; gap:4px; }
.field-group label { font-size:.76rem; font-weight:700; color:var(--muted); letter-spacing:.05em; text-transform:uppercase; }
.field-input { border:1.5px solid var(--border); border-radius:7px; padding:10px 13px; font-size:.92rem; color:var(--text); outline:none; width:100%; background:#fff; }
.field-input:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(46,141,255,.12); }
.modal-bg { display:none; position:fixed; inset:0; background:rgba(11,28,46,.65); z-index:500; align-items:center; justify-content:center; padding:20px; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Finanzas · Compras</div>
            <h1 class="page-title">{{ $orden->numero }}</h1>
            <p class="page-subtitle">{{ $orden->proveedor->nombre }} · {{ $orden->created_at->format('d/m/Y H:i') }} hs</p>
        </div>
        <a href="{{ route('admin.compras.index') }}" class="btn-secondary-ta">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

@if(session('success'))
<div class="ta-alert success" style="margin-bottom:18px">
    <span class="ta-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
    <div>{{ session('success') }}</div>
</div>
@endif
@if(session('error'))
<div class="ta-alert error" style="margin-bottom:18px">
    <span class="ta-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
    <div>{{ session('error') }}</div>
</div>
@endif

<div style="display:grid; grid-template-columns:1.6fr 1fr; gap:18px; align-items:start">

    {{-- Columna izquierda --}}
    <div>
        {{-- Datos proveedor --}}
        <div class="comp-section">
            <div class="comp-header">
                <div class="comp-icon"><i class="bi bi-building"></i></div>
                <span class="comp-title">DATOS DEL PROVEEDOR</span>
            </div>
            <div class="comp-body" style="display:grid; grid-template-columns:1fr 1fr; gap:16px">
                <div>
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Proveedor</div>
                    <div style="font-weight:600; color:var(--navy)">{{ $orden->proveedor->nombre }}</div>
                    <div style="font-size:.82rem; color:var(--muted)">{{ $orden->proveedor->telefono }}</div>
                    <div style="font-size:.82rem; color:var(--muted)">{{ $orden->proveedor->email }}</div>
                </div>
                <div>
                    <div style="font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Detalles</div>
                    <div style="font-size:.84rem; color:var(--text)">Creada por: <strong>{{ $orden->creadaPor->name }}</strong></div>
                    @if($orden->fecha_esperada)
                    <div style="font-size:.84rem; color:var(--text)">Fecha esperada: <strong>{{ $orden->fecha_esperada->format('d/m/Y') }}</strong></div>
                    @endif
                    @if($orden->observaciones)
                    <div style="font-size:.84rem; color:var(--muted); margin-top:6px">{{ $orden->observaciones }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Items de la orden --}}
        <div class="comp-section">
            <div class="comp-header">
                <div class="comp-icon" style="background:var(--ok)"><i class="bi bi-box-seam"></i></div>
                <span class="comp-title">REPUESTOS PEDIDOS</span>
            </div>
            <div class="comp-body">
                <table style="width:100%; border-collapse:collapse; font-size:.88rem">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border)">
                            <th style="text-align:left; padding:8px; color:var(--muted); font-size:.74rem; text-transform:uppercase">Repuesto</th>
                            <th style="text-align:center; padding:8px; color:var(--muted); font-size:.74rem; text-transform:uppercase">Pedido</th>
                            <th style="text-align:center; padding:8px; color:var(--muted); font-size:.74rem; text-transform:uppercase">Recibido</th>
                            <th style="text-align:center; padding:8px; color:var(--muted); font-size:.74rem; text-transform:uppercase">Pendiente</th>
                            <th style="text-align:right; padding:8px; color:var(--muted); font-size:.74rem; text-transform:uppercase">Precio unit.</th>
                            <th style="text-align:right; padding:8px; color:var(--muted); font-size:.74rem; text-transform:uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orden->items as $item)
                        <tr style="border-bottom:1px solid var(--border)">
                            <td style="padding:10px 8px; font-weight:600; color:var(--navy)">{{ $item->repuesto->nombre }}</td>
                            <td style="padding:10px 8px; text-align:center">{{ $item->cantidad_pedida }}</td>
                            <td style="padding:10px 8px; text-align:center; color:var(--ok); font-weight:600">{{ $item->cantidad_recibida }}</td>
                            <td style="padding:10px 8px; text-align:center">
                                @if($item->pendienteRecibir() > 0)
                                <span style="color:var(--warn); font-weight:600">{{ $item->pendienteRecibir() }}</span>
                                @else
                                <span style="color:var(--ok)"><i class="bi bi-check-circle-fill"></i></span>
                                @endif
                            </td>
                            <td style="padding:10px 8px; text-align:right; color:var(--muted)">${{ number_format($item->precio_unitario, 2, ',', '.') }}</td>
                            <td style="padding:10px 8px; text-align:right; font-weight:600; color:var(--navy)">${{ number_format($item->subtotal, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" style="padding:12px 8px; text-align:right; font-family:'Oswald',sans-serif; color:var(--navy)">TOTAL</td>
                            <td style="padding:12px 8px; text-align:right; font-family:'Oswald',sans-serif; font-size:1.1rem; color:var(--navy)">${{ number_format($orden->total, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Recepciones anteriores --}}
        @if($orden->recepciones->isNotEmpty())
        <div class="comp-section">
            <div class="comp-header">
                <div class="comp-icon" style="background:var(--accent)"><i class="bi bi-truck"></i></div>
                <span class="comp-title">HISTORIAL DE RECEPCIONES</span>
            </div>
            <div class="comp-body">
                @foreach($orden->recepciones as $rec)
                <div style="padding:12px 14px; background:var(--card); border-radius:9px; margin-bottom:8px">
                    <div style="display:flex; justify-content:space-between; align-items:center">
                        <div style="font-size:.86rem; font-weight:600; color:var(--navy)">
                            Recepción del {{ $rec->created_at->format('d/m/Y H:i') }} hs
                        </div>
                        <div style="font-size:.78rem; color:var(--muted)">{{ $rec->registradaPor->name }}</div>
                    </div>
                    @if($rec->observaciones)
                    <div style="font-size:.82rem; color:var(--muted); margin-top:4px">{{ $rec->observaciones }}</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Columna derecha --}}
    <div>
        {{-- Estado --}}
        <div class="comp-section" style="margin-bottom:16px">
            <div class="comp-header">
                <div class="comp-icon"><i class="bi bi-info-circle"></i></div>
                <span class="comp-title">ESTADO</span>
            </div>
            <div class="comp-body" style="text-align:center; padding:24px">
                @php
                $badgeColor = match($orden->estado) {
                    'enviada'          => 'var(--accent)',
                    'recibida_parcial' => 'var(--warn)',
                    'recibida'         => 'var(--ok)',
                    'cancelada'        => 'var(--error)',
                    default            => 'var(--muted)',
                };
                @endphp
                <div style="font-family:'Oswald',sans-serif; font-size:1.4rem; color:{{ $badgeColor }}; margin-bottom:8px">
                    {{ $orden->etiquetaEstado() }}
                </div>
                <div style="font-size:.82rem; color:var(--muted)">
                    @if($orden->estado === 'enviada') Esperando recepción de mercadería
                    @elseif($orden->estado === 'recibida_parcial') Recibida parcialmente
                    @elseif($orden->estado === 'recibida') Todos los items recibidos
                    @elseif($orden->estado === 'cancelada') Orden cancelada
                    @endif
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="comp-section">
            <div class="comp-header">
                <div class="comp-icon"><i class="bi bi-gear"></i></div>
                <span class="comp-title">ACCIONES</span>
            </div>
            <div class="comp-body" style="display:flex; flex-direction:column; gap:10px">
                @if(in_array($orden->estado, ['enviada','recibida_parcial']))
                <button onclick="document.getElementById('modalRecibir').style.display='flex'" class="btn-ok-ta" style="width:100%; justify-content:center">
                    <i class="bi bi-truck"></i> Registrar Recepción
                </button>
                @endif

                @if(in_array($orden->estado, ['enviada']))
                <form method="POST" action="{{ route('admin.compras.cancelar', $orden) }}"
                    onsubmit="return confirm('¿Seguro que querés cancelar esta orden?')">
                    @csrf
                    <button type="submit" class="btn-secondary-ta" style="width:100%; justify-content:center; color:var(--error); border-color:var(--error)">
                        <i class="bi bi-x-circle"></i> Cancelar Orden
                    </button>
                </form>
                @endif

                @if($orden->estado === 'recibida')
                <div style="text-align:center; padding:12px; background:rgba(15,138,74,.08); border-radius:8px; border:1px solid rgba(15,138,74,.2)">
                    <i class="bi bi-check-circle" style="color:var(--ok); font-size:1.3rem; display:block; margin-bottom:4px"></i>
                    <div style="font-size:.82rem; font-weight:600; color:var(--ok)">Orden completada</div>
                    <div style="font-size:.76rem; color:var(--muted)">Stock actualizado correctamente</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal registrar recepción --}}
<div id="modalRecibir" class="modal-bg">
    <div style="background:white; border-radius:14px; width:100%; max-width:560px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.3); max-height:90vh; overflow-y:auto">
        <div style="padding:18px 22px; border-bottom:1px solid var(--border); background:var(--light); display:flex; justify-content:space-between; align-items:center; position:sticky; top:0">
            <div style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--navy); letter-spacing:.04em">
                <i class="bi bi-truck" style="color:var(--ok); margin-right:8px"></i>REGISTRAR RECEPCIÓN DE MERCADERÍA
            </div>
            <button type="button" onclick="document.getElementById('modalRecibir').style.display='none'"
                style="background:none; border:none; font-size:1.3rem; cursor:pointer; color:var(--muted)">×</button>
        </div>
        <form method="POST" action="{{ route('admin.compras.recibir', $orden) }}">
            @csrf
            <div style="padding:22px; display:flex; flex-direction:column; gap:14px">
                <div style="font-size:.84rem; color:var(--muted)">
                    <i class="bi bi-info-circle" style="color:var(--blue)"></i>
                    Ingresá la cantidad recibida de cada repuesto. El stock se actualizará automáticamente.
                </div>
                @foreach($orden->items as $item)
                @if($item->pendienteRecibir() > 0)
                <div style="background:var(--card); border-radius:9px; padding:14px 16px">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px">
                        <div style="font-weight:600; color:var(--navy)">{{ $item->repuesto->nombre }}</div>
                        <div style="font-size:.78rem; color:var(--muted)">Pendiente: <strong style="color:var(--warn)">{{ $item->pendienteRecibir() }}</strong></div>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px">
                        <label style="font-size:.78rem; color:var(--muted); white-space:nowrap">Cantidad recibida:</label>
                        <input type="number" name="items[{{ $item->id }}][cantidad]"
                            class="field-input" min="0" max="{{ $item->pendienteRecibir() }}"
                            value="{{ $item->pendienteRecibir() }}" style="max-width:100px">
                    </div>
                </div>
                @endif
                @endforeach
                <div class="field-group">
                    <label>Observaciones</label>
                    <textarea name="observaciones" class="field-input" style="resize:vertical; min-height:60px"
                        placeholder="Notas sobre la recepción..."></textarea>
                </div>
            </div>
            <div style="padding:14px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px; background:var(--card)">
                <button type="button" class="btn-secondary-ta" onclick="document.getElementById('modalRecibir').style.display='none'">Cancelar</button>
                <button type="submit" class="btn-ok-ta"><i class="bi bi-check-circle"></i> Confirmar Recepción</button>
            </div>
        </form>
    </div>
</div>
@endsection