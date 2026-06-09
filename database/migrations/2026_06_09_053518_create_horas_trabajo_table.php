<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horas_trabajo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mecanico_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('registrada_por')->constrained('users');
            $table->date('fecha');
            $table->decimal('horas', 4, 1);
            $table->enum('tipo', ['normal', 'extra'])->default('normal');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horas_trabajo');
    }
};