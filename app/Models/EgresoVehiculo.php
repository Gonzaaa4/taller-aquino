<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EgresoVehiculo extends Model
{
    use HasFactory;

    protected $table = 'egresos_vehiculo';

    protected $fillable = [
        'ingreso_id', 'registrado_por',
        'fecha_egreso', 'kilometraje_egreso',
        'firma_conformidad', 'observaciones',
    ];

    protected $casts = [
        'fecha_egreso'      => 'datetime',
        'firma_conformidad' => 'boolean',
    ];

    public function ingreso()
    {
        return $this->belongsTo(IngresoVehiculo::class, 'ingreso_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
