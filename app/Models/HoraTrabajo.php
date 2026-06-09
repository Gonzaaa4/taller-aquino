<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoraTrabajo extends Model
{
    protected $table = 'horas_trabajo';

    protected $fillable = [
        'mecanico_id', 'registrada_por', 'fecha', 'horas', 'tipo', 'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'horas' => 'decimal:1',
    ];

    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    public function registradaPor()
    {
        return $this->belongsTo(User::class, 'registrada_por');
    }
}