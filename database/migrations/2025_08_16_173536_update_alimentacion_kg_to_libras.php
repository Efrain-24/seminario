<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convertir los valores existentes de kg a libras (1 kg = 2.20462 libras)
        DB::statement('UPDATE alimentacions SET cantidad_kg = cantidad_kg * 2.20462');
        
        // Actualizar los costos para que reflejen el costo por libra
        DB::statement('UPDATE tipo_alimentos SET costo_por_kg = costo_por_kg / 2.20462 WHERE costo_por_kg IS NOT NULL');
        
        // Opcional: Añadir comentarios a las columnas para clarificar que ahora son libras
        Schema::table('alimentacions', function (Blueprint $table) {
            $table->decimal('cantidad_kg', 8, 2)->comment('Cantidad en libras')->change();
        });
        
        Schema::table('tipo_alimentos', function (Blueprint $table) {
            $table->decimal('costo_por_kg', 8, 2)->nullable()->comment('Costo por libra en quetzales')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir conversión de libras a kg
        DB::statement('UPDATE alimentacions SET cantidad_kg = cantidad_kg / 2.20462');
        
        // Revertir costos a costo por kg
        DB::statement('UPDATE tipo_alimentos SET costo_por_kg = costo_por_kg * 2.20462 WHERE costo_por_kg IS NOT NULL');
        
        Schema::table('alimentacions', function (Blueprint $table) {
            $table->decimal('cantidad_kg', 8, 3)->comment('Cantidad en kilogramos')->change();
        });
        
        Schema::table('tipo_alimentos', function (Blueprint $table) {
            $table->decimal('costo_por_kg', 8, 2)->nullable()->comment('Costo por kilogramo en quetzales')->change();
        });
    }
};
