<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Turno;
use App\Models\Vehiculo;
use App\Models\IngresoVehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class TurnoController extends Controller
{
    public function index()
    {
        $turnos = Auth::user()->turnos()
            ->with(['vehiculo.marca', 'vehiculo.modelo', 'mecanico'])
            ->orderBy('fecha_hora_turno', 'desc')
            ->paginate(10);

        return view('cliente.turnos.index', compact('turnos'));
    }

    public function solicitar()
    {
        $marcas   = Marca::orderBy('nombre')->get();
        $vehiculos = Auth::user()->vehiculos()->with(['marca', 'modelo'])->where('activo', true)->get();

        return view('cliente.turnos.solicitar', compact('marcas', 'vehiculos'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'fecha_hora_turno' => 'required|date|after:now',
            'tipo_servicio'    => 'required|in:mantenimiento_preventivo,reparacion,diagnostico,service,otros',
            'observaciones'    => 'nullable|string|max:500',
            // Vehículo: puede ser uno existente o nuevo
            'vehiculo_id'      => 'nullable|exists:vehiculos,id',
            'marca_id'         => 'required_without:vehiculo_id|exists:marcas,id',
            'modelo_id'        => 'required_without:vehiculo_id|exists:modelos,id',
            'anio'             => 'required_without:vehiculo_id|integer|min:1900|max:' . (date('Y') + 1),
            'patente'          => 'required_without:vehiculo_id|string|max:20',
            'kilometraje'      => 'required_without:vehiculo_id|integer|min:0',
        ], [
            'fecha_hora_turno.required' => 'La fecha y hora del turno son obligatorias.',
            'fecha_hora_turno.after'    => 'El turno debe ser en una fecha y hora futura.',
            'tipo_servicio.required'    => 'Seleccioná el tipo de servicio.',
        ]);

        $cliente = Auth::user();

        // Verificar si está suspendido
        if ($cliente->esCliente() && $this->clienteEstaSuspendido($cliente)) {
            return back()->with('error', 'Tu cuenta está suspendida temporalmente por exceso de cancelaciones. Contactá al taller.');
        }

        // Verificar disponibilidad de horario
        $conflicto = Turno::whereDate('fecha_hora_turno', Carbon::parse($request->fecha_hora_turno)->toDateString())
            ->whereTime('fecha_hora_turno', Carbon::parse($request->fecha_hora_turno)->toTimeString())
            ->whereNotIn('estado', ['cancelado'])
            ->exists();

        if ($conflicto) {
            return back()->with('error', 'El horario seleccionado ya está ocupado. Por favor elegí otro.');
        }

        // Obtener o crear vehículo
        $vehiculoId = $request->vehiculo_id;
        if (! $vehiculoId) {
            $vehiculo = Vehiculo::create([
                'cliente_id' => $cliente->id,
                'marca_id'   => $request->marca_id,
                'modelo_id'  => $request->modelo_id,
                'anio'       => $request->anio,
                'patente'    => strtoupper($request->patente),
                'kilometraje'=> $request->kilometraje,
            ]);
            $vehiculoId = $vehiculo->id;
        }

        $turno = Turno::create([
            'cliente_id'       => $cliente->id,
            'vehiculo_id'      => $vehiculoId,
            'fecha_hora_turno' => $request->fecha_hora_turno,
            'tipo_servicio'    => $request->tipo_servicio,
            'observaciones'    => $request->observaciones,
            'estado'           => 'pendiente',
        ]);

        return redirect()->route('cliente.turnos.confirmacion', $turno)
            ->with('success', "¡Turno solicitado! Tu número de seguimiento es: {$turno->numero_seguimiento}");
    }

    public function confirmacion(Turno $turno)
    {
        $this->authorize('view', $turno);
        $turno->load(['vehiculo.marca', 'vehiculo.modelo']);
        return view('cliente.turnos.confirmacion', compact('turno'));
    }

    public function cancelar(Request $request, Turno $turno)
    {
        $this->authorize('view', $turno);

        if (! $turno->puedeSerCancelado()) {
            return back()->with('error', 'Este turno no puede ser cancelado.');
        }

        $cliente = Auth::user();
        $mes     = now()->month;
        $anio    = now()->year;

        // Contar cancelaciones del mes
        $cancelacionesMes = Turno::where('cliente_id', $cliente->id)
            ->where('estado', 'cancelado')
            ->whereMonth('fecha_cancelacion', $mes)
            ->whereYear('fecha_cancelacion', $anio)
            ->count();

        if ($cancelacionesMes >= 2) {
            // Suspender cliente
            $cliente->update(['activo' => false]);
            $turno->update([
                'estado'            => 'cancelado',
                'fecha_cancelacion' => now(),
                'motivo_cancelacion'=> $request->motivo ?? 'Cancelado por cliente',
                'suspendido'        => true,
            ]);
            return redirect()->route('cliente.dashboard')
                ->with('error', 'Has superado el límite de 2 cancelaciones por mes. Tu cuenta ha sido suspendida temporalmente.');
        }

        $turno->update([
            'estado'            => 'cancelado',
            'fecha_cancelacion' => now(),
            'motivo_cancelacion'=> $request->motivo ?? 'Cancelado por cliente',
        ]);

        $aviso = $turno->cancelacionEsTardia()
            ? ' (Nota: cancelaste con menos de 48 horas de anticipación.)'
            : '';

        return redirect()->route('cliente.turnos.index')
            ->with('success', "Turno cancelado correctamente.{$aviso}");
    }

    // ── Consultar estado de reparación (número de seguimiento) ───
    public function consultarEstado(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('cliente.consultar-estado');
        }

        $request->validate([
            'numero_seguimiento' => 'required|string',
        ], [
            'numero_seguimiento.required' => 'Ingresá el número de seguimiento.',
        ]);

        // Buscar por número de seguimiento del turno
        $turno = Turno::with(['vehiculo.marca', 'vehiculo.modelo', 'mecanico'])
            ->where('numero_seguimiento', strtoupper($request->numero_seguimiento))
            ->first();

        if (! $turno) {
            return back()->with('error', 'El número de seguimiento es incorrecto o no existe.');
        }

        $ingreso = $turno->ingreso?->load(['trabajos.repuestos', 'diagnosticos.mecanico']);

        return view('cliente.consultar-estado', compact('turno', 'ingreso'));
    }

    // ── Helper: obtener modelos por marca (AJAX) ─────────────────
    public function modelosPorMarca(Marca $marca)
    {
        return response()->json($marca->modelos()->orderBy('nombre')->get(['id', 'nombre']));
    }

    // ── Helper privado ───────────────────────────────────────────
    private function clienteEstaSuspendido($cliente): bool
    {
        // Verificar cancelaciones del mes actual (máx 2 permitidas)
        return Turno::where('cliente_id', $cliente->id)
            ->where('estado', 'cancelado')
            ->whereMonth('fecha_cancelacion', now()->month)
            ->whereYear('fecha_cancelacion', now()->year)
            ->count() >= 2;
    }
}
