<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Registro de ingreso de vehículos al taller
        Schema::create('ingresos_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turno_id')->nullable()->constrained('turnos')->nullOnDelete();
            $table->foreignId('vehiculo_id')->constrained('vehiculos');
            $table->foreignId('cliente_id')->constrained('users');
            $table->foreignId('registrado_por')->constrained('users'); // empleado administrativo
            $table->integer('kilometraje_ingreso');
            $table->text('descripcion_problema')->nullable();
            $table->dateTime('fecha_ingreso')->useCurrent();
            $table->dateTime('fecha_egreso')->nullable();
            $table->enum('estado', ['ingresado', 'en_diagnostico', 'en_reparacion', 'finalizado', 'entregado'])->default('ingresado');
            $table->timestamps();
        });

        // Diagnósticos realizados por los mecánicos
        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingreso_id')->constrained('ingresos_vehiculo')->cascadeOnDelete();
            $table->foreignId('mecanico_id')->constrained('users');
            $table->text('descripcion_falla');
            $table->text('trabajos_propuestos');
            $table->decimal('costo_estimado', 10, 2)->default(0);
            $table->enum('estado', ['pendiente_aprobacion', 'aprobado', 'rechazado'])->default('pendiente_aprobacion');
            $table->dateTime('fecha_diagnostico')->useCurrent();
            $table->timestamps();
        });

        // Trabajos realizados (registrados por el mecánico)
        Schema::create('trabajos_realizados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingreso_id')->constrained('ingresos_vehiculo')->cascadeOnDelete();
            $table->foreignId('mecanico_id')->constrained('users');
            $table->enum('tipo_servicio', ['mantenimiento_preventivo', 'reparacion', 'diagnostico', 'service', 'otros'])->default('reparacion');
            $table->text('descripcion_trabajo'); // descripción técnica (mín 20 chars)
            $table->decimal('costo_mano_obra', 10, 2)->default(0);
            $table->decimal('costo_repuestos', 10, 2)->default(0); // calculado automáticamente
            $table->decimal('costo_total', 10, 2)->default(0);      // derivado: mano_obra + repuestos
            $table->integer('duracion_minutos')->nullable();
            $table->enum('estado', ['pendiente', 'en_proceso', 'finalizado'])->default('en_proceso');
            $table->dateTime('fecha_trabajo')->useCurrent();
            $table->timestamps();
        });

        // Repuestos utilizados en cada trabajo (tabla pivote con cantidad)
        Schema::create('trabajo_repuesto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajo_id')->constrained('trabajos_realizados')->cascadeOnDelete();
            $table->foreignId('repuesto_id')->constrained('repuestos');
            $table->integer('cantidad');
            $table->decimal('costo_unitario', 10, 2); // precio al momento del uso
            $table->decimal('subtotal', 10, 2);        // cantidad * costo_unitario
            $table->timestamps();
        });

        // Registro de egreso y conformidad del cliente
        Schema::create('egresos_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingreso_id')->constrained('ingresos_vehiculo')->cascadeOnDelete();
            $table->foreignId('registrado_por')->constrained('users'); // empleado administrativo
            $table->dateTime('fecha_egreso')->useCurrent();
            $table->integer('kilometraje_egreso')->nullable();
            $table->boolean('firma_conformidad')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('egresos_vehiculo');
        Schema::dropIfExists('trabajo_repuesto');
        Schema::dropIfExists('trabajos_realizados');
        Schema::dropIfExists('diagnosticos');
        Schema::dropIfExists('ingresos_vehiculo');
    }
};
