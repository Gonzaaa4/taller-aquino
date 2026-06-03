<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['stock_repuestos', 'trabajos_realizados', 'turnos'])->default('trabajos_realizados');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->foreignId('generado_por')->constrained('users');
            $table->string('categoria_filtro')->nullable(); // mantenimiento_preventivo, reparacion, todos
            $table->string('orden')->nullable(); // tipo_trabajo, mayor_importe, menor_importe, empleado
            $table->dateTime('fecha_generacion')->useCurrent();
            $table->boolean('es_automatico')->default(false); // true = generado mensualmente por el sistema
            $table->string('archivo_pdf')->nullable(); // path al PDF guardado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
