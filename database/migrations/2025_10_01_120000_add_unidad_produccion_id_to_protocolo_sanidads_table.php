<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('protocolo_sanidads', function (Blueprint $table) {
            if (!Schema::hasColumn('protocolo_sanidads', 'unidad_produccion_id')) {
                $table->foreignId('unidad_produccion_id')
                    ->nullable()
                    ->after('responsable')
                    ->constrained('unidad_produccions')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('protocolo_sanidads', function (Blueprint $table) {
            if (Schema::hasColumn('protocolo_sanidads', 'unidad_produccion_id')) {
                $table->dropForeign(['unidad_produccion_id']);
                $table->dropColumn('unidad_produccion_id');
            }
        });
    }
};
