@extends('layouts.app')
@section('title', 'Generar Reporte')
@section('topbar-title', 'Generar <span>Reporte</span>')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Reportes</div>
            <h1 class="page-title">Generar Reporte</h1>
            <p class="page-subtitle">Seleccioná el tipo de reporte y configurá los parámetros</p>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:20px; margin-bottom:32px">

    {{-- Trabajos realizados --}}
    <div class="ta-card">
        <div class="ta-card-header">
            <div class="ta-card-title">
                <i class="bi bi-wrench-adjustable" style="color:var(--warn)"></i> Trabajos Realizados
            </div>
        </div>
        <div style="padding:8px 20px 4px; font-size:.84rem; color:var(--muted)">
            Historial de reparaciones, costos y mecánicos por período.
        </div>
        <form method="GET" action="{{ route('admin.reportes.trabajos') }}" style="padding:16px 20px 20px">
            <div style="display:grid; gap:12px">
                <div>
                    <label class="ta-label">Desde <span class="req">*</span></label>
                    <input type="date" name="fecha_inicio" class="ta-input"
                        value="{{ now()->startOfMonth()->format('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="ta-label">Hasta <span class="req">*</span></label>
                    <input type="date" name="fecha_fin" class="ta-input"
                        value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="ta-label">Categoría</label>
                    <select name="categoria" class="ta-input ta-select">
                        <option value="todos">Todas</option>
                        <option value="mantenimiento_preventivo">Mantenimiento Preventivo</option>
                        <option value="reparacion">Reparación</option>
                        <option value="service">Service</option>
                        <option value="diagnostico">Diagnóstico</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>
                <div>
                    <label class="ta-label">Ordenar por</label>
                    <select name="orden" class="ta-input ta-select">
                        <option value="fecha">Fecha</option>
                        <option value="mayor_importe">Mayor importe</option>
                        <option value="menor_importe">Menor importe</option>
                        <option value="empleado">Empleado</option>
                    </select>
                </div>
                <div style="display:flex; gap:8px; margin-top:4px">
                    <button type="submit" class="btn-primary-ta" style="flex:1">
                        <i class="bi bi-eye"></i> Ver reporte
                    </button>
                    <button type="submit" name="formato" value="pdf" class="btn-danger-ta" title="Descargar PDF">
                        <i class="bi bi-file-pdf"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Stock de repuestos --}}
    <div class="ta-card">
        <div class="ta-card-header">
            <div class="ta-card-title">
                <i class="bi bi-box-seam" style="color:var(--ok)"></i> Stock de Repuestos
            </div>
        </div>
        <div style="padding:8px 20px 4px; font-size:.84rem; color:var(--muted)">
            Estado actual del inventario, alertas y valorización total.
        </div>
        <form method="GET" action="{{ route('admin.reportes.stock') }}" style="padding:16px 20px 20px">
            <div style="display:grid; gap:12px">
                <div>
                    <label class="ta-label">Categoría</label>
                    <select name="categoria" class="ta-input ta-select">
                        <option value="todos">Todas las categorías</option>
                        @foreach(['motor','transmision','frenos','suspension','electrico','lubricantes','filtros','otros'] as $c)
                            <option value="{{ $c }}">{{ ucfirst($c) }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex; gap:8px; margin-top:4px">
                    <button type="submit" class="btn-primary-ta" style="flex:1">
                        <i class="bi bi-eye"></i> Ver reporte
                    </button>
                    <button type="submit" name="formato" value="pdf" class="btn-danger-ta" title="Descargar PDF">
                        <i class="bi bi-file-pdf"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Turnos --}}
    <div class="ta-card">
        <div class="ta-card-header">
            <div class="ta-card-title">
                <i class="bi bi-calendar-check" style="color:var(--accent)"></i> Turnos Programados
            </div>
        </div>
        <div style="padding:8px 20px 4px; font-size:.84rem; color:var(--muted)">
            Agenda de citas, estados y mecánicos asignados por período.
        </div>
        <form method="GET" action="{{ route('admin.reportes.turnos') }}" style="padding:16px 20px 20px">
            <div style="display:grid; gap:12px">
                <div>
                    <label class="ta-label">Desde <span class="req">*</span></label>
                    <input type="date" name="fecha_inicio" class="ta-input"
                        value="{{ now()->startOfMonth()->format('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="ta-label">Hasta <span class="req">*</span></label>
                    <input type="date" name="fecha_fin" class="ta-input"
                        value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="ta-label">Estado</label>
                    <select name="estado" class="ta-input ta-select">
                        <option value="todos">Todos</option>
                        @foreach(['pendiente','confirmado','en_proceso','finalizado','cancelado'] as $e)
                            <option value="{{ $e }}">{{ ucfirst(str_replace('_',' ',$e)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex; gap:8px; margin-top:4px">
                    <button type="submit" class="btn-primary-ta" style="flex:1">
                        <i class="bi bi-eye"></i> Ver reporte
                    </button>
                    <button type="submit" name="formato" value="pdf" class="btn-danger-ta" title="Descargar PDF">
                        <i class="bi bi-file-pdf"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Historial de reportes --}}
<div class="section-label">
    <h2>Historial de Reportes</h2>
    <div class="section-label-line"></div>
</div>

<div class="ta-card">
    @php $reportes = \App\Models\Reporte::with('generadoPor')->orderBy('fecha_generacion','desc')->take(15)->get(); @endphp
    <div style="overflow-x:auto">
        <table class="ta-table">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Período</th>
                    <th>Filtros</th>
                    <th>Generado por</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportes as $rep)
                <tr>
                    <td>
                        <span style="font-weight:600; color:var(--navy)">{{ $rep->etiquetaTipo() }}</span>
                    </td>
                    <td style="font-size:.84rem; color:var(--muted)">
                        {{ $rep->fecha_inicio->format('d/m/Y') }} — {{ $rep->fecha_fin->format('d/m/Y') }}
                    </td>
                    <td style="font-size:.82rem; color:var(--muted)">
                        {{ $rep->categoria_filtro ?? '—' }}
                        @if($rep->orden) · {{ str_replace('_',' ',$rep->orden) }} @endif
                    </td>
                    <td style="font-size:.84rem">{{ $rep->generadoPor->nombreCompleto() }}</td>
                    <td style="font-size:.82rem; color:var(--muted)">
                        {{ $rep->fecha_generacion->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:40px; color:var(--muted)">
                        <i class="bi bi-file-earmark-bar-graph" style="font-size:2rem; display:block; margin-bottom:12px; opacity:.3"></i>
                        No hay reportes generados aún
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
