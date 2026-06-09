<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'factura_id', 'registrado_por', 'monto', 'metodo', 'referencia', 'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function movimientoCaja()
    {
        return $this->hasOne(MovimientoCaja::class, 'pago_id');
    }

    public function etiquetaMetodo(): string
    {
        return match ($this->metodo) {
            'efectivo'        => 'Efectivo',
            'transferencia'   => 'Transferencia',
            'tarjeta_debito'  => 'Tarjeta de débito',
            'tarjeta_credito' => 'Tarjeta de crédito',
            'cheque'          => 'Cheque',
            default           => ucfirst($this->metodo),
        };
    }
}