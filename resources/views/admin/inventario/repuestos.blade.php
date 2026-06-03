@extends('layouts.app')
@section('title', 'Inventario – Repuestos')
@section('topbar-title', 'Inventario de <span>Repuestos</span>')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Inventario</div>
            <h1 class="page-title">Repuestos</h1>
            <p class="page-subtitle">Control de stock de piezas del taller</p>
        </div>
        <a href="{{ route('admin.inventario.repuesto.crear') }}" class="btn-primary-ta">
            <i class="bi bi-plus-circle"></i> Agregar Repuesto
        </a>
    </div>
</div>

@if($alertas > 0)
<div class="ta-alert warning" style="margin-bottom:20px">
    <span class="ta-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
    <div>
        <strong>{{ $alertas }} repuesto{{ $alertas > 1 ? 's' : '' }} con stock bajo o agotado.</strong>
        Revisá el inventario para evitar demoras en los trabajos.
    </div>
</div>
@endif

{{-- Filtros --}}
<div class="ta-card" style="margin-bottom:20px">
    <div class="ta-card-body" style="padding:14px 20px">
        <form style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end">
            <div style="flex:1; min-width:200px">
                <label class="ta-label">Buscar</label>
                <input type="text" name="buscar" class="ta-input" placeholder="Nombre del repuesto..."
                    value="{{ request('buscar') }}">
            </div>
            <div>
                <label class="ta-label">Categoría</label>
                <select name="categoria" class="ta-input ta-select" style="width:170px">
                    <option value="">Todas las categorías</option>
                    @foreach(['motor','transmision','frenos','suspension','electrico','lubricantes','filtros','otros'] as $cat)
                        <option value="{{ $cat }}" {{ request('categoria') === $cat ? 'selected' : '' }}>
                            {{ ucfirst($cat) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; align-items:flex-end; gap:8px; padding-bottom:1px">
                <label style="display:flex; align-items:center; gap:7px; font-size:.86rem; color:var(--text); cursor:pointer; white-space:nowrap">
                    <input type="checkbox" name="stock_bajo" value="1" {{ request('stock_bajo') ? 'checked' : '' }}
                        style="width:15px; height:15px; accent-color:var(--warn)">
                    Solo stock bajo
                </label>
            </div>
            <div style="display:flex; gap:8px; align-items:flex-end">
                <button type="submit" class="btn-primary-ta" style="height:40px">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <a href="{{ route('admin.inventario.repuestos') }}" class="btn-secondary-ta" style="height:40px">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="ta-card">
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th style="text-align:center">Stock</th>
                    <th style="text-align:center">Mín.</th>
                    <th>Estado</th>
                    <th>Proveedor</th>
                    <th>Costo Unit.</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($repuestos as $rep)
                @php $estado = $rep->estadoStock(); @endphp
                <tr style="{{ $estado === 'sin_stock' ? 'background:rgba(217,48,37,.03)' : ($estado === 'critico' ? 'background:rgba(230,126,0,.03)' : '') }}">
                    <td>
                        <div style="font-weight:600; color:var(--navy)">{{ $rep->nombre }}</div>
                        @if($rep->codigo)
                        <div style="font-size:.72rem; color:var(--muted); font-family:'Courier New',monospace">{{ $rep->codigo }}</div>
                        @endif
                    </td>
                    <td>
                        <span style="background:rgba(46,141,255,.1); color:var(--blue); padding:3px 10px; border-radius:20px; font-size:.74rem; font-weight:600">
                            {{ ucfirst($rep->categoria) }}
                        </span>
                    </td>
                    <td style="text-align:center">
                        <span style="font-family:'Oswald',sans-serif; font-size:1.1rem; font-weight:600;
                            color: {{ $estado === 'sin_stock' ? 'var(--error)' : ($estado === 'critico' ? 'var(--warn)' : 'var(--ok)') }}">
                            {{ $rep->cantidad_stock }}
                        </span>
                    </td>
                    <td style="text-align:center; color:var(--muted); font-size:.88rem">{{ $rep->stock_minimo }}</td>
                    <td>
                        @if($estado === 'sin_stock')
                            <span class="stock-sin">Sin stock</span>
                        @elseif($estado === 'critico')
                            <span class="stock-critico">Crítico</span>
                        @else
                            <span class="stock-ok">OK</span>
                        @endif
                    </td>
                    <td style="font-size:.84rem; color:var(--muted)">{{ $rep->proveedor?->nombre ?? '—' }}</td>
                    <td style="font-size:.88rem; font-weight:600; color:var(--navy)">
                        ${{ number_format($rep->costo, 2, ',', '.') }}
                    </td>
                    <td>
                        <div style="display:flex; gap:6px">
                            <a href="{{ route('admin.inventario.repuesto.editar', $rep) }}"
                               class="btn-secondary-ta" style="padding:6px 11px; font-size:.8rem" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button class="btn-secondary-ta" style="padding:6px 11px; font-size:.8rem; cursor:pointer" title="Ajustar stock"
                                onclick="abrirStock({{ $rep->id }}, '{{ addslashes($rep->nombre) }}', {{ $rep->cantidad_stock }})">
                                <i class="bi bi-plus-slash-minus"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:56px; color:var(--muted)">
                        <i class="bi bi-box-seam" style="font-size:2.5rem; display:block; margin-bottom:14px; opacity:.3"></i>
                        No hay repuestos registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($repuestos->hasPages())
    <div style="padding:16px 20px; border-top:1px solid var(--border)">
        {{ $repuestos->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- Modal ajuste de stock --}}
<div id="modalStock" style="display:none; position:fixed; inset:0; background:rgba(11,28,46,.6); z-index:500; align-items:center; justify-content:center; padding:20px">
    <div style="background:white; border-radius:14px; width:100%; max-width:400px; box-shadow:0 20px 60px rgba(0,0,0,.3)">
        <div style="padding:18px 22px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center">
            <div style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--navy); letter-spacing:.04em">
                AJUSTAR STOCK
            </div>
            <button onclick="document.getElementById('modalStock').style.display='none'"
                style="background:none; border:none; font-size:1.3rem; cursor:pointer; color:var(--muted); line-height:1">×</button>
        </div>
        <form id="stockForm" method="POST">
            @csrf
            <div style="padding:22px">
                <div style="font-size:.88rem; color:var(--muted); margin-bottom:16px" id="stockNombre"></div>
                <div style="margin-bottom:16px">
                    <label class="ta-label">Tipo de movimiento</label>
                    <div style="display:flex; gap:12px; margin-top:6px">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; flex:1; padding:10px 14px; border:1.5px solid var(--border); border-radius:9px; transition:all .15s" id="labelIngreso">
                            <input type="radio" name="tipo" value="ingreso" checked onchange="updateTipoStyle()"
                                style="accent-color:var(--ok)">
                            <span style="font-size:.88rem; color:var(--ok); font-weight:600">Ingreso (+)</span>
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; flex:1; padding:10px 14px; border:1.5px solid var(--border); border-radius:9px; transition:all .15s" id="labelEgreso">
                            <input type="radio" name="tipo" value="egreso" onchange="updateTipoStyle()"
                                style="accent-color:var(--error)">
                            <span style="font-size:.88rem; color:var(--error); font-weight:600">Egreso (−)</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="ta-label">Cantidad <span class="req">*</span></label>
                    <input type="number" name="cantidad" class="ta-input" min="1" required placeholder="Ej: 5">
                </div>
                <div style="margin-top:10px; font-size:.8rem; color:var(--muted)">
                    Stock actual: <strong id="stockActual" style="color:var(--navy)"></strong>
                </div>
            </div>
            <div style="padding:16px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px">
                <button type="button" class="btn-secondary-ta" onclick="document.getElementById('modalStock').style.display='none'">Cancelar</button>
                <button type="submit" class="btn-primary-ta"><i class="bi bi-check-circle"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function abrirStock(id, nombre, stock) {
    document.getElementById('stockNombre').textContent = nombre;
    document.getElementById('stockActual').textContent = stock + ' unidades';
    document.getElementById('stockForm').action = `/admin/inventario/repuestos/${id}/stock`;
    document.getElementById('modalStock').style.display = 'flex';
}
function updateTipoStyle() {
    const ingreso = document.querySelector('input[value="ingreso"]').checked;
    document.getElementById('labelIngreso').style.borderColor = ingreso ? 'var(--ok)' : 'var(--border)';
    document.getElementById('labelEgreso').style.borderColor = !ingreso ? 'var(--error)' : 'var(--border)';
}
</script>
@endpush
@endsection
