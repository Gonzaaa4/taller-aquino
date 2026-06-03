<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IngresoVehiculo extends Model
{
    use HasFactory;

    protected $table = 'ingresos_vehiculo';

    protected $fillable = [
        'turno_id', 'vehiculo_id', 'cliente_id', 'registrado_por',
        'kilometraje_ingreso', 'descripcion_problema',
        'fecha_ingreso', 'fecha_egreso', 'estado',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_egreso'  => 'datetime',
    ];

    public function etiquetaEstado(): string
    {
        return match($this->estado) {
            'ingresado'      => 'Ingresado',
            'en_diagnostico' => 'En Diagnóstico',
            'en_reparacion'  => 'En Reparación',
            'finalizado'     => 'Finalizado',
            'entregado'      => 'Entregado',
            default          => $this->estado,
        };
    }

    // ── Relaciones ───────────────────────────────────────────────
    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class, 'ingreso_id');
    }

    public function trabajos()
    {
        return $this->hasMany(TrabajoRealizado::class, 'ingreso_id');
    }

    public function egreso()
    {
        return $this->hasOne(EgresoVehiculo::class, 'ingreso_id');
    }
}
