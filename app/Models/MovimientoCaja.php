<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    protected $table = 'movimientos_caja';

    protected $fillable = [
        'registrado_por', 'pago_id', 'tipo', 'monto', 'concepto', 'categoria', 'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }

    public function etiquetaCategoria(): string
    {
        return match ($this->categoria) {
            'venta'            => 'Venta',
            'compra_repuestos' => 'Compra de repuestos',
            'sueldos'          => 'Sueldos',
            'servicios'        => 'Servicios',
            'otros'            => 'Otros',
            default            => ucfirst($this->categoria),
        };
    }
}