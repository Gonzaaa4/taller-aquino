<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reporte;
use App\Models\TrabajoRealizado;
use App\Models\Repuesto;
use App\Models\Turno;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ReporteController extends Controller
{
    public function index()
    {
        $reportes = Reporte::with('generadoPor')->orderBy('fecha_generacion', 'desc')->paginate(20);
        return view('admin.reportes.index', compact('reportes'));
    }

    // ── Formulario de parámetros ─────────────────────────────────
    public function formulario()
    {
        return view('admin.reportes.formulario');
    }

    // ── Generar reporte de trabajos realizados ───────────────────
    public function trabajosRealizados(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
            'categoria'    => 'nullable|in:mantenimiento_preventivo,reparacion,diagnostico,service,otros,todos',
            'orden'        => 'nullable|in:fecha,mayor_importe,menor_importe,empleado',
        ]);

        $query = TrabajoRealizado::with(['mecanico', 'repuestos', 'ingreso.vehiculo.marca', 'ingreso.vehiculo.modelo', 'ingreso.cliente'])
            ->whereBetween('fecha_trabajo', [
                Carbon::parse($request->fecha_inicio)->startOfDay(),
                Carbon::parse($request->fecha_fin)->endOfDay(),
            ])
            ->where('estado', 'finalizado');

        if ($request->categoria && $request->categoria !== 'todos') {
            $query->where('tipo_servicio', $request->categoria);
        }

        $query = match($request->orden) {
            'mayor_importe'  => $query->orderBy('costo_total', 'desc'),
            'menor_importe'  => $query->orderBy('costo_total', 'asc'),
            'empleado'       => $query->orderBy('mecanico_id'),
            default          => $query->orderBy('fecha_trabajo', 'desc'),
        };

        $trabajos = $query->get();

        // Métricas derivadas
        $totalIngresos     = $trabajos->sum('costo_total');
        $cantidadTrabajos  = $trabajos->count();
        $categorias        = $trabajos->groupBy('tipo_servicio')->map->count();
        $tecnicoDestacado  = $trabajos->groupBy('mecanico_id')
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first();

        $tecnicoDestacado = $tecnicoDestacado
            ? $trabajos->firstWhere('mecanico_id', $tecnicoDestacado)?->mecanico
            : null;

        // Guardar registro del reporte
        $reporte = Reporte::create([
            'tipo'             => 'trabajos_realizados',
            'fecha_inicio'     => $request->fecha_inicio,
            'fecha_fin'        => $request->fecha_fin,
            'generado_por'     => Auth::id(),
            'categoria_filtro' => $request->categoria ?? 'todos',
            'orden'            => $request->orden ?? 'fecha',
        ]);

        $datos = compact(
            'trabajos', 'totalIngresos', 'cantidadTrabajos',
            'categorias', 'tecnicoDestacado', 'reporte', 'request'
        );

        if ($request->formato === 'pdf') {
            $pdf = Pdf::loadView('admin.reportes.pdf.trabajos', $datos);
            return $pdf->download("reporte-trabajos-{$request->fecha_inicio}-{$request->fecha_fin}.pdf");
        }

        return view('admin.reportes.trabajos-resultado', $datos);
    }

    // ── Generar reporte de stock ─────────────────────────────────
    public function stockRepuestos(Request $request)
    {
        $request->validate([
            'categoria' => 'nullable|string',
        ]);

        $repuestos = Repuesto::with('proveedor')
            ->activo()
            ->when($request->categoria && $request->categoria !== 'todos',
                fn($q) => $q->where('categoria', $request->categoria)
            )
            ->orderBy('nombre')
            ->get();

        $alertas      = $repuestos->filter(fn($r) => $r->estadoStock() !== 'disponible')->count();
        $sinStock     = $repuestos->filter(fn($r) => $r->estadoStock() === 'sin_stock')->count();
        $valorTotal   = $repuestos->sum(fn($r) => $r->cantidad_stock * $r->costo);

        Reporte::create([
            'tipo'             => 'stock_repuestos',
            'fecha_inicio'     => now()->toDateString(),
            'fecha_fin'        => now()->toDateString(),
            'generado_por'     => Auth::id(),
            'categoria_filtro' => $request->categoria ?? 'todos',
        ]);

        $datos = compact('repuestos', 'alertas', 'sinStock', 'valorTotal', 'request');

        if ($request->formato === 'pdf') {
            $pdf = Pdf::loadView('admin.reportes.pdf.stock', $datos);
            return $pdf->download('reporte-stock-' . now()->format('Y-m-d') . '.pdf');
        }

        return view('admin.reportes.stock-resultado', $datos);
    }

    // ── Generar reporte de turnos ────────────────────────────────
    public function turnos(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
            'estado'       => 'nullable|string',
        ]);

        $turnos = Turno::with(['cliente', 'vehiculo.marca', 'vehiculo.modelo', 'mecanico'])
            ->whereBetween('fecha_hora_turno', [
                Carbon::parse($request->fecha_inicio)->startOfDay(),
                Carbon::parse($request->fecha_fin)->endOfDay(),
            ])
            ->when($request->estado && $request->estado !== 'todos',
                fn($q) => $q->where('estado', $request->estado)
            )
            ->orderBy('fecha_hora_turno')
            ->get();

        Reporte::create([
            'tipo'             => 'turnos',
            'fecha_inicio'     => $request->fecha_inicio,
            'fecha_fin'        => $request->fecha_fin,
            'generado_por'     => Auth::id(),
            'categoria_filtro' => $request->estado ?? 'todos',
        ]);

        $datos = compact('turnos', 'request');

        if ($request->formato === 'pdf') {
            $pdf = Pdf::loadView('admin.reportes.pdf.turnos', $datos);
            return $pdf->download("reporte-turnos-{$request->fecha_inicio}-{$request->fecha_fin}.pdf");
        }

        return view('admin.reportes.turnos-resultado', $datos);
    }
}
