<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->string('nombre_enfermedad')->nullable();
            $table->integer('cantidad_afectados')->nullable();
            $table->decimal('porcentaje_afectados', 8, 2)->nullable();
            $table->enum('nivel_riesgo', ['alto', 'medio', 'bajo'])->nullable();
            $table->string('estado_tratamiento')->nullable();
            $table->text('descripcion_tratamiento')->nullable();
            $table->date('fecha_deteccion')->nullable();
            $table->date('fecha_inicio_tratamiento')->nullable();
        });
    }

    public function down()
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropColumn([
                'nombre_enfermedad',
                'cantidad_afectados',
                'porcentaje_afectados',
                'nivel_riesgo',
                'estado_tratamiento',
                'descripcion_tratamiento',
                'fecha_deteccion',
                'fecha_inicio_tratamiento'
            ]);
        });
    }
};
