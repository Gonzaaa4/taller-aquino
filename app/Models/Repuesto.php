<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Repuesto extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'categoria',
        'cantidad_stock', 'stock_minimo', 'costo',
        'ubicacion_taller', 'proveedor_id', 'activo',
    ];

    protected $casts = [
        'activo'         => 'boolean',
        'cantidad_stock' => 'integer',
        'stock_minimo'   => 'integer',
        'costo'          => 'decimal:2',
    ];

    // ── Scopes ──────────────────────────────────────────────────
    public function scopeConStockBajo($query)
    {
        return $query->whereColumn('cantidad_stock', '<=', 'stock_minimo');
    }

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    // ── Helpers ─────────────────────────────────────────────────
    public function estadoStock(): string
    {
        if ($this->cantidad_stock <= 0) return 'sin_stock';
        if ($this->cantidad_stock <= $this->stock_minimo) return 'critico';
        return 'disponible';
    }

    public function tieneStockSuficiente(int $cantidad): bool
    {
        return $this->cantidad_stock >= $cantidad;
    }

    // ── Relaciones ───────────────────────────────────────────────
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function trabajos()
    {
        return $this->belongsToMany(TrabajoRealizado::class, 'trabajo_repuesto', 'repuesto_id', 'trabajo_id')
                    ->withPivot('cantidad', 'costo_unitario', 'subtotal')
                    ->withTimestamps();
    }
}
