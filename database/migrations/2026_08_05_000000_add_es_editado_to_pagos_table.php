<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pagos', 'es_editado')) {
            Schema::table('pagos', function (Blueprint $table) {
                $table->boolean('es_editado')->default(false);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pagos', 'es_editado')) {
            Schema::table('pagos', function (Blueprint $table) {
                $table->dropColumn('es_editado');
            });
        }
    }
};
