<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\MovimientoCaja;
use App\Models\IngresoVehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FacturacionController extends Controller
{
    // ── Listado de facturas ──────────────────────────────
    public function index(Request $request)
    {
        $facturas = Factura::with(['cliente', 'ingreso.vehiculo.marca', 'ingreso.vehiculo.modelo'])
            ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.facturacion.index', compact('facturas'));
    }

    // ── Formulario para generar factura desde una orden ──
    public function crear(IngresoVehiculo $ingreso)
    {
        $ingreso->load(['vehiculo.marca', 'vehiculo.modelo', 'cliente', 'trabajos.repuestos']);

        // Verificar que no tenga factura ya
        if (Factura::where('ingreso_id', $ingreso->id)->where('estado', '!=', 'anulada')->exists()) {
            return redirect()->route('admin.facturacion.index')
                ->with('error', 'Esta orden ya tiene una factura generada.');
        }

        return view('admin.facturacion.crear', compact('ingreso'));
    }

    // ── Guardar factura ──────────────────────────────────
    public function guardar(Request $request, IngresoVehiculo $ingreso)
    {
        $request->validate([
            'tipo'               => 'required|in:presupuesto,factura',
            'subtotal_mano_obra' => 'required|numeric|min:0',
            'subtotal_repuestos' => 'required|numeric|min:0',
            'descuento'          => 'nullable|numeric|min:0',
            'observaciones'      => 'nullable|string',
        ]);

        $descuento = $request->descuento ?? 0;
        $total = ($request->subtotal_mano_obra + $request->subtotal_repuestos) - $descuento;

        $factura = Factura::create([
            'numero'             => Factura::generarNumero(),
            'ingreso_id'         => $ingreso->id,
            'cliente_id'         => $ingreso->cliente_id,
            'generada_por'       => Auth::id(),
            'tipo'               => $request->tipo,
            'subtotal_mano_obra' => $request->subtotal_mano_obra,
            'subtotal_repuestos' => $request->subtotal_repuestos,
            'descuento'          => $descuento,
            'total'              => $total,
            'estado'             => 'pendiente',
            'observaciones'      => $request->observaciones,
        ]);

        return redirect()->route('admin.facturacion.show', $factura)
            ->with('success', "Factura {$factura->numero} generada correctamente.");
    }

    // ── Ver detalle de factura ───────────────────────────
    public function show(Factura $factura)
    {
        $factura->load(['cliente', 'ingreso.vehiculo.marca', 'ingreso.vehiculo.modelo', 'ingreso.trabajos', 'pagos.registradoPor', 'generadaPor']);
        return view('admin.facturacion.show', compact('factura'));
    }

    // ── Registrar pago ───────────────────────────────────
    public function registrarPago(Request $request, Factura $factura)
    {
        $request->validate([
            'monto'   => 'required|numeric|min:0.01|max:' . $factura->saldoPendiente(),
            'metodo'  => 'required|in:efectivo,transferencia,tarjeta_debito,tarjeta_credito,cheque',
            'referencia' => 'nullable|string|max:100',
        ], [
            'monto.max' => 'El monto no puede superar el saldo pendiente de $' . number_format($factura->saldoPendiente(), 2, ',', '.'),
        ]);

        DB::transaction(function () use ($request, $factura) {
            // Crear el pago
            $pago = Pago::create([
                'factura_id'     => $factura->id,
                'registrado_por' => Auth::id(),
                'monto'          => $request->monto,
                'metodo'         => $request->metodo,
                'referencia'     => $request->referencia,
            ]);

            // Crear movimiento de caja (ingreso)
            MovimientoCaja::create([
                'registrado_por' => Auth::id(),
                'pago_id'        => $pago->id,
                'tipo'           => 'ingreso',
                'monto'          => $request->monto,
                'concepto'       => "Pago factura {$factura->numero}",
                'categoria'      => 'venta',
            ]);

            // Actualizar estado de la factura
            $factura->actualizarEstado();
        });

        return back()->with('success', 'Pago registrado correctamente.');
    }

    // ── Anular factura ───────────────────────────────────
    public function anular(Factura $factura)
    {
        if ($factura->totalPagado() > 0) {
            return back()->with('error', 'No se puede anular una factura con pagos registrados.');
        }
        $factura->update(['estado' => 'anulada']);
        return back()->with('success', 'Factura anulada.');
    }

    // ── CAJA: vista diaria ───────────────────────────────
    public function caja(Request $request)
    {
        $fecha = $request->fecha ?? now()->toDateString();

        $movimientos = MovimientoCaja::with('registradoPor')
            ->whereDate('created_at', $fecha)
            ->orderBy('created_at', 'desc')
            ->get();

        $ingresos = $movimientos->where('tipo', 'ingreso')->sum('monto');
        $egresos  = $movimientos->where('tipo', 'egreso')->sum('monto');
        $saldo    = $ingresos - $egresos;

        return view('admin.facturacion.caja', compact('movimientos', 'ingresos', 'egresos', 'saldo', 'fecha'));
    }

    // ── CAJA: registrar movimiento manual ────────────────
    public function guardarMovimiento(Request $request)
    {
        $request->validate([
            'tipo'      => 'required|in:ingreso,egreso',
            'monto'     => 'required|numeric|min:0.01',
            'concepto'  => 'required|string|max:200',
            'categoria' => 'required|in:venta,compra_repuestos,sueldos,servicios,otros',
        ]);

        MovimientoCaja::create([
            'registrado_por' => Auth::id(),
            'tipo'           => $request->tipo,
            'monto'          => $request->monto,
            'concepto'       => $request->concepto,
            'categoria'      => $request->categoria,
            'observaciones'  => $request->observaciones,
        ]);

        return back()->with('success', 'Movimiento registrado en caja.');
    }
}