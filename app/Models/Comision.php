<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comision extends Model
{
    protected $table = 'comisiones';

    protected $fillable = [
        'mecanico_id', 'trabajo_id', 'registrada_por',
        'monto_base', 'porcentaje', 'monto_comision', 'estado',
    ];

    protected $casts = [
        'monto_base'     => 'decimal:2',
        'porcentaje'     => 'decimal:2',
        'monto_comision' => 'decimal:2',
    ];

    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    public function trabajo()
    {
        return $this->belongsTo(TrabajoRealizado::class, 'trabajo_id');
    }

    public function registradaPor()
    {
        return $this->belongsTo(User::class, 'registrada_por');
    }
}