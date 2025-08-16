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
        Schema::table('alimentacions', function (Blueprint $table) {
            $table->dropColumn([
                'frecuencia_diaria',
                'temperatura_agua',
                'ph_agua',
                'oxigeno_disuelto',
                'condiciones_climaticas'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alimentacions', function (Blueprint $table) {
            $table->integer('frecuencia_diaria')->nullable()->after('metodo_alimentacion');
            $table->decimal('temperatura_agua', 5, 2)->nullable()->after('frecuencia_diaria');
            $table->decimal('ph_agua', 4, 2)->nullable()->after('temperatura_agua');
            $table->decimal('oxigeno_disuelto', 5, 2)->nullable()->after('ph_agua');
            $table->json('condiciones_climaticas')->nullable()->after('observaciones');
        });
    }
};
