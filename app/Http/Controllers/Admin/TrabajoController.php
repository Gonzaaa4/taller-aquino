<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IngresoVehiculo;
use App\Models\TrabajoRealizado;
use App\Models\Repuesto;
use App\Models\Turno;
use App\Models\Vehiculo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrabajoController extends Controller
{
    // ── Registrar ingreso de vehículo ────────────────────────────
    public function registrarIngreso(Request $request)
    {
        $request->validate([
            'turno_id'             => 'nullable|exists:turnos,id',
            'vehiculo_id'          => 'required|exists:vehiculos,id',
            'cliente_id'           => 'required|exists:users,id',
            'kilometraje_ingreso'  => 'required|integer|min:0',
            'descripcion_problema' => 'nullable|string',
        ]);

        $ingreso = IngresoVehiculo::create([
            'turno_id'             => $request->turno_id,
            'vehiculo_id'          => $request->vehiculo_id,
            'cliente_id'           => $request->cliente_id,
            'registrado_por'       => Auth::id(),
            'kilometraje_ingreso'  => $request->kilometraje_ingreso,
            'descripcion_problema' => $request->descripcion_problema,
            'estado'               => 'ingresado',
        ]);

        if ($request->turno_id) {
            Turno::find($request->turno_id)->update(['estado' => 'en_proceso']);
        }

        return redirect()->route('admin.trabajos.show', $ingreso)
            ->with('success', 'Vehículo ingresado. N° de seguimiento del turno asignado.');
    }

    // ── Registrar trabajo realizado (mecánico) ───────────────────
    public function show(IngresoVehiculo $ingreso)
    {
        $ingreso->load(['vehiculo.marca', 'vehiculo.modelo', 'cliente', 'diagnosticos', 'trabajos.repuestos', 'turno']);
        $repuestos = Repuesto::activo()->where('cantidad_stock', '>', 0)->orderBy('nombre')->get();
        return view('admin.trabajos.show', compact('ingreso', 'repuestos'));
    }

    public function guardarTrabajo(Request $request, IngresoVehiculo $ingreso)
    {
        $request->validate([
            'tipo_servicio'      => 'required|in:mantenimiento_preventivo,reparacion,diagnostico,service,otros',
            'descripcion_trabajo'=> 'required|string|min:20',
            'costo_mano_obra'    => 'required|numeric|min:0',
            'estado'             => 'required|in:pendiente,en_proceso,finalizado',
            'repuestos'          => 'nullable|array',
            'repuestos.*.id'     => 'required|exists:repuestos,id',
            'repuestos.*.cantidad' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $ingreso) {
            $costoRepuestos = 0;
            $repuestosData  = [];

            // Verificar stock y preparar datos de repuestos
            if ($request->has('repuestos')) {
                foreach ($request->repuestos as $item) {
                    $repuesto = Repuesto::findOrFail($item['id']);

                    if (! $repuesto->tieneStockSuficiente($item['cantidad'])) {
                        throw new \Exception("Stock insuficiente para '{$repuesto->nombre}'. Disponible: {$repuesto->cantidad_stock}.");
                    }

                    $subtotal = $repuesto->costo * $item['cantidad'];
                    $costoRepuestos += $subtotal;

                    $repuestosData[$item['id']] = [
                        'cantidad'      => $item['cantidad'],
                        'costo_unitario'=> $repuesto->costo,
                        'subtotal'      => $subtotal,
                    ];

                    // Descontar del inventario
                    $repuesto->decrement('cantidad_stock', $item['cantidad']);
                }
            }

            // Crear el trabajo
            $trabajo = TrabajoRealizado::create([
                'ingreso_id'         => $ingreso->id,
                'mecanico_id'        => Auth::id(),
                'tipo_servicio'      => $request->tipo_servicio,
                'descripcion_trabajo'=> $request->descripcion_trabajo,
                'costo_mano_obra'    => $request->costo_mano_obra,
                'costo_repuestos'    => $costoRepuestos,
                'estado'             => $request->estado,
            ]);

            // Sincronizar repuestos con la tabla pivote
            if (! empty($repuestosData)) {
                $trabajo->repuestos()->attach($repuestosData);
            }

            // Actualizar estado del ingreso
            if ($request->estado === 'finalizado') {
                $ingreso->update(['estado' => 'finalizado']);
            }
        });

        return redirect()->route('admin.trabajos.show', $ingreso)
            ->with('success', 'Trabajo registrado correctamente.');
    }

    // ── Registrar egreso de vehículo ─────────────────────────────
    public function registrarEgreso(Request $request, IngresoVehiculo $ingreso)
    {
        $request->validate([
            'kilometraje_egreso' => 'nullable|integer|min:0',
            'firma_conformidad'  => 'boolean',
            'observaciones'      => 'nullable|string',
        ]);

        $ingreso->egreso()->create([
            'registrado_por'    => Auth::id(),
            'kilometraje_egreso'=> $request->kilometraje_egreso,
            'firma_conformidad' => $request->boolean('firma_conformidad'),
            'observaciones'     => $request->observaciones,
        ]);

        $ingreso->update([
            'estado'       => 'entregado',
            'fecha_egreso' => now(),
        ]);

        if ($ingreso->turno) {
            $ingreso->turno->update(['estado' => 'finalizado']);
        }

        return redirect()->route('admin.trabajos.index')
            ->with('success', 'Vehículo entregado y egreso registrado.');
    }

    public function index(Request $request)
    {
        $ingresos = IngresoVehiculo::with(['vehiculo.marca', 'vehiculo.modelo', 'cliente', 'turno'])
            ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.trabajos.index', compact('ingresos'));
    }
}
