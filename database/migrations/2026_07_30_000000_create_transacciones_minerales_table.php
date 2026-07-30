<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transacciones_minerales', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->enum('tipo', ['compra', 'venta']);
            $table->string('presentacion'); // saco, concentrado, volqueta, tonelada, otro
            $table->string('cliente_proveedor');
            $table->decimal('peso_bruto', 12, 2)->nullable();
            $table->decimal('humedad_porcentaje', 5, 2)->nullable();
            $table->decimal('peso_neto_seco', 12, 2)->nullable();
            $table->string('ley')->nullable(); // grade, e.g. "55% Zn"
            $table->decimal('precio_unidad', 12, 2)->nullable();
            $table->decimal('monto_total', 12, 2);
            $table->foreignId('bocamina_id')->nullable()->constrained('bocaminas')->nullOnDelete();
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transacciones_minerales');
    }
};
