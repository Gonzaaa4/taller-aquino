@extends('layouts.app')
@section('title', 'Mis Vehículos')
@section('topbar-title', 'Mis Vehículos')

@section('content')
<div class="page-header">
    <div class="page-header-top">
        <div>
            <div class="page-eyebrow">Portal del Cliente</div>
            <h1 class="page-title">Mis Vehículos</h1>
            <p class="page-subtitle">Vehículos registrados en tu cuenta</p>
        </div>
        <a href="{{ route('cliente.vehiculos.crear') }}" class="btn-primary-ta">
            <i class="bi bi-plus-circle"></i> Agregar Vehículo
        </a>
    </div>
</div>

@if($vehiculos->isEmpty())
<div class="ta-card">
    <div style="text-align:center; padding:64px 20px; color:var(--muted)">
        <i class="bi bi-car-front" style="font-size:3rem; display:block; margin-bottom:16px; opacity:.3"></i>
        <div style="font-family:'Oswald',sans-serif; font-size:1.2rem; color:var(--navy); margin-bottom:8px">No tenés vehículos registrados</div>
        <p style="font-size:.9rem; margin-bottom:20px">Agregá tu vehículo para poder solicitar turnos más rápido.</p>
        <a href="{{ route('cliente.vehiculos.crear') }}" class="btn-primary-ta" style="display:inline-flex">
            <i class="bi bi-plus-circle"></i> Agregar Vehículo
        </a>
    </div>
</div>
@else
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px">
    @foreach($vehiculos as $v)
    <div class="ta-card">
        <div style="padding:20px">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:12px">
                <div style="width:44px; height:44px; border-radius:10px; background:var(--light); display:flex; align-items:center; justify-content:center; flex-shrink:0">
                    <i class="bi bi-car-front" style="font-size:1.3rem; color:var(--blue)"></i>
                </div>
                <span style="font-family:'Oswald',sans-serif; font-size:.95rem; color:var(--accent); letter-spacing:.08em">
                    {{ $v->patente }}
                
            </div>
            <div style="font-family:'Oswald',sans-serif; font-size:1.1rem; color:var(--navy); margin-bottom:4px">
                {{ strtoupper($v->marca->nombre) }} {{ strtoupper($v->modelo->nombre) }}
            </div>
            <div style="font-size:.84rem; color:var(--muted); margin-bottom:14px">
                Año {{ $v->anio }} · {{ number_format($v->kilometraje) }} km
                @if($v->color) · {{ $v->color }}@endif
            </div>
            <a href="{{ route('cliente.turnos.solicitar') }}?vehiculo_id={{ $v->id }}"
               class="btn-primary-ta" style="width:100%; justify-content:center; font-size:.84rem; padding:8px">
                <i class="bi bi-calendar-plus"></i> Solicitar turno
            </a>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
