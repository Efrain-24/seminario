<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venta_id');
            $table->unsignedBigInteger('articulo_id')->nullable();
            $table->string('nombre_articulo');
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('cantidad', 12, 2);
            $table->decimal('total', 14, 2);
            $table->timestamps();
            $table->foreign('venta_id')->references('id')->on('ventas')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
