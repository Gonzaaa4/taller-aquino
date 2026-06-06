<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'apellido',
        'email',
        'dni',
        'telefono',
        'direccion',
        'rol',
        'password',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    // ── Helpers de rol ──────────────────────────────────────────
    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function esMecanico(): bool
    {
        return $this->rol === 'mecanico';
    }

    public function esAdministrativo(): bool
    {
        return $this->rol === 'administrativo';
    }

    public function esCliente(): bool
    {
        return $this->rol === 'cliente';
    }

    // Cualquier empleado del taller (no cliente)
    public function esEmpleado(): bool
    {
        return in_array($this->rol, ['admin', 'mecanico', 'administrativo']);
    }

    public function nombreCompleto(): string
    {
        return $this->name . ' ' . $this->apellido;
    }

    // ── Relaciones ───────────────────────────────────────────────
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'cliente_id');
    }

    public function turnos()
    {
        return $this->hasMany(Turno::class, 'cliente_id');
    }

    public function turnosComoMecanico()
    {
        return $this->hasMany(Turno::class, 'mecanico_id');
    }

    public function trabajosRealizados()
    {
        return $this->hasMany(TrabajoRealizado::class, 'mecanico_id');
    }

    public function reportesGenerados()
    {
        return $this->hasMany(Reporte::class, 'generado_por');
    }

    public function ingresosRegistrados()
    {
        return $this->hasMany(IngresoVehiculo::class, 'registrado_por');
    }
    public function trabajosComoMecanico()
    {
        return $this->hasMany(\App\Models\TrabajoRealizado::class, 'mecanico_id');
    }
}
