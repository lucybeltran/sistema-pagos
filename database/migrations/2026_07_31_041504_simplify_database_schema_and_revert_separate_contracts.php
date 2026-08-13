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

        Schema::table('trabajadores', function (Blueprint $table) {
            if (!Schema::hasColumn('trabajadores', 'bocamina_id')) {
                $table->foreignId('bocamina_id')->nullable()->constrained('bocaminas')->nullOnDelete();
            }
            if (!Schema::hasColumn('trabajadores', 'tipo_contrato_id')) {
                $table->foreignId('tipo_contrato_id')->nullable()->constrained('tipos_contrato')->nullOnDelete();
            }
            if (!Schema::hasColumn('trabajadores', 'tarifa_acordada')) {
                $table->decimal('tarifa_acordada', 10, 2)->nullable();
            }
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trabajadores', function (Blueprint $table) {
            $table->dropForeign(['tipo_contrato_id']);
            $table->dropForeign(['bocamina_id']);
            $table->dropColumn(['bocamina_id', 'tipo_contrato_id', 'tarifa_acordada']);
        });

        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->foreignId('bocamina_id')->constrained('bocaminas')->cascadeOnDelete();
            $table->foreignId('tipo_contrato_id')->constrained('tipos_contrato')->cascadeOnDelete();
            $table->decimal('tarifa_acordada', 10, 2)->nullable();
            $table->string('estado')->default('activo');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }
};
