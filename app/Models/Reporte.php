<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reporte extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo', 'fecha_inicio', 'fecha_fin', 'generado_por',
        'categoria_filtro', 'orden', 'fecha_generacion',
        'es_automatico', 'archivo_pdf',
    ];

    protected $casts = [
        'fecha_inicio'     => 'date',
        'fecha_fin'        => 'date',
        'fecha_generacion' => 'datetime',
        'es_automatico'    => 'boolean',
    ];

    public function generadoPor()
    {
        return $this->belongsTo(User::class, 'generado_por');
    }

    public function etiquetaTipo(): string
    {
        return match($this->tipo) {
            'stock_repuestos'    => 'Stock de Repuestos',
            'trabajos_realizados'=> 'Trabajos Realizados',
            'turnos'             => 'Turnos',
            default              => $this->tipo,
        };
    }
}
