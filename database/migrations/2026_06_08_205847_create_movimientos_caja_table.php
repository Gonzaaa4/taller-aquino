<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrado_por')->constrained('users');
            $table->foreignId('pago_id')->nullable()->constrained('pagos')->nullOnDelete();
            $table->enum('tipo', ['ingreso', 'egreso']);
            $table->decimal('monto', 12, 2);
            $table->string('concepto');
            $table->enum('categoria', ['venta', 'compra_repuestos', 'sueldos', 'servicios', 'otros'])->default('otros');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
    }
};