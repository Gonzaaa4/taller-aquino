<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Repuestos (inventario de piezas consumibles)
        Schema::create('repuestos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique()->nullable(); // código interno
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->enum('categoria', ['motor', 'transmision', 'frenos', 'suspension', 'electrico', 'lubricantes', 'filtros', 'otros'])->default('otros');
            $table->integer('cantidad_stock')->default(0);
            $table->integer('stock_minimo')->default(1); // alerta por debajo de este valor
            $table->decimal('costo', 10, 2)->default(0); // precio de costo
            $table->string('ubicacion_taller')->nullable(); // dónde está en el taller
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Herramientas (no consumibles, inventario de equipos)
        Schema::create('herramientas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['manual', 'electrica', 'especializada', 'medicion', 'otros'])->default('manual');
            $table->enum('estado', ['disponible', 'en_uso', 'en_reparacion', 'baja'])->default('disponible');
            $table->string('ubicacion')->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repuestos');
        Schema::dropIfExists('herramientas');
    }
};
