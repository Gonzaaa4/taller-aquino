<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use Illuminate\Support\Facades\Auth;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::with(['ingreso.vehiculo.marca', 'ingreso.vehiculo.modelo'])
            ->where('cliente_id', Auth::id())
            ->where('estado', '!=', 'anulada')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('cliente.facturas.index', compact('facturas'));
    }

    public function show(Factura $factura)
    {
        if ($factura->cliente_id !== Auth::id()) {
            abort(403);
        }

        $factura->load(['ingreso.vehiculo.marca', 'ingreso.vehiculo.modelo', 'ingreso.trabajos', 'pagos']);

        return view('cliente.facturas.show', compact('factura'));
    }
}