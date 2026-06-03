@extends('layouts.app')
@section('title', 'Herramientas')
@section('topbar-title', 'Inventario — Herramientas')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Inventario</div>
            <h1 class="page-title">Herramientas</h1>
            <p class="page-subtitle">Equipos y herramientas del taller</p>
        </div>
        <button class="btn-primary-ta" onclick="document.getElementById('modalAgregar').style.display='flex'">
            <i class="bi bi-plus-circle"></i> Agregar Herramienta
        </button>
    </div>
</div>

{{-- Filtros --}}
<div class="ta-card" style="margin-bottom:20px">
    <div class="ta-card-body" style="padding:14px 20px">
        <form style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end">
            <div>
                <label class="ta-label">Tipo</label>
                <select name="tipo" class="ta-input ta-select" style="width:160px">
                    <option value="">Todos</option>
                    @foreach(['manual','electrica','especializada','medicion','otros'] as $t)
                        <option value="{{ $t }}" {{ request('tipo') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="ta-label">Estado</label>
                <select name="estado" class="ta-input ta-select" style="width:160px">
                    <option value="">Todos</option>
                    @foreach(['disponible','en_uso','en_reparacion','baja'] as $e)
                        <option value="{{ $e }}" {{ request('estado') === $e ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$e)) }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:8px; align-items:flex-end; padding-bottom:0">
                <button type="submit" class="btn-primary-ta" style="height:40px">Filtrar</button>
                <a href="{{ route('admin.inventario.herramientas') }}" class="btn-secondary-ta" style="height:40px">Limpiar</a>
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
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Ubicación</th>
                    <th>Adquisición</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($herramientas as $h)
                @php
                    $colorEstado = match($h->estado) {
                        'disponible'    => 'var(--ok)',
                        'en_uso'        => 'var(--accent)',
                        'en_reparacion' => 'var(--warn)',
                        'baja'          => 'var(--muted)',
                        default         => 'var(--muted)',
                    };
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:600; color:var(--navy)">{{ $h->nombre }}</div>
                        @if($h->descripcion)
                        <div style="font-size:.78rem; color:var(--muted)">{{ Str::limit($h->descripcion, 50) }}</div>
                        @endif
                    </td>
                    <td><span style="font-size:.82rem; color:var(--muted)">{{ ucfirst($h->tipo) }}</td>
                    <td>
                        <span class="ta-badge" style="background:{{ $colorEstado }}1a; color:{{ $colorEstado }}; gap:6px">
                            <span style="width:6px;height:6px;border-radius:50%;background:{{ $colorEstado }};display:inline-block;flex-shrink:0">
                            {{ ucfirst(str_replace('_',' ',$h->estado)) }}
                        
                    </td>
                    <td style="font-size:.86rem; color:var(--muted)">{{ $h->ubicacion ?? '—' }}</td>
                    <td style="font-size:.84rem; color:var(--muted)">
                        {{ $h->fecha_adquisicion ? $h->fecha_adquisicion->format('d/m/Y') : '—' }}
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.inventario.herramienta.guardar') }}" style="display:inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="id" value="{{ $h->id }}">
                            <select name="estado" class="ta-input ta-select" style="width:140px; font-size:.8rem; padding:5px 28px 5px 9px; height:auto"
                                onchange="this.form.submit()">
                                @foreach(['disponible','en_uso','en_reparacion','baja'] as $e)
                                    <option value="{{ $e }}" {{ $h->estado === $e ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_',' ',$e)) }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:48px; color:var(--muted)">
                        <i class="bi bi-tools" style="font-size:2rem; display:block; margin-bottom:12px; opacity:.3"></i>
                        No hay herramientas registradas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($herramientas->hasPages())
    <div style="padding:16px 20px; border-top:1px solid var(--border)">{{ $herramientas->withQueryString()->links() }}</div>
    @endif
</div>

{{-- Modal agregar --}}
<div id="modalAgregar" style="display:none; position:fixed; inset:0; background:rgba(11,28,46,.6); z-index:500; align-items:center; justify-content:center; padding:20px">
    <div style="background:white; border-radius:14px; width:100%; max-width:520px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.3)">
        <div style="padding:18px 22px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center">
            <div style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--navy); letter-spacing:.04em">
                <i class="bi bi-plus-circle me-2" style="color:var(--blue)"></i>AGREGAR HERRAMIENTA
            </div>
            <button onclick="document.getElementById('modalAgregar').style.display='none'"
                style="background:none; border:none; font-size:1.2rem; cursor:pointer; color:var(--muted)">×</button>
        </div>
        <form method="POST" action="{{ route('admin.inventario.herramienta.guardar') }}">
            @csrf
            <div style="padding:22px; display:grid; grid-template-columns:1fr 1fr; gap:14px">
                <div style="grid-column:span 2">
                    <label class="ta-label">Nombre <span class="req">*</label>
                    <input type="text" name="nombre" class="ta-input" required placeholder="Ej: Llave torquímetro">
                </div>
                <div>
                    <label class="ta-label">Tipo <span class="req">*</label>
                    <select name="tipo" class="ta-input ta-select" required>
                        <option value="manual">Manual</option>
                        <option value="electrica">Eléctrica</option>
                        <option value="especializada">Especializada</option>
                        <option value="medicion">Medición</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>
                <div>
                    <label class="ta-label">Estado <span class="req">*</label>
                    <select name="estado" class="ta-input ta-select" required>
                        <option value="disponible">Disponible</option>
                        <option value="en_uso">En uso</option>
                        <option value="en_reparacion">En reparación</option>
                        <option value="baja">Baja</option>
                    </select>
                </div>
                <div>
                    <label class="ta-label">Ubicación</label>
                    <input type="text" name="ubicacion" class="ta-input" placeholder="Ej: Sector A">
                </div>
                <div>
                    <label class="ta-label">Fecha de adquisición</label>
                    <input type="date" name="fecha_adquisicion" class="ta-input">
                </div>
                <div style="grid-column:span 2">
                    <label class="ta-label">Descripción</label>
                    <textarea name="descripcion" class="ta-input ta-textarea" placeholder="Detalles adicionales..."></textarea>
                </div>
            </div>
            <div style="padding:16px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px">
                <button type="button" class="btn-secondary-ta" onclick="document.getElementById('modalAgregar').style.display='none'">Cancelar</button>
                <button type="submit" class="btn-primary-ta"><i class="bi bi-check-circle"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
