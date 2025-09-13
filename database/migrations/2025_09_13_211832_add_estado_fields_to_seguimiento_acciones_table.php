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
        Schema::table('seguimiento_acciones', function (Blueprint $table) {
            $table->string('estado_anterior')->nullable()->after('descripcion');
            $table->string('estado_nuevo')->nullable()->after('estado_anterior');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seguimiento_acciones', function (Blueprint $table) {
            $table->dropColumn(['estado_anterior', 'estado_nuevo']);
        });
    }
};
