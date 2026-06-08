<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE ingresos_vehiculo MODIFY COLUMN estado ENUM('ingresado','en_diagnostico','en_reparacion','en_proceso','finalizado','entregado') NOT NULL DEFAULT 'ingresado'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE ingresos_vehiculo MODIFY COLUMN estado ENUM('ingresado','en_diagnostico','en_reparacion','finalizado','entregado') NOT NULL DEFAULT 'ingresado'");
    }
};
