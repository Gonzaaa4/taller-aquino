<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrabajoRealizado extends Model
{
    use HasFactory;

    protected $table = 'trabajos_realizados';

    protected $fillable = [
        'ingreso_id', 'mecanico_id', 'tipo_servicio',
        'descripcion_trabajo', 'costo_mano_obra',
        'costo_repuestos', 'costo_total',
        'duracion_minutos', 'estado', 'fecha_trabajo',
    ];

    protected $casts = [
        'fecha_trabajo'    => 'datetime',
        'costo_mano_obra'  => 'decimal:2',
        'costo_repuestos'  => 'decimal:2',
        'costo_total'      => 'decimal:2',
    ];

    // ── Boot: recalcula costo_total automáticamente ──────────────
    protected static function booted(): void
    {
        static::saving(function (TrabajoRealizado $trabajo) {
            $trabajo->costo_total = $trabajo->costo_mano_obra + $trabajo->costo_repuestos;
        });
    }

    public function etiquetaEstado(): string
    {
        return match($this->estado) {
            'pendiente'   => 'Pendiente',
            'en_proceso'  => 'En proceso',
            'finalizado'  => 'Finalizado',
            default       => $this->estado,
        };
    }

    // ── Relaciones ───────────────────────────────────────────────
    public function ingreso()
    {
        return $this->belongsTo(IngresoVehiculo::class, 'ingreso_id');
    }

    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    public function repuestos()
    {
        return $this->belongsToMany(Repuesto::class, 'trabajo_repuesto', 'trabajo_id', 'repuesto_id')
                    ->withPivot('cantidad', 'costo_unitario', 'subtotal')
                    ->withTimestamps();
    }
}
