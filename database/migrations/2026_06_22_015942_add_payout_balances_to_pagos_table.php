<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->decimal('monto_pagado', 10, 2)->default(0.00)->after('neto');
            $table->decimal('saldo_pendiente', 10, 2)->default(0.00)->after('monto_pagado');
            $table->boolean('saldo_liquidado')->default(true)->after('saldo_pendiente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn(['monto_pagado', 'saldo_pendiente', 'saldo_liquidado']);
        });
    }
};
