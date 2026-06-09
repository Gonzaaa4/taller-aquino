@extends('layouts.app')
@section('title', 'Nueva Orden de Compra')
@section('topbar-title', 'Nueva Orden de Compra')

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
.req-star { color:var(--error); }
.field-input { border:1.5px solid var(--border); border-radius:7px; padding:10px 13px; font-family:'Source Sans 3',sans-serif; font-size:.92rem; color:var(--text); outline:none; width:100%; background:#fff; transition:border-color .2s; }
.field-input:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(46,141,255,.12); }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Finanzas · Compras</div>
            <h1 class="page-title">Nueva Orden de Compra</h1>
        </div>
        <a href="{{ route('admin.compras.index') }}" class="btn-secondary-ta">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.compras.guardar') }}" id="ordenForm">
    @csrf

    {{-- Proveedor y fecha --}}
    <div class="comp-section">
        <div class="comp-header">
            <div class="comp-icon"><i class="bi bi-building"></i></div>
            <span class="comp-title">DATOS DE LA ORDEN</span>
        </div>
        <div class="comp-body" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px">
            <div class="field-group" style="grid-column:span 2">
                <label>Proveedor <span class="req-star">*</span></label>
                <select name="proveedor_id" class="field-input" required>
                    <option value="">— Seleccioná un proveedor —</option>
                    @foreach($proveedores as $p)
                    <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field-group">
                <label>Fecha esperada de entrega</label>
                <input type="date" name="fecha_esperada" class="field-input" min="{{ now()->addDay()->toDateString() }}">
            </div>
            <div class="field-group" style="grid-column:span 3">
                <label>Observaciones</label>
                <textarea name="observaciones" class="field-input" style="resize:vertical; min-height:60px"
                    placeholder="Notas para el proveedor..."></textarea>
            </div>
        </div>
    </div>

    {{-- Items --}}
    <div class="comp-section">
        <div class="comp-header">
            <div class="comp-icon" style="background:var(--ok)"><i class="bi bi-box-seam"></i></div>
            <span class="comp-title">REPUESTOS A PEDIR</span>
        </div>
        <div class="comp-body">
            <table style="width:100%; border-collapse:collapse; font-size:.88rem; margin-bottom:14px" id="itemsTable">
                <thead>
                    <tr style="border-bottom:1px solid var(--border)">
                        <th style="text-align:left; padding:8px; color:var(--muted); font-size:.74rem; text-transform:uppercase">Repuesto</th>
                        <th style="text-align:center; padding:8px; color:var(--muted); font-size:.74rem; text-transform:uppercase; width:110px">Cantidad</th>
                        <th style="text-align:right; padding:8px; color:var(--muted); font-size:.74rem; text-transform:uppercase; width:160px">Precio unitario ($)</th>
                        <th style="text-align:right; padding:8px; color:var(--muted); font-size:.74rem; text-transform:uppercase; width:140px">Subtotal</th>
                        <th style="width:44px"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <tr class="item-row" data-idx="0">
                        <td style="padding:8px">
                            <select name="items[0][repuesto_id]" class="field-input" required>
                                <option value="">— Seleccioná —</option>
                                @foreach($repuestos as $r)
                                <option value="{{ $r->id }}" data-precio="{{ $r->costo }}">
                                    {{ $r->nombre }} (Stock: {{ $r->cantidad_stock }})
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td style="padding:8px">
                            <input type="number" name="items[0][cantidad]" class="field-input item-cant" min="1" value="1" required oninput="calcularFila(this)">
                        </td>
                        <td style="padding:8px">
                            <div style="position:relative">
                                <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--muted); font-size:.85rem">$</span>
                                <input type="number" name="items[0][precio]" class="field-input item-precio" min="0" step="0.01" style="padding-left:24px" required oninput="calcularFila(this)">
                            </div>
                        </td>
                        <td style="padding:8px; text-align:right; font-weight:600; color:var(--navy)" class="item-subtotal">$0</td>
                        <td style="padding:8px; text-align:center">
                            <button type="button" onclick="eliminarFila(this)"
                                style="background:none; border:1.5px solid var(--border); border-radius:7px; padding:5px 9px; cursor:pointer; color:var(--error)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" onclick="agregarFila()" class="btn-secondary-ta" style="font-size:.82rem; padding:6px 14px">
                <i class="bi bi-plus"></i> Agregar repuesto
            </button>

            {{-- Total --}}
            <div style="margin-top:16px; display:flex; justify-content:flex-end">
                <div style="background:var(--navy); border-radius:10px; padding:14px 22px; text-align:right">
                    <div style="font-size:.74rem; color:rgba(255,255,255,.5); text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px">Total orden</div>
                    <div style="font-family:'Oswald',sans-serif; font-size:1.8rem; color:#4ade80" id="totalOrden">$0</div>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:10px">
        <a href="{{ route('admin.compras.index') }}" class="btn-secondary-ta">Cancelar</a>
        <button type="submit" class="btn-ok-ta"><i class="bi bi-check-circle"></i> Crear Orden de Compra</button>
    </div>
</form>

@push('scripts')
<script>
let idx = 1;
const repuestosData = {
    @foreach($repuestos as $r)
    {{ $r->id }}: { precio: {{ $r->costo ?? 0 }} },
    @endforeach
};

function calcularFila(input) {
    const row  = input.closest('.item-row');
    const cant = parseFloat(row.querySelector('.item-cant').value) || 0;
    const prec = parseFloat(row.querySelector('.item-precio').value) || 0;
    const sub  = cant * prec;
    row.querySelector('.item-subtotal').textContent = '$' + sub.toLocaleString('es-AR', {minimumFractionDigits:2, maximumFractionDigits:2});
    calcularTotal();
}

function calcularTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const cant = parseFloat(row.querySelector('.item-cant').value) || 0;
        const prec = parseFloat(row.querySelector('.item-precio').value) || 0;
        total += cant * prec;
    });
    document.getElementById('totalOrden').textContent = '$' + total.toLocaleString('es-AR', {minimumFractionDigits:2, maximumFractionDigits:2});
}

function agregarFila() {
    const tbody = document.getElementById('itemsBody');
    const opts  = `<option value="">— Seleccioná —</option>` +
        Object.entries(repuestosData).map(([id]) =>
            `<option value="${id}">${document.querySelector(\`select[name="items[0][repuesto_id]"] option[value="${id}"]\`)?.textContent || ''}</option>`
        ).join('');

    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.dataset.idx = idx;
    tr.innerHTML = `
        <td style="padding:8px">
            <select name="items[${idx}][repuesto_id]" class="field-input" required onchange="cargarPrecio(this)">
                ${opts}
            </select>
        </td>
        <td style="padding:8px">
            <input type="number" name="items[${idx}][cantidad]" class="field-input item-cant" min="1" value="1" required oninput="calcularFila(this)">
        </td>
        <td style="padding:8px">
            <div style="position:relative">
                <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--muted); font-size:.85rem">$</span>
                <input type="number" name="items[${idx}][precio]" class="field-input item-precio" min="0" step="0.01" style="padding-left:24px" required oninput="calcularFila(this)">
            </div>
        </td>
        <td style="padding:8px; text-align:right; font-weight:600; color:var(--navy)" class="item-subtotal">$0</td>
        <td style="padding:8px; text-align:center">
            <button type="button" onclick="eliminarFila(this)"
                style="background:none; border:1.5px solid var(--border); border-radius:7px; padding:5px 9px; cursor:pointer; color:var(--error)">
                <i class="bi bi-trash"></i>
            </button>
        </td>`;
    tbody.appendChild(tr);
    idx++;
}

function eliminarFila(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length === 1) { alert('Debe haber al menos un repuesto.'); return; }
    btn.closest('.item-row').remove();
    calcularTotal();
}

function cargarPrecio(select) {
    const id   = select.value;
    const row  = select.closest('.item-row');
    const prec = repuestosData[id]?.precio || 0;
    row.querySelector('.item-precio').value = prec;
    calcularFila(row.querySelector('.item-cant'));
}

// Cargar precio al cambiar repuesto en la primer fila
document.querySelector('select[name="items[0][repuesto_id]"]').addEventListener('change', function() {
    cargarPrecio(this);
});
</script>
@endpush
@endsection