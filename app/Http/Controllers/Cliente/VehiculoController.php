<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehiculoController extends Controller
{
    public function index()
    {
        $vehiculos = Auth::user()->vehiculos()
            ->with(['marca', 'modelo'])
            ->where('activo', true)
            ->get();

        return view('cliente.vehiculos.index', compact('vehiculos'));
    }

    public function create()
    {
        $marcas = Marca::orderBy('nombre')->get();
        return view('cliente.vehiculos.crear', compact('marcas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'marca_id'   => 'required|exists:marcas,id',
            'modelo_id'  => 'required|exists:modelos,id',
            'anio'       => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'patente'    => 'required|string|max:20|unique:vehiculos,patente',
            'kilometraje'=> 'required|integer|min:0',
            'color'      => 'nullable|string|max:50',
        ], [
            'patente.unique' => 'Ya existe un vehículo registrado con esa patente.',
        ]);

        Vehiculo::create([
            'cliente_id' => Auth::id(),
            'marca_id'   => $request->marca_id,
            'modelo_id'  => $request->modelo_id,
            'anio'       => $request->anio,
            'patente'    => strtoupper($request->patente),
            'kilometraje'=> $request->kilometraje,
            'color'      => $request->color,
        ]);

        return redirect()->route('cliente.vehiculos.index')
            ->with('success', 'Vehículo agregado correctamente.');
    }
}
