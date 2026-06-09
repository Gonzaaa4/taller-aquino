<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenCompraItem extends Model
{
    protected $table = 'ordenes_compra_items';

    protected $fillable = [
        'orden_compra_id', 'repuesto_id', 'cantidad_pedida',
        'cantidad_recibida', 'precio_unitario', 'subtotal',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal'        => 'decimal:2',
    ];

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class, 'repuesto_id');
    }

    public function pendienteRecibir(): int
    {
        return $this->cantidad_pedida - $this->cantidad_recibida;
    }
}