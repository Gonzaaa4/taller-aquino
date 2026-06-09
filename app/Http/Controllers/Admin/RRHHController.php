<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\HoraTrabajo;
use App\Models\Comision;
use App\Models\TrabajoRealizado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class RRHHController extends Controller
{
    // ── Panel principal de RRHH ──────────────────────────
    public function index()
    {
        $mecanicos = User::where('rol', 'mecanico')
            ->withCount(['trabajosComoMecanico as trabajos_mes' => fn($q) =>
                $q->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
            ])
            ->withSum(['horasTrabajo as horas_mes' => fn($q) =>
                $q->whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year)
            ], 'horas')
            ->withSum(['comisiones as comisiones_pendientes' => fn($q) =>
                $q->where('estado', 'pendiente')
            ], 'monto_comision')
            ->get();

        $trabajosMes = \App\Models\TrabajoRealizado::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('admin.rrhh.index', compact('mecanicos', 'trabajosMes'));
    }

    // ── Perfil de mecánico ───────────────────────────────
    public function perfil(User $mecanico, Request $request)
    {
        $mes  = $request->mes  ?? now()->month;
        $anio = $request->anio ?? now()->year;

        $trabajos = TrabajoRealizado::with(['ingreso.vehiculo.marca', 'ingreso.vehiculo.modelo'])
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $anio)
            ->orderBy('created_at', 'desc')
            ->get();

        $horas = $mecanico->horasTrabajo()
            ->whereMonth('fecha', $mes)
            ->whereYear('fecha', $anio)
            ->orderBy('fecha', 'desc')
            ->get();

        $comisiones = $mecanico->comisiones()
            ->with('trabajo')
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $anio)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalHoras         = $horas->sum('horas');
        $totalHorasExtra    = $horas->where('tipo', 'extra')->sum('horas');
        $totalComisiones    = $comisiones->sum('monto_comision');
        $comisionesPendientes = $comisiones->where('estado', 'pendiente')->sum('monto_comision');

        $anios = range(now()->year, now()->year - 2);

        $todosLosTrabajos = TrabajoRealizado::with(['ingreso.vehiculo.marca', 'ingreso.vehiculo.modelo'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.rrhh.perfil', compact(
            'mecanico', 'trabajos', 'horas', 'comisiones',
            'totalHoras', 'totalHorasExtra', 'totalComisiones',
            'comisionesPendientes', 'mes', 'anio', 'anios', 'todosLosTrabajos'
        ));
    }

    // ── Registrar horas ──────────────────────────────────
    public function registrarHoras(Request $request, User $mecanico)
    {
        $request->validate([
            'fecha'         => 'required|date',
            'horas'         => 'required|numeric|min:0.5|max:24',
            'tipo'          => 'required|in:normal,extra',
            'observaciones' => 'nullable|string|max:200',
        ], [
            'horas.min' => 'El mínimo es 0.5 horas.',
            'horas.max' => 'El máximo es 24 horas por día.',
        ]);

        HoraTrabajo::create([
            'mecanico_id'    => $mecanico->id,
            'registrada_por' => Auth::id(),
            'fecha'          => $request->fecha,
            'horas'          => $request->horas,
            'tipo'           => $request->tipo,
            'observaciones'  => $request->observaciones,
        ]);

        return back()->with('success', 'Horas registradas correctamente.');
    }

    // ── Registrar comisión ───────────────────────────────
    public function registrarComision(Request $request, User $mecanico)
    {
        $request->validate([
            'trabajo_id'  => 'required|exists:trabajos_realizados,id',
            'porcentaje'  => 'required|numeric|min:1|max:100',
        ], [
            'porcentaje.min' => 'El porcentaje mínimo es 1%.',
            'porcentaje.max' => 'El porcentaje máximo es 100%.',
        ]);

        $trabajo = TrabajoRealizado::findOrFail($request->trabajo_id);
        $montoComision = $trabajo->costo_mano_obra * ($request->porcentaje / 100);

        // Verificar que no tenga comisión ya
        if (Comision::where('trabajo_id', $trabajo->id)->where('mecanico_id', $mecanico->id)->exists()) {
            return back()->with('error', 'Este trabajo ya tiene una comisión registrada.');
        }

        Comision::create([
            'mecanico_id'    => $mecanico->id,
            'trabajo_id'     => $trabajo->id,
            'registrada_por' => Auth::id(),
            'monto_base'     => $trabajo->costo_mano_obra,
            'porcentaje'     => $request->porcentaje,
            'monto_comision' => $montoComision,
            'estado'         => 'pendiente',
        ]);

        return back()->with('success', "Comisión de $" . number_format($montoComision, 2, ',', '.') . " registrada.");
    }

    // ── Pagar comisión ───────────────────────────────────
    public function pagarComision(Comision $comision)
    {
        if ($comision->estado === 'pagada') {
            return back()->with('error', 'Esta comisión ya fue pagada.');
        }

        $comision->update(['estado' => 'pagada']);

        // Registrar egreso en caja
        \App\Models\MovimientoCaja::create([
            'registrado_por' => Auth::id(),
            'tipo'           => 'egreso',
            'monto'          => $comision->monto_comision,
            'concepto'       => "Comisión {$comision->mecanico->nombreCompleto()} - Trabajo #{$comision->trabajo_id}",
            'categoria'      => 'sueldos',
        ]);

        return back()->with('success', 'Comisión pagada y registrada en caja.');
    }

    // ── Eliminar horas ───────────────────────────────────
    public function eliminarHoras(HoraTrabajo $hora)
    {
        $hora->delete();
        return back()->with('success', 'Registro de horas eliminado.');
    }
}