<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mortalidades', function (Blueprint $table) {
            $table->foreignId('unidad_produccion_id')->nullable()->after('lote_id')->constrained('unidad_produccions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mortalidades', function (Blueprint $table) {
            $table->dropForeign(['unidad_produccion_id']);
            $table->dropColumn('unidad_produccion_id');
        });
    }
};
