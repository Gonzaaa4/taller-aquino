@extends('layouts.app')
@section('title', 'Proveedores')
@section('topbar-title', 'Gestión de Proveedores')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Inventario</div>
            <h1 class="page-title">Proveedores</h1>
            <p class="page-subtitle">Empresas y contactos que abastecen al taller</p>
        </div>
        <a href="{{ route('admin.proveedores.crear') }}" class="btn-primary-ta">
            <i class="bi bi-plus-circle"></i> Agregar Proveedor
        </a>
    </div>
</div>

{{-- Filtros --}}
<div class="ta-card" style="margin-bottom:20px">
    <div class="ta-card-body" style="padding:14px 20px">
        <form style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end">
            <div style="flex:1; min-width:200px">
                <label class="ta-label">Buscar</label>
                <input type="text" name="buscar" class="ta-input" placeholder="Nombre del proveedor..."
                    value="{{ request('buscar') }}">
            </div>
            <div>
                <label class="ta-label">Categoría</label>
                <select name="categoria" class="ta-input ta-select" style="width:160px">
                    <option value="">Todas</option>
                    @foreach(['repuestos','lubricantes','herramientas','otros'] as $c)
                        <option value="{{ $c }}" {{ request('categoria') === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:8px; align-items:flex-end">
                <button type="submit" class="btn-primary-ta" style="height:40px">Filtrar</button>
                <a href="{{ route('admin.proveedores.index') }}" class="btn-secondary-ta" style="height:40px">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="ta-card">
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>Proveedor</th>
                    <th>Contacto</th>
                    <th>Categoría</th>
                    <th>Repuestos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proveedores as $p)
                <tr>
                    <td>
                        <div style="font-weight:600; color:var(--navy)">{{ $p->nombre }}</div>
                        @if($p->direccion)
                        <div style="font-size:.76rem; color:var(--muted)">{{ $p->direccion }}</div>
                        @endif
                    </td>
                    <td>
                        @if($p->telefono)
                        <div style="font-size:.84rem"><i class="bi bi-telephone" style="color:var(--muted)"></i> {{ $p->telefono }}</div>
                        @endif
                        @if($p->email)
                        <div style="font-size:.84rem; color:var(--accent)">{{ $p->email }}</div>
                        @endif
                    </td>
                    <td>
                        <span style="background:rgba(46,141,255,.1); color:var(--blue); padding:3px 10px; border-radius:20px; font-size:.76rem; font-weight:600">
                            {{ ucfirst($p->categoria ?? 'General') }}
                        
                    </td>
                    <td>
                        <span style="font-family:'Oswald',sans-serif; font-size:1rem; color:var(--navy)">
                            {{ $p->repuestos_count }}
                        
                        <span style="font-size:.76rem; color:var(--muted)"> items
                    </td>
                    <td>
                        @if($p->activo)
                            <span class="ta-badge badge-finalizado">Activo
                        @else
                            <span class="ta-badge badge-cancelado">Inactivo
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; gap:8px">
                            <a href="{{ route('admin.proveedores.editar', $p) }}" class="btn-secondary-ta" style="padding:6px 12px; font-size:.8rem">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <form method="POST" action="{{ route('admin.proveedores.eliminar', $p) }}"
                                onsubmit="return confirm('¿Eliminar este proveedor?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger-ta" style="padding:6px 12px; font-size:.8rem">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:48px; color:var(--muted)">
                        <i class="bi bi-truck" style="font-size:2rem; display:block; margin-bottom:12px; opacity:.3"></i>
                        No hay proveedores registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($proveedores->hasPages())
    <div style="padding:16px 20px; border-top:1px solid var(--border)">{{ $proveedores->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
