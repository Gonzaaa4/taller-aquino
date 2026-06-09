<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->cascadeOnDelete();
            $table->foreignId('registrado_por')->constrained('users');
            $table->decimal('monto', 12, 2);
            $table->enum('metodo', ['efectivo', 'transferencia', 'tarjeta_debito', 'tarjeta_credito', 'cheque']);
            $table->string('referencia')->nullable();         // nro de transferencia, etc.
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};