<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Repuesto;
use App\Models\Herramienta;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    // ── REPUESTOS ────────────────────────────────────────────────
    public function repuestos(Request $request)
    {
        $repuestos = Repuesto::with('proveedor')
            ->activo()
            ->when($request->categoria, fn($q) => $q->where('categoria', $request->categoria))
            ->when($request->buscar, fn($q) => $q->where('nombre', 'like', "%{$request->buscar}%"))
            ->when($request->stock_bajo, fn($q) => $q->conStockBajo())
            ->orderBy('nombre')
            ->paginate(20);

        $alertas = Repuesto::activo()->conStockBajo()->count();

        return view('admin.inventario.repuestos', compact('repuestos', 'alertas'));
    }

    public function crearRepuesto()
    {
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        return view('admin.inventario.crear-repuesto', compact('proveedores'));
    }

    public function guardarRepuesto(Request $request)
    {
        $datos = $request->validate([
            'codigo'          => 'nullable|string|max:50|unique:repuestos,codigo',
            'nombre'          => 'required|string|max:200',
            'descripcion'     => 'nullable|string',
            'categoria'       => 'required|in:motor,transmision,frenos,suspension,electrico,lubricantes,filtros,otros',
            'cantidad_stock'  => 'required|integer|min:0',
            'stock_minimo'    => 'required|integer|min:0',
            'costo'           => 'required|numeric|min:0',
            'ubicacion_taller'=> 'nullable|string|max:100',
            'proveedor_id'    => 'nullable|exists:proveedores,id',
        ]);

        Repuesto::create($datos);

        return redirect()->route('admin.inventario.repuestos')
            ->with('success', 'Repuesto registrado correctamente.');
    }

    public function editarRepuesto(Repuesto $repuesto)
    {
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        return view('admin.inventario.editar-repuesto', compact('repuesto', 'proveedores'));
    }

    public function actualizarRepuesto(Request $request, Repuesto $repuesto)
    {
        $datos = $request->validate([
            'codigo'          => "nullable|string|max:50|unique:repuestos,codigo,{$repuesto->id}",
            'nombre'          => 'required|string|max:200',
            'descripcion'     => 'nullable|string',
            'categoria'       => 'required|in:motor,transmision,frenos,suspension,electrico,lubricantes,filtros,otros',
            'cantidad_stock'  => 'required|integer|min:0',
            'stock_minimo'    => 'required|integer|min:0',
            'costo'           => 'required|numeric|min:0',
            'ubicacion_taller'=> 'nullable|string|max:100',
            'proveedor_id'    => 'nullable|exists:proveedores,id',
        ]);

        $repuesto->update($datos);

        return redirect()->route('admin.inventario.repuestos')
            ->with('success', 'Repuesto actualizado correctamente.');
    }

    public function ajustarStock(Request $request, Repuesto $repuesto)
    {
        $request->validate([
            'cantidad' => 'required|integer',
            'tipo'     => 'required|in:ingreso,egreso',
        ]);

        $nueva = $repuesto->cantidad_stock +
            ($request->tipo === 'ingreso' ? $request->cantidad : -$request->cantidad);

        if ($nueva < 0) {
            return back()->with('error', 'No hay suficiente stock para realizar el egreso.');
        }

        $repuesto->update(['cantidad_stock' => $nueva]);

        return back()->with('success', "Stock actualizado. Nuevo stock: {$nueva} unidades.");
    }

    // ── HERRAMIENTAS ─────────────────────────────────────────────
    public function herramientas(Request $request)
    {
        $herramientas = Herramienta::activo()
            ->when($request->tipo, fn($q) => $q->where('tipo', $request->tipo))
            ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
            ->orderBy('nombre')
            ->paginate(20);

        return view('admin.inventario.herramientas', compact('herramientas'));
    }

    public function guardarHerramienta(Request $request)
    {
        $datos = $request->validate([
            'nombre'            => 'required|string|max:200',
            'descripcion'       => 'nullable|string',
            'tipo'              => 'required|in:manual,electrica,especializada,medicion,otros',
            'estado'            => 'required|in:disponible,en_uso,en_reparacion,baja',
            'ubicacion'         => 'nullable|string|max:100',
            'fecha_adquisicion' => 'nullable|date',
        ]);

        Herramienta::create($datos);

        return redirect()->route('admin.inventario.herramientas')
            ->with('success', 'Herramienta registrada correctamente.');
    }
}
