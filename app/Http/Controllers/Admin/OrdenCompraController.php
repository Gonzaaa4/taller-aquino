<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrdenCompra;
use App\Models\OrdenCompraItem;
use App\Models\RecepcionCompra;
use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Models\MovimientoCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdenCompraController extends Controller
{
    // ── Listado ──────────────────────────────────────────
    public function index(Request $request)
    {
        $ordenes = OrdenCompra::with(['proveedor', 'creadaPor'])
            ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
            ->when($request->proveedor_id, fn($q) => $q->where('proveedor_id', $request->proveedor_id))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();

        return view('admin.compras.index', compact('ordenes', 'proveedores'));
    }

    // ── Formulario crear ─────────────────────────────────
    public function crear()
    {
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $repuestos   = Repuesto::activo()->orderBy('nombre')->get();
        return view('admin.compras.crear', compact('proveedores', 'repuestos'));
    }

    // ── Guardar orden ────────────────────────────────────
    public function guardar(Request $request)
    {
        $request->validate([
            'proveedor_id'          => 'required|exists:proveedores,id',
            'fecha_esperada'        => 'nullable|date|after:today',
            'observaciones'         => 'nullable|string',
            'items'                 => 'required|array|min:1',
            'items.*.repuesto_id'   => 'required|exists:repuestos,id',
            'items.*.cantidad'      => 'required|integer|min:1',
            'items.*.precio'        => 'required|numeric|min:0',
        ], [
            'proveedor_id.required' => 'Seleccioná un proveedor.',
            'items.required'        => 'Agregá al menos un repuesto.',
            'items.*.repuesto_id.required' => 'Seleccioná el repuesto.',
            'items.*.cantidad.required'    => 'Ingresá la cantidad.',
            'items.*.precio.required'      => 'Ingresá el precio unitario.',
        ]);

        DB::transaction(function () use ($request) {
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['cantidad'] * $item['precio'];
            }

            $orden = OrdenCompra::create([
                'numero'        => OrdenCompra::generarNumero(),
                'proveedor_id'  => $request->proveedor_id,
                'creada_por'    => Auth::id(),
                'estado'        => 'enviada',
                'total'         => $total,
                'fecha_esperada'=> $request->fecha_esperada,
                'observaciones' => $request->observaciones,
            ]);

            foreach ($request->items as $item) {
                OrdenCompraItem::create([
                    'orden_compra_id' => $orden->id,
                    'repuesto_id'     => $item['repuesto_id'],
                    'cantidad_pedida' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal'        => $item['cantidad'] * $item['precio'],
                ]);
            }
        });

        return redirect()->route('admin.compras.index')
            ->with('success', 'Orden de compra creada correctamente.');
    }

    // ── Ver detalle ──────────────────────────────────────
    public function show(OrdenCompra $orden)
    {
        $orden->load(['proveedor', 'creadaPor', 'items.repuesto', 'recepciones.registradaPor']);
        return view('admin.compras.show', compact('orden'));
    }

    // ── Registrar recepción de mercadería ────────────────
    public function recibirMercaderia(Request $request, OrdenCompra $orden)
    {
        $request->validate([
            'items'            => 'required|array',
            'items.*.cantidad' => 'required|integer|min:0',
            'observaciones'    => 'nullable|string',
        ]);

        if (!in_array($orden->estado, ['enviada', 'recibida_parcial'])) {
            return back()->with('error', 'Esta orden no puede recibir mercadería.');
        }

        DB::transaction(function () use ($request, $orden) {
            $recepcion = RecepcionCompra::create([
                'orden_compra_id' => $orden->id,
                'registrada_por'  => Auth::id(),
                'observaciones'   => $request->observaciones,
            ]);

            $totalRecibido = 0;

            foreach ($request->items as $itemId => $data) {
                $item = OrdenCompraItem::findOrFail($itemId);
                $cantRecibida = (int) $data['cantidad'];

                if ($cantRecibida <= 0) continue;

                // Actualizar cantidad recibida
                $item->increment('cantidad_recibida', $cantRecibida);

                // Actualizar stock del repuesto
                $item->repuesto->increment('cantidad_stock', $cantRecibida);

                // Actualizar costo del repuesto
                $item->repuesto->update(['costo' => $item->precio_unitario]);

                $totalRecibido += $cantRecibida * $item->precio_unitario;
            }

            // Registrar egreso en caja
            if ($totalRecibido > 0) {
                MovimientoCaja::create([
                    'registrado_por' => Auth::id(),
                    'tipo'           => 'egreso',
                    'monto'          => $totalRecibido,
                    'concepto'       => "Compra {$orden->numero} - {$orden->proveedor->nombre}",
                    'categoria'      => 'compra_repuestos',
                ]);
            }

            // Actualizar estado de la orden
            $orden->load('items');
            $todasRecibidas = $orden->items->every(fn($i) => $i->cantidad_recibida >= $i->cantidad_pedida);
            $orden->update(['estado' => $todasRecibidas ? 'recibida' : 'recibida_parcial']);
        });

        return back()->with('success', 'Mercadería recibida. Stock actualizado correctamente.');
    }

    // ── Cancelar orden ───────────────────────────────────
    public function cancelar(OrdenCompra $orden)
    {
        if (!in_array($orden->estado, ['borrador', 'enviada'])) {
            return back()->with('error', 'Esta orden no puede ser cancelada.');
        }
        $orden->update(['estado' => 'cancelada']);
        return back()->with('success', 'Orden cancelada.');
    }
}