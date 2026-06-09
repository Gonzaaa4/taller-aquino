<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    protected $table = 'ordenes_compra';

    protected $fillable = [
        'numero', 'proveedor_id', 'creada_por', 'estado',
        'total', 'fecha_esperada', 'observaciones',
    ];

    protected $casts = [
        'fecha_esperada' => 'date',
        'total'          => 'decimal:2',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function creadaPor()
    {
        return $this->belongsTo(User::class, 'creada_por');
    }

    public function items()
    {
        return $this->hasMany(OrdenCompraItem::class, 'orden_compra_id');
    }

    public function recepciones()
    {
        return $this->hasMany(RecepcionCompra::class, 'orden_compra_id');
    }

    public function etiquetaEstado(): string
    {
        return match($this->estado) {
            'borrador'          => 'Borrador',
            'enviada'           => 'Enviada',
            'recibida_parcial'  => 'Recibida parcial',
            'recibida'          => 'Recibida',
            'cancelada'         => 'Cancelada',
            default             => ucfirst($this->estado),
        };
    }

    public static function generarNumero(): string
    {
        $ultimo = static::orderBy('id', 'desc')->first();
        $numero = $ultimo ? ((int) str_replace('OC-', '', $ultimo->numero)) + 1 : 1;
        return 'OC-' . str_pad($numero, 5, '0', STR_PAD_LEFT);
    }
}