<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MovimientoCaja;
use App\Models\Factura;
use App\Models\TrabajoRealizado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ContabilidadController extends Controller
{
    // ── Libro de ingresos y egresos ──────────────────────
    public function libro(Request $request)
    {
        $anio = $request->anio ?? now()->year;
        $mes  = $request->mes  ?? now()->month;

        $movimientos = MovimientoCaja::with('registradoPor')
            ->whereYear('created_at', $anio)
            ->whereMonth('created_at', $mes)
            ->orderBy('created_at', 'desc')
            ->get();

        $ingresos = $movimientos->where('tipo', 'ingreso')->sum('monto');
        $egresos  = $movimientos->where('tipo', 'egreso')->sum('monto');
        $resultado = $ingresos - $egresos;

        // Agrupar por categoría
        $porCategoria = $movimientos->groupBy('categoria')->map(function ($items) {
            return [
                'ingresos' => $items->where('tipo', 'ingreso')->sum('monto'),
                'egresos'  => $items->where('tipo', 'egreso')->sum('monto'),
            ];
        });

        $anios = range(now()->year, now()->year - 3);

        return view('admin.contabilidad.libro', compact(
            'movimientos', 'ingresos', 'egresos', 'resultado',
            'porCategoria', 'anio', 'mes', 'anios'
        ));
    }

    // ── Rentabilidad mensual ─────────────────────────────
    public function rentabilidad(Request $request)
    {
        $anio = $request->anio ?? now()->year;

        // Ingresos y egresos por mes del año seleccionado
        $datos = collect(range(1, 12))->map(function ($mes) use ($anio) {
            $movs = MovimientoCaja::whereYear('created_at', $anio)
                ->whereMonth('created_at', $mes)
                ->get();

            $ingresos = $movs->where('tipo', 'ingreso')->sum('monto');
            $egresos  = $movs->where('tipo', 'egreso')->sum('monto');

            return [
                'mes'       => $mes,
                'ingresos'  => $ingresos,
                'egresos'   => $egresos,
                'ganancia'  => $ingresos - $egresos,
            ];
        });

        $totalIngresos = $datos->sum('ingresos');
        $totalEgresos  = $datos->sum('egresos');
        $totalGanancia = $datos->sum('ganancia');

        $anios = range(now()->year, now()->year - 3);

        return view('admin.contabilidad.rentabilidad', compact(
            'datos', 'totalIngresos', 'totalEgresos', 'totalGanancia', 'anio', 'anios'
        ));
    }

    // ── Margen por trabajo ───────────────────────────────
    public function margen(Request $request)
    {
        $desde = $request->desde ?? now()->startOfMonth()->toDateString();
        $hasta = $request->hasta ?? now()->toDateString();

        $facturas = Factura::with(['ingreso.trabajos', 'cliente', 'ingreso.vehiculo.marca', 'ingreso.vehiculo.modelo'])
            ->where('estado', '!=', 'anulada')
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($factura) {
                $costoMO  = $factura->ingreso->trabajos->sum('costo_mano_obra');
                $costoRep = $factura->ingreso->trabajos->sum('costo_repuestos');
                $costoTotal = $costoMO + $costoRep;
                $ganancia   = $factura->total - $costoTotal;
                $margen     = $factura->total > 0
                    ? round(($ganancia / $factura->total) * 100, 1)
                    : 0;

                return [
                    'factura'    => $factura,
                    'costo_mo'   => $costoMO,
                    'costo_rep'  => $costoRep,
                    'costo_total'=> $costoTotal,
                    'ganancia'   => $ganancia,
                    'margen'     => $margen,
                ];
            });

        $totalVentas   = $facturas->sum(fn($f) => $f['factura']->total);
        $totalCostos   = $facturas->sum('costo_total');
        $totalGanancia = $facturas->sum('ganancia');
        $margenPromedio = $totalVentas > 0
            ? round(($totalGanancia / $totalVentas) * 100, 1)
            : 0;

        return view('admin.contabilidad.margen', compact(
            'facturas', 'totalVentas', 'totalCostos', 'totalGanancia',
            'margenPromedio', 'desde', 'hasta'
        ));
    }
}