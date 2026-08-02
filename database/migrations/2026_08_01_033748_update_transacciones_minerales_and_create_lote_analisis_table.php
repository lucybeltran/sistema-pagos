<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modificar tabla transacciones_minerales
        Schema::table('transacciones_minerales', function (Blueprint $table) {
            $table->foreignId('lote_id')->nullable()->constrained('transacciones_minerales')->nullOnDelete();
            $table->decimal('cantidad', 12, 2)->default(0);
            $table->decimal('cantidad_disponible', 12, 2)->default(0); // Para compras (stock restante)
            $table->decimal('peso_disponible', 12, 2)->default(0);     // Para compras (stock restante)
            $table->string('destino')->nullable();                      // Para ventas
            $table->string('presentacion_otro')->nullable();             // Si presentacion = 'otro'
        });

        // 2. Crear tabla lote_analisis
        Schema::create('lote_analisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaccion_mineral_id')->constrained('transacciones_minerales')->cascadeOnDelete();
            $table->string('mineral'); // Zinc, Plomo, Plata, etc.
            $table->decimal('ley', 8, 2); // Ley en % (ej. 48.50)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lote_analisis');

        Schema::table('transacciones_minerales', function (Blueprint $table) {
            $table->dropForeign(['lote_id']);
            $table->dropColumn(['lote_id', 'cantidad', 'cantidad_disponible', 'peso_disponible', 'destino', 'presentacion_otro']);
        });
    }
};
