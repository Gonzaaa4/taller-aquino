<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Turno;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Modelo;

class TurnoController extends Controller
{
    public function index(Request $request)
    {
        $turnos = Turno::with(['cliente', 'vehiculo.marca', 'vehiculo.modelo', 'mecanico'])
            ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
            ->when($request->fecha, fn($q) => $q->whereDate('fecha_hora_turno', $request->fecha))
            ->orderBy('fecha_hora_turno', 'asc')
            ->paginate(15);

        $mecanicos = User::where('rol', 'mecanico')->where('activo', true)->get();

        return view('admin.turnos.index', compact('turnos', 'mecanicos'));
    }

    public function show(Turno $turno)
{
    $turno->load(['cliente', 'vehiculo.marca', 'vehiculo.modelo', 'mecanico', 'ingreso.trabajos.mecanico']);
    $mecanicos = \App\Models\User::where('rol', 'mecanico')->where('activo', true)->get();
    return view('admin.turnos.show', compact('turno', 'mecanicos'));
}

    public function confirmar(Request $request, Turno $turno)
    {
        if (!$turno->estaPendiente()) {
            return back()->with('error', 'Este turno no puede ser confirmado.');
        }

        $turno->update([
            'estado'      => 'confirmado',
            'mecanico_id' => $request->mecanico_id ?: null,
        ]);

        return redirect()->route('admin.turnos.show', $turno)
            ->with('success', "Turno #{$turno->numero_seguimiento} confirmado.");
    }

    public function asignarMecanico(Request $request, Turno $turno)
    {
        $request->validate(['mecanico_id' => 'required|exists:users,id']);

        $mecanico = User::findOrFail($request->mecanico_id);
        if (! $mecanico->esMecanico()) {
            return back()->with('error', 'El usuario seleccionado no es mecánico.');
        }

        $turno->update(['mecanico_id' => $request->mecanico_id]);

        return back()->with('success', "Mecánico {$mecanico->nombreCompleto()} asignado al turno.");
    }

    public function cancelar(Request $request, Turno $turno)
    {
        if (! $turno->puedeSerCancelado()) {
            return back()->with('error', 'Este turno no puede ser cancelado.');
        }

        $turno->update([
            'estado'            => 'cancelado',
            'fecha_cancelacion' => now(),
            'motivo_cancelacion'=> $request->motivo ?? 'Cancelado por administración',
        ]);

        return back()->with('success', 'Turno cancelado correctamente.');
    }

    // Agenda semanal del taller
    public function agenda(Request $request)
    {
        $semana = $request->semana ? Carbon::parse($request->semana)->startOfWeek() : now()->startOfWeek();

        $turnos = Turno::with(['cliente', 'vehiculo.marca', 'mecanico'])
            ->whereBetween('fecha_hora_turno', [$semana, $semana->copy()->endOfWeek()])
            ->whereNotIn('estado', ['cancelado'])
            ->orderBy('fecha_hora_turno')
            ->get()
            ->groupBy(fn($t) => $t->fecha_hora_turno->format('Y-m-d'));

        return view('admin.turnos.agenda', compact('turnos', 'semana'));
    }

    public function solicitar()
{
    $marcas    = \App\Models\Marca::orderBy('nombre')->get();
    $mecanicos = \App\Models\User::where('rol', 'mecanico')->where('activo', true)->get();
    return view('admin.turnos.solicitar', compact('marcas', 'mecanicos'));
}

public function guardar(Request $request)
{
    $request->validate([
        'name'             => 'required|string|max:100',
        'apellido'         => 'required|string|max:100',
        'dni'              => 'required|string|max:20',
        'telefono'         => 'required|string|max:30',
        'email'            => 'nullable|email',
        'marca_id'           => 'nullable|exists:marcas,id',
        'marca_nombre_custom'=> 'required_without:marca_id|nullable|string|max:100',
        'modelo_id'          => 'nullable|exists:modelos,id',
        'modelo_nombre_custom'=> 'required_without:modelo_id|nullable|string|max:100',
        'anio'             => 'required|integer|min:1990',
        'patente'          => 'required|string|max:20',
        'kilometraje'      => 'required|integer|min:0',
        'fecha_hora_turno' => 'required|date|after:now',
        'tipo_servicio'    => 'required|in:mantenimiento_preventivo,reparacion,diagnostico,service,otros',
        'mecanico_id'      => 'nullable|exists:users,id',
        'observaciones'    => 'nullable|string',
    ]);

    // Buscar cliente por DNI primero, luego por email
    $cliente = \App\Models\User::where('dni', $request->dni)->first();

    if (!$cliente && $request->email) {
        $cliente = \App\Models\User::where('email', $request->email)->first();
    }

    if (!$cliente) {
        $email = $request->email ?? $request->dni . '@presencial.talleraquino.com';
        $cliente = \App\Models\User::create([
            'name'     => $request->name,
            'apellido' => $request->apellido,
            'email'    => $email,
            'dni'      => $request->dni,
            'telefono' => $request->telefono,
            'rol'      => 'cliente',
            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(12)),
        ]);
    }

    // Si eligió "Otra" marca, crearla
    if (!$request->marca_id && $request->marca_nombre_custom) {
        $marca = \App\Models\Marca::firstOrCreate(
            ['nombre' => ucfirst(trim($request->marca_nombre_custom))]
        );
        $request->merge(['marca_id' => $marca->id]);
    }

    // Si eligió "Otro" modelo, crearlo
    if (!$request->modelo_id && $request->modelo_nombre_custom) {
        $modelo = \App\Models\Modelo::firstOrCreate(
            ['nombre' => ucfirst(trim($request->modelo_nombre_custom)), 'marca_id' => $request->marca_id]
        );
        $request->merge(['modelo_id' => $modelo->id]);
    }

    $vehiculo = \App\Models\Vehiculo::firstOrCreate(
        ['patente' => strtoupper($request->patente)],
        [
            'cliente_id' => $cliente->id,
            'marca_id'   => $request->marca_id,
            'modelo_id'  => $request->modelo_id,
            'anio'       => $request->anio,
            'kilometraje'=> $request->kilometraje,
        ]
    );

    $turno = \App\Models\Turno::create([
        'cliente_id'       => $cliente->id,
        'vehiculo_id'      => $vehiculo->id,
        'mecanico_id'      => $request->mecanico_id,
        'fecha_hora_turno' => $request->fecha_hora_turno,
        'tipo_servicio'    => $request->tipo_servicio,
        'observaciones'    => $request->observaciones,
        'estado'           => 'confirmado',
        'es_presencial'    => true,
    ]);

    return redirect()->route('admin.turnos.show', $turno)
        ->with('success', "Turno registrado. N° de seguimiento: {$turno->numero_seguimiento}");
}
}
