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

        // 1. Drop the temporary contratos table with CASCADE for PostgreSQL
        DB::statement('DROP TABLE IF EXISTS contratos CASCADE');

        // 2. Create the catalog table for "Tipos de Contrato"
        Schema::create('tipos_contrato', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('estado')->default('activo'); // activo, inactivo
            $table->timestamps();
        });

        // 3. Recreate the "contratos" assignment table
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->foreignId('bocamina_id')->constrained('bocaminas')->cascadeOnDelete();
            $table->foreignId('tipo_contrato_id')->constrained('tipos_contrato')->cascadeOnDelete();
            $table->decimal('tarifa_acordada', 10, 2)->nullable();
            $table->string('estado')->default('activo'); // activo, inactivo
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // 4. Clean up trabajadores table
        Schema::table('trabajadores', function (Blueprint $table) {
            try {
                if (Schema::hasColumn('trabajadores', 'tipo_contrato_id')) {
                    $table->dropColumn('tipo_contrato_id');
                }
            } catch (\Throwable $e) {}

            try {
                if (Schema::hasColumn('trabajadores', 'bocamina_id')) {
                    $table->dropColumn('bocamina_id');
                }
            } catch (\Throwable $e) {}

            try {
                if (Schema::hasColumn('trabajadores', 'tarifa_acordada')) {
                    $table->dropColumn('tarifa_acordada');
                }
            } catch (\Throwable $e) {}
        });

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
