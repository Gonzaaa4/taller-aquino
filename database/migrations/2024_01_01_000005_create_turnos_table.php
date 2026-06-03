<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turnos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_seguimiento', 20)->unique(); // ej: TKA-04821
            $table->foreignId('cliente_id')->constrained('users');
            $table->foreignId('vehiculo_id')->constrained('vehiculos');
            $table->foreignId('mecanico_id')->nullable()->constrained('users')->nullOnDelete(); // mecánico asignado
            $table->dateTime('fecha_hora_turno');
            $table->dateTime('fecha_hora_solicitud')->useCurrent();
            $table->enum('tipo_servicio', ['mantenimiento_preventivo', 'reparacion', 'diagnostico', 'service', 'otros'])->default('reparacion');
            $table->text('observaciones')->nullable(); // descripción del problema
            $table->enum('estado', ['pendiente', 'confirmado', 'en_proceso', 'finalizado', 'cancelado'])->default('pendiente');
            // Control de cancelaciones (máx 2 por mes)
            $table->integer('cancelaciones_mes')->default(0);
            $table->boolean('suspendido')->default(false); // suspendido por exceso de cancelaciones
            $table->dateTime('fecha_cancelacion')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            // Datos para el cliente sin cuenta (modalidad presencial)
            $table->boolean('es_presencial')->default(false);
            $table->timestamps();
        });

        // Tabla de control de cancelaciones mensuales por cliente
        Schema::create('cancelaciones_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('users')->cascadeOnDelete();
            $table->integer('mes'); // 1-12
            $table->integer('anio');
            $table->integer('cantidad')->default(0);
            $table->unique(['cliente_id', 'mes', 'anio']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cancelaciones_cliente');
        Schema::dropIfExists('turnos');
    }
};
