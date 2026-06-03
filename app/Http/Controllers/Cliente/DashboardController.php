<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Turno;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $cliente = Auth::user();

        $proximoTurno = $cliente->turnos()
            ->with(['vehiculo.marca', 'vehiculo.modelo'])
            ->where('fecha_hora_turno', '>', now())
            ->whereNotIn('estado', ['cancelado'])
            ->orderBy('fecha_hora_turno')
            ->first();

        $turnosRecientes = $cliente->turnos()
            ->with(['vehiculo.marca', 'vehiculo.modelo'])
            ->orderBy('fecha_hora_turno', 'desc')
            ->take(5)
            ->get();

        $vehiculos = $cliente->vehiculos()
            ->with(['marca', 'modelo'])
            ->where('activo', true)
            ->get();

        return view('cliente.dashboard', compact('proximoTurno', 'turnosRecientes', 'vehiculos'));
    }
}
