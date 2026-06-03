<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Diagnostico extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingreso_id', 'mecanico_id', 'descripcion_falla',
        'trabajos_propuestos', 'costo_estimado', 'estado', 'fecha_diagnostico',
    ];

    protected $casts = [
        'fecha_diagnostico' => 'datetime',
        'costo_estimado'    => 'decimal:2',
    ];

    public function ingreso()
    {
        return $this->belongsTo(IngresoVehiculo::class, 'ingreso_id');
    }

    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }
}
