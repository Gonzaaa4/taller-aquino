@extends('layouts.app')
@section('title', 'Usuarios')
@section('topbar-title', 'Gestión de Usuarios')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Administración</div>
            <h1 class="page-title">Usuarios del Sistema</h1>
            <p class="page-subtitle">Empleados y clientes registrados</p>
        </div>
        <a href="{{ route('admin.usuarios.crear') }}" class="btn-primary-ta">
            <i class="bi bi-person-plus"></i> Nuevo Usuario
        </a>
    </div>
</div>

{{-- Filtros --}}
<div class="ta-card" style="margin-bottom:20px">
    <div class="ta-card-body" style="padding:14px 20px">
        <form style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end">
            <div style="flex:1; min-width:200px">
                <label class="ta-label">Buscar</label>
                <input type="text" name="buscar" class="ta-input" placeholder="Nombre, apellido o email..."
                    value="{{ request('buscar') }}">
            </div>
            <div>
                <label class="ta-label">Rol</label>
                <select name="rol" class="ta-input ta-select" style="width:160px">
                    <option value="">Todos los roles</option>
                    @foreach(['admin','administrativo','mecanico','cliente'] as $r)
                        <option value="{{ $r }}" {{ request('rol') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:8px; align-items:flex-end">
                <button type="submit" class="btn-primary-ta" style="height:40px">Filtrar</button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn-secondary-ta" style="height:40px">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="ta-card">
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Contacto</th>
                    <th>DNI</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $u)
                @php
                    $rolColor = match($u->rol) {
                        'admin'          => ['bg:rgba(217,48,37,.1)', 'color:var(--error)'],
                        'mecanico'       => ['bg:rgba(46,141,255,.1)', 'color:var(--accent)'],
                        'administrativo' => ['bg:rgba(230,126,0,.1)', 'color:var(--warn)'],
                        'cliente'        => ['bg:rgba(15,138,74,.1)', 'color:var(--ok)'],
                        default          => ['bg:rgba(90,122,149,.1)', 'color:var(--muted)'],
                    };
                @endphp
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:11px">
                            <div style="width:36px; height:36px; border-radius:50%; background:var(--light); color:var(--blue);
                                display:flex; align-items:center; justify-content:center; font-family:'Oswald',sans-serif;
                                font-size:.82rem; flex-shrink:0; border:1.5px solid var(--border)">
                                {{ strtoupper(substr($u->name,0,1).substr($u->apellido,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600; color:var(--navy)">{{ $u->nombreCompleto() }}</div>
                                <div style="font-size:.76rem; color:var(--muted)">{{ $u->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.84rem; color:var(--muted)">{{ $u->telefono ?? '—' }}</td>
                    <td style="font-size:.84rem; color:var(--muted); font-family:'Courier New',monospace">{{ $u->dni ?? '—' }}</td>
                    <td>
                        <span style="padding:3px 11px; border-radius:20px; font-size:.74rem; font-weight:700;
                            {{ $rolColor[0] }}; {{ $rolColor[1] }}; background:{{ str_replace('bg:','',$rolColor[0]) }}">
                            {{ ucfirst($u->rol) }}
                        
                    </td>
                    <td>
                        @if($u->activo)
                            <span class="ta-badge badge-finalizado">Activo
                        @else
                            <span class="ta-badge badge-cancelado">Suspendido
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; gap:8px">
                            <a href="{{ route('admin.usuarios.editar', $u) }}" class="btn-secondary-ta" style="padding:6px 12px; font-size:.8rem">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.usuarios.toggle', $u) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="{{ $u->activo ? 'btn-danger-ta' : 'btn-ok-ta' }}" style="padding:6px 12px; font-size:.8rem">
                                    <i class="bi bi-{{ $u->activo ? 'pause-circle' : 'play-circle' }}"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:48px; color:var(--muted)">
                        <i class="bi bi-people" style="font-size:2rem; display:block; margin-bottom:12px; opacity:.3"></i>
                        No hay usuarios para mostrar
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($usuarios->hasPages())
    <div style="padding:16px 20px; border-top:1px solid var(--border)">{{ $usuarios->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
