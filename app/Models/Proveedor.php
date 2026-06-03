<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre', 'contacto', 'telefono', 'email',
        'direccion', 'categoria', 'activo', 'notas',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function repuestos()
    {
        return $this->hasMany(Repuesto::class, 'proveedor_id');
    }
}
