<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();              // FAC-00001
            $table->foreignId('ingreso_id')->constrained('ingresos_vehiculo')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('generada_por')->constrained('users');
            $table->enum('tipo', ['presupuesto', 'factura'])->default('factura');
            $table->decimal('subtotal_mano_obra', 12, 2)->default(0);
            $table->decimal('subtotal_repuestos', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado', ['pendiente', 'pagada', 'parcial', 'anulada'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};