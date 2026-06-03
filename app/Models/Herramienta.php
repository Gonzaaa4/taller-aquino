<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Herramienta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'descripcion', 'tipo', 'estado',
        'ubicacion', 'fecha_adquisicion', 'activo',
    ];

    protected $casts = [
        'activo'            => 'boolean',
        'fecha_adquisicion' => 'date',
    ];

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}
