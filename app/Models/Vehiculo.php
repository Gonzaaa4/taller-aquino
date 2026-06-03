<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id', 'marca_id', 'modelo_id',
        'anio', 'patente', 'kilometraje', 'color', 'observaciones', 'activo',
    ];

    protected $casts = [
        'activo'      => 'boolean',
        'kilometraje' => 'integer',
    ];

    public function descripcion(): string
    {
        return "{$this->marca->nombre} {$this->modelo->nombre} {$this->anio} ({$this->patente})";
    }

    // ── Relaciones ───────────────────────────────────────────────
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }

    public function turnos()
    {
        return $this->hasMany(Turno::class);
    }

    public function ingresos()
    {
        return $this->hasMany(IngresoVehiculo::class);
    }

    public function trabajosRealizados()
    {
        return $this->hasManyThrough(TrabajoRealizado::class, IngresoVehiculo::class, 'vehiculo_id', 'ingreso_id');
    }
}
