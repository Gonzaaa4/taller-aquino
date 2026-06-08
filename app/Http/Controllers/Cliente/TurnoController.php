<?php
namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Turno;
use App\Models\Vehiculo;
use App\Models\IngresoVehiculo;
use App\Models\User;
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
        $marcas    = Marca::orderBy('nombre')->get();
        $vehiculos = Auth::user()->vehiculos()->with(['marca', 'modelo'])->where('activo', true)->get();
        return view('cliente.turnos.solicitar', compact('marcas', 'vehiculos'));
    }

    public function guardar(Request $request)
    {
        // Limpiar valores "otra/otro" antes de validar
        if ($request->marca_id === 'otra') {
            $request->merge(['marca_id' => null]);
        }
        if ($request->modelo_id === 'otro') {
            $request->merge(['modelo_id' => null]);
        }
        $request->validate([
            'fecha_hora_turno'     => 'required|date|after:now',
            'tipo_servicio'        => 'required|in:mantenimiento_preventivo,reparacion,diagnostico,service,otros',
            'observaciones'        => 'nullable|string|max:500',
            'vehiculo_id'          => 'nullable|exists:vehiculos,id',
            'marca_id'             => 'nullable|exists:marcas,id',
            'marca_nombre_custom'  => 'nullable|string|max:100',
            'modelo_id'            => 'nullable|exists:modelos,id',
            'modelo_nombre_custom' => 'nullable|string|max:100',
            'anio'                 => 'required_without:vehiculo_id|integer|min:1900|max:' . (date('Y') + 1),
            'patente'              => 'required_without:vehiculo_id|string|max:20',
            'kilometraje'          => 'required_without:vehiculo_id|integer|min:0',
        ], [
            'fecha_hora_turno.required' => 'La fecha y hora del turno son obligatorias.',
            'fecha_hora_turno.after'    => 'El turno debe ser en una fecha y hora futura.',
            'tipo_servicio.required'    => 'Seleccioná el tipo de servicio.',
            'anio.required_without'     => 'El año del vehículo es obligatorio.',
            'anio.integer'              => 'El año debe ser un número válido.',
            'patente.required_without'  => 'La patente del vehículo es obligatoria.',
            'kilometraje.required_without' => 'El kilometraje es obligatorio.',
        ]);

        $cliente = Auth::user();

        if ($cliente->esCliente() && $this->clienteEstaSuspendido($cliente)) {
            return back()->with('error', 'Tu cuenta está suspendida temporalmente por exceso de cancelaciones. Contactá al taller.');
        }

        $conflicto = Turno::whereDate('fecha_hora_turno', Carbon::parse($request->fecha_hora_turno)->toDateString())
            ->whereTime('fecha_hora_turno', Carbon::parse($request->fecha_hora_turno)->toTimeString())
            ->whereNotIn('estado', ['cancelado'])
            ->exists();

        if ($conflicto) {
            return back()->with('error', 'El horario seleccionado ya está ocupado. Por favor elegí otro.');
        }

        $vehiculoId = $request->vehiculo_id;
        if (!$vehiculoId) {
            if (!$request->marca_id && $request->marca_nombre_custom) {
                $marca = Marca::firstOrCreate(['nombre' => ucfirst(trim($request->marca_nombre_custom))]);
                $request->merge(['marca_id' => $marca->id]);
            }
            if (!$request->modelo_id && $request->modelo_nombre_custom) {
                $modelo = Modelo::firstOrCreate([
                    'nombre'   => ucfirst(trim($request->modelo_nombre_custom)),
                    'marca_id' => $request->marca_id,
                ]);
                $request->merge(['modelo_id' => $modelo->id]);
            }
            $vehiculo = Vehiculo::firstOrCreate(
                ['patente' => strtoupper($request->patente)],
                [
                    'cliente_id'  => auth()->id(),
                    'marca_id'    => $request->marca_id,
                    'modelo_id'   => $request->modelo_id,
                    'anio'        => $request->anio,
                    'kilometraje' => $request->kilometraje ?? 0,
                ]
            );
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
            ->with('success', "Turno solicitado. Tu numero de seguimiento es: {$turno->numero_seguimiento}");
    }

    public function confirmacion(Turno $turno)
    {
        if ($turno->cliente_id !== auth()->id()) {
            abort(403);
        }
        $turno->load(['vehiculo.marca', 'vehiculo.modelo']);
        return view('cliente.turnos.confirmacion', compact('turno'));
    }

    public function cancelar(Request $request, Turno $turno)
    {
        $this->authorize('view', $turno);

        if (!$turno->puedeSerCancelado()) {
            return back()->with('error', 'Este turno no puede ser cancelado.');
        }

        $cliente = Auth::user();
        $mes     = now()->month;
        $anio    = now()->year;

        $cancelacionesMes = Turno::where('cliente_id', $cliente->id)
            ->where('estado', 'cancelado')
            ->whereMonth('fecha_cancelacion', $mes)
            ->whereYear('fecha_cancelacion', $anio)
            ->count();

        if ($cancelacionesMes >= 2) {
            $cliente->update(['activo' => false]);
            $turno->update([
                'estado'             => 'cancelado',
                'fecha_cancelacion'  => now(),
                'motivo_cancelacion' => $request->motivo ?? 'Cancelado por cliente',
                'suspendido'         => true,
            ]);
            return redirect()->route('cliente.dashboard')
                ->with('error', 'Has superado el limite de 2 cancelaciones por mes. Tu cuenta ha sido suspendida temporalmente.');
        }

        $turno->update([
            'estado'             => 'cancelado',
            'fecha_cancelacion'  => now(),
            'motivo_cancelacion' => $request->motivo ?? 'Cancelado por cliente',
        ]);

        $aviso = $turno->cancelacionEsTardia()
            ? ' (Nota: cancelaste con menos de 48 horas de anticipacion.)'
            : '';

        return redirect()->route('cliente.turnos.index')
            ->with('success', "Turno cancelado correctamente.{$aviso}");
    }

    public function consultarEstado(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('cliente.consultar-estado');
        }

        $request->validate([
            'numero_seguimiento' => 'required|string',
        ], [
            'numero_seguimiento.required' => 'Ingresa el numero de seguimiento.',
        ]);

        $turno = Turno::with(['vehiculo.marca', 'vehiculo.modelo', 'mecanico'])
            ->where('numero_seguimiento', strtoupper($request->numero_seguimiento))
            ->first();

        if (!$turno) {
            return back()->with('error', 'El numero de seguimiento es incorrecto o no existe.');
        }

        $ingreso = $turno->ingreso?->load(['trabajos.repuestos', 'diagnosticos.mecanico']);

        return view('cliente.consultar-estado', compact('turno', 'ingreso'));
    }

    public function modelosPorMarca(Marca $marca)
    {
        return response()->json($marca->modelos()->orderBy('nombre')->get(['id', 'nombre']));
    }

    private function clienteEstaSuspendido($cliente): bool
    {
        return Turno::where('cliente_id', $cliente->id)
            ->where('estado', 'cancelado')
            ->whereMonth('fecha_cancelacion', now()->month)
            ->whereYear('fecha_cancelacion', now()->year)
            ->count() >= 2;
    }

    public function solicitarPublico()
    {
        $marcas    = Marca::orderBy('nombre')->get();
        $vehiculos = collect();
        return view('cliente.turnos.solicitar-publico', compact('marcas', 'vehiculos'));
    }

    public function guardarPublico(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string|max:100',
            'apellido'             => 'required|string|max:100',
            'dni'                  => 'required|string|max:20',
            'telefono'             => 'required|string|max:30',
            'email'                => 'nullable|email',
            'fecha_hora_turno'     => 'required|date|after:now',
            'tipo_servicio'        => 'required|in:mantenimiento_preventivo,reparacion,diagnostico,service,otros',
            'observaciones'        => 'nullable|string|max:500',
            'marca_id'             => 'nullable|exists:marcas,id',
            'marca_nombre_custom'  => 'nullable|string|max:100',
            'modelo_id'            => 'nullable|exists:modelos,id',
            'modelo_nombre_custom' => 'nullable|string|max:100',
            'anio'                 => 'required|integer|min:1990',
            'patente'              => 'required|string|max:20',
            'kilometraje'          => 'nullable|integer|min:0',
        ]);

        $cliente = User::where('dni', $request->dni)->first();
        if (!$cliente && $request->email) {
            $cliente = User::where('email', $request->email)->first();
        }
        if (!$cliente) {
            $cliente = User::create([
                'name'     => $request->name,
                'apellido' => $request->apellido,
                'email'    => $request->email ?? $request->dni . '@invitado.talleraquino.com',
                'dni'      => $request->dni,
                'telefono' => $request->telefono,
                'rol'      => 'cliente',
                'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(12)),
            ]);
        }

        if (!$request->marca_id && $request->marca_nombre_custom) {
            $marca = Marca::firstOrCreate(['nombre' => ucfirst(trim($request->marca_nombre_custom))]);
            $request->merge(['marca_id' => $marca->id]);
        }

        if (!$request->modelo_id && $request->modelo_nombre_custom) {
            $modelo = Modelo::firstOrCreate([
                'nombre'   => ucfirst(trim($request->modelo_nombre_custom)),
                'marca_id' => $request->marca_id,
            ]);
            $request->merge(['modelo_id' => $modelo->id]);
        }

        $vehiculo = Vehiculo::firstOrCreate(
            ['patente' => strtoupper($request->patente)],
            [
                'cliente_id'  => $cliente->id,
                'marca_id'    => $request->marca_id,
                'modelo_id'   => $request->modelo_id,
                'anio'        => $request->anio,
                'kilometraje' => $request->kilometraje ?? 0,
            ]
        );

        $conflicto = Turno::whereDate('fecha_hora_turno', Carbon::parse($request->fecha_hora_turno)->toDateString())
            ->whereTime('fecha_hora_turno', Carbon::parse($request->fecha_hora_turno)->toTimeString())
            ->whereNotIn('estado', ['cancelado'])
            ->exists();

        if ($conflicto) {
            return back()->with('error', 'El horario seleccionado ya esta ocupado. Por favor elegi otro.');
        }

        $turno = Turno::create([
            'cliente_id'       => $cliente->id,
            'vehiculo_id'      => $vehiculo->id,
            'fecha_hora_turno' => $request->fecha_hora_turno,
            'tipo_servicio'    => $request->tipo_servicio,
            'observaciones'    => $request->observaciones,
            'estado'           => 'pendiente',
        ]);

        return redirect()->route('turno.publico.confirmacion', $turno->numero_seguimiento)
            ->with('success', "Turno solicitado. Tu numero de seguimiento es: {$turno->numero_seguimiento}");
    }

    public function confirmacionPublica($numero)
    {
        $turno = Turno::with(['vehiculo.marca', 'vehiculo.modelo'])
            ->where('numero_seguimiento', $numero)
            ->firstOrFail();
        return view('cliente.turnos.confirmacion-publica', compact('turno'));
    }
}