<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecepcionCompra extends Model
{
    protected $table = 'recepciones_compra';

    protected $fillable = [
        'orden_compra_id', 'registrada_por', 'observaciones',
    ];

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    public function registradaPor()
    {
        return $this->belongsTo(User::class, 'registrada_por');
    }
}