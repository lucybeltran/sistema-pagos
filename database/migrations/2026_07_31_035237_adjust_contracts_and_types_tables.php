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
        Schema::disableForeignKeyConstraints();

        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('DROP TABLE IF EXISTS contratos CASCADE');
        } else {
            Schema::dropIfExists('contratos');
        }

        if (!Schema::hasTable('tipos_contrato')) {
            Schema::create('tipos_contrato', function (Blueprint $table) {
                $table->id();
                $table->string('nombre')->unique();
                $table->string('estado')->default('activo'); // activo, inactivo
                $table->timestamps();
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trabajadores', function (Blueprint $table) {
            $table->foreignId('bocamina_id')->nullable()->constrained('bocaminas')->cascadeOnDelete();
            $table->foreignId('tipo_contrato_id')->nullable()->constrained('tipos_contrato')->nullOnDelete();
            $table->decimal('tarifa_acordada', 10, 2)->nullable();
        });

        Schema::dropIfExists('contratos');
        Schema::dropIfExists('tipos_contrato');
    }
};
