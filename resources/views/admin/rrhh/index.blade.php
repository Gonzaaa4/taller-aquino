@extends('layouts.app')
@section('title', 'RRHH')
@section('topbar-title', 'Recursos Humanos')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">RRHH</div>
            <h1 class="page-title">Recursos Humanos</h1>
            <p class="page-subtitle">Gestión de mecánicos, horas y comisiones</p>
        </div>
    </div>
</div>

{{-- KPIs generales --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px">
    <div class="ta-card" style="padding:20px; border-left:4px solid var(--blue)">
        <div style="font-size:.74rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px"><i class="bi bi-people"></i> Mecánicos activos</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.8rem; color:var(--navy)">{{ $mecanicos->where('activo', true)->count() }}</div>
    </div>
    <div class="ta-card" style="padding:20px; border-left:4px solid var(--ok)">
        <div style="font-size:.74rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px"><i class="bi bi-wrench"></i> Trabajos este mes</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.8rem; color:var(--ok)">{{ $trabajosMes }}</div>
    </div>
    <div class="ta-card" style="padding:20px; border-left:4px solid var(--warn)">
        <div style="font-size:.74rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px"><i class="bi bi-cash-coin"></i> Comisiones pendientes</div>
        <div style="font-family:'Oswald',sans-serif; font-size:1.8rem; color:var(--warn)">${{ number_format($mecanicos->sum('comisiones_pendientes'), 0, ',', '.') }}</div>
    </div>
</div>

{{-- Lista de mecánicos --}}
<div class="ta-card">
    <div class="ta-card-header">
        <div class="ta-card-title"><i class="bi bi-people" style="color:var(--blue)"></i> Equipo de Mecánicos</div>
    </div>
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>Mecánico</th>
                    <th style="text-align:center">Trabajos este mes</th>
                    <th style="text-align:center">Horas este mes</th>
                    <th style="text-align:right">Comisiones pendientes</th>
                    <th style="text-align:center">Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mecanicos as $mecanico)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:12px">
                            <div style="width:38px; height:38px; border-radius:50%; background:var(--blue); display:flex; align-items:center; justify-content:center; font-family:'Oswald',sans-serif; font-size:.85rem; color:white; flex-shrink:0">
                                {{ strtoupper(substr($mecanico->name,0,1).substr($mecanico->apellido,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600; color:var(--navy)">{{ $mecanico->nombreCompleto() }}</div>
                                <div style="font-size:.76rem; color:var(--muted)">{{ $mecanico->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align:center">
                        <span style="font-family:'Oswald',sans-serif; font-size:1.1rem; color:var(--navy)">
                            {{ \App\Models\TrabajoRealizado::where('mecanico_id', $mecanico->id)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count() }}
                        </span>
                    </td>
                    <td style="text-align:center">
                        <span style="font-family:'Oswald',sans-serif; font-size:1.1rem; color:var(--navy)">{{ number_format($mecanico->horas_mes ?? 0, 1) }}h</span>
                    </td>
                    <td style="text-align:right; font-weight:600; color:{{ ($mecanico->comisiones_pendientes ?? 0) > 0 ? 'var(--warn)' : 'var(--muted)' }}">
                        ${{ number_format($mecanico->comisiones_pendientes ?? 0, 0, ',', '.') }}
                    </td>
                    <td style="text-align:center">
                        @if($mecanico->activo)
                        <span class="ta-badge badge-finalizado">Activo</span>
                        @else
                        <span class="ta-badge badge-cancelado">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.rrhh.perfil', $mecanico) }}" class="btn-primary-ta" style="padding:6px 14px; font-size:.82rem">
                            <i class="bi bi-person-badge"></i> Ver Perfil
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:56px; color:var(--muted)">
                        <i class="bi bi-people" style="font-size:2.5rem; display:block; margin-bottom:14px; opacity:.3"></i>
                        No hay mecánicos registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
