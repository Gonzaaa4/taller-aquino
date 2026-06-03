<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Turno extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_seguimiento', 'cliente_id', 'vehiculo_id', 'mecanico_id',
        'fecha_hora_turno', 'fecha_hora_solicitud', 'tipo_servicio',
        'observaciones', 'estado', 'cancelaciones_mes', 'suspendido',
        'fecha_cancelacion', 'motivo_cancelacion', 'es_presencial',
    ];

    protected $casts = [
        'fecha_hora_turno'     => 'datetime',
        'fecha_hora_solicitud' => 'datetime',
        'fecha_cancelacion'    => 'datetime',
        'suspendido'           => 'boolean',
        'es_presencial'        => 'boolean',
    ];

    // ── Boot: genera número de seguimiento automáticamente ───────
    protected static function booted(): void
    {
        static::creating(function (Turno $turno) {
            if (empty($turno->numero_seguimiento)) {
                $turno->numero_seguimiento = self::generarNroSeguimiento();
            }
        });
    }

    public static function generarNroSeguimiento(): string
    {
        do {
            $codigo = 'TKA-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('numero_seguimiento', $codigo)->exists());

        return $codigo;
    }

    // ── Helpers de estado ────────────────────────────────────────
    public function estaPendiente(): bool    { return $this->estado === 'pendiente'; }
    public function estaConfirmado(): bool   { return $this->estado === 'confirmado'; }
    public function estaEnProceso(): bool    { return $this->estado === 'en_proceso'; }
    public function estaFinalizado(): bool   { return $this->estado === 'finalizado'; }
    public function estaCancelado(): bool    { return $this->estado === 'cancelado'; }

    public function puedeSerCancelado(): bool
    {
        return in_array($this->estado, ['pendiente', 'confirmado']);
    }

    // Verifica si cancelación está dentro de las 48 horas (penalización)
    public function cancelacionEsTardia(): bool
    {
        return now()->diffInHours($this->fecha_hora_turno, false) < 48;
    }

    public function etiquetaEstado(): string
    {
        return match($this->estado) {
            'pendiente'   => 'Pendiente',
            'confirmado'  => 'Confirmado',
            'en_proceso'  => 'En proceso',
            'finalizado'  => 'Finalizado',
            'cancelado'   => 'Cancelado',
            default       => $this->estado,
        };
    }

    public function colorEstado(): string
    {
        return match($this->estado) {
            'pendiente'   => 'warning',
            'confirmado'  => 'info',
            'en_proceso'  => 'primary',
            'finalizado'  => 'success',
            'cancelado'   => 'danger',
            default       => 'secondary',
        };
    }

    // ── Relaciones ───────────────────────────────────────────────
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    public function ingreso()
    {
        return $this->hasOne(IngresoVehiculo::class, 'turno_id');
    }
}
