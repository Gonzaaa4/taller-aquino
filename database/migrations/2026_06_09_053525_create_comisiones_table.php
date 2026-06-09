<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mecanico_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('trabajo_id')->constrained('trabajos_realizados')->cascadeOnDelete();
            $table->foreignId('registrada_por')->constrained('users');
            $table->decimal('monto_base', 12, 2);
            $table->decimal('porcentaje', 5, 2)->default(0);
            $table->decimal('monto_comision', 12, 2);
            $table->enum('estado', ['pendiente', 'pagada'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comisiones');
    }
};