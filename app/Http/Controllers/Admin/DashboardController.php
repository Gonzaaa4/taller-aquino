<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Turno;
use App\Models\IngresoVehiculo;
use App\Models\TrabajoRealizado;
use App\Models\Repuesto;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $turnosPendientes  = Turno::where('estado', 'pendiente')->count();
        $vehiculosEnTaller = IngresoVehiculo::whereNotIn('estado', ['entregado'])->count();
        $trabajosMes       = TrabajoRealizado::whereMonth('fecha_trabajo', now()->month)
                                ->whereYear('fecha_trabajo', now()->year)
                                ->where('estado', 'finalizado')
                                ->count();
        $alertasStock      = Repuesto::activo()->conStockBajo()->count();

        $turnosHoy = Turno::with(['cliente', 'vehiculo.marca', 'vehiculo.modelo'])
            ->whereDate('fecha_hora_turno', today())
            ->whereNotIn('estado', ['cancelado'])
            ->orderBy('fecha_hora_turno')
            ->get();

        $repuestosCriticos = Repuesto::activo()
            ->conStockBajo()
            ->orderBy('cantidad_stock')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'turnosPendientes', 'vehiculosEnTaller', 'trabajosMes',
            'alertasStock', 'turnosHoy', 'repuestosCriticos'
        ));
    }
}
