<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Marcas de vehículos
        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('origen')->nullable();
            $table->timestamps();
        });

        // Modelos de vehículos (asociados a una marca)
        Schema::create('modelos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo')->nullable(); // sedan, suv, pickup, etc.
            $table->string('cilindrada')->nullable();
            $table->foreignId('marca_id')->constrained('marcas')->cascadeOnDelete();
            $table->timestamps();
        });

        // Vehículos de clientes
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('marca_id')->constrained('marcas');
            $table->foreignId('modelo_id')->constrained('modelos');
            $table->year('anio'); // año del vehículo
            $table->string('patente', 20)->unique(); // matrícula / placa
            $table->integer('kilometraje')->default(0);
            $table->string('color')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
        Schema::dropIfExists('modelos');
        Schema::dropIfExists('marcas');
    }
};
