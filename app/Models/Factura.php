<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'facturas';

    protected $fillable = [
        'numero', 'ingreso_id', 'cliente_id', 'generada_por', 'tipo',
        'subtotal_mano_obra', 'subtotal_repuestos', 'descuento', 'total',
        'estado', 'observaciones',
    ];

    protected $casts = [
        'subtotal_mano_obra' => 'decimal:2',
        'subtotal_repuestos' => 'decimal:2',
        'descuento'          => 'decimal:2',
        'total'              => 'decimal:2',
    ];

    // ── Relaciones ───────────────────────────────────────
    public function ingreso()
    {
        return $this->belongsTo(IngresoVehiculo::class, 'ingreso_id');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function generadaPor()
    {
        return $this->belongsTo(User::class, 'generada_por');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'factura_id');
    }

    // ── Métodos ──────────────────────────────────────────
    public function totalPagado(): float
    {
        return (float) $this->pagos()->sum('monto');
    }

    public function saldoPendiente(): float
    {
        return (float) $this->total - $this->totalPagado();
    }

    public function actualizarEstado(): void
    {
        $pagado = $this->totalPagado();
        if ($pagado <= 0) {
            $estado = 'pendiente';
        } elseif ($pagado >= $this->total) {
            $estado = 'pagada';
        } else {
            $estado = 'parcial';
        }
        $this->update(['estado' => $estado]);
    }

    public function etiquetaEstado(): string
    {
        return match ($this->estado) {
            'pendiente' => 'Pendiente',
            'pagada'    => 'Pagada',
            'parcial'   => 'Pago parcial',
            'anulada'   => 'Anulada',
            default     => ucfirst($this->estado),
        };
    }

    // Generar número correlativo
    public static function generarNumero(): string
    {
        $ultimo = static::orderBy('id', 'desc')->first();
        $numero = $ultimo ? ((int) str_replace('FAC-', '', $ultimo->numero)) + 1 : 1;
        return 'FAC-' . str_pad($numero, 5, '0', STR_PAD_LEFT);
    }
}