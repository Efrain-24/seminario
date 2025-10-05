<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('entrada_compras', function (Blueprint $table) {
            $table->id();
            // referencia correcta a tabla proveedores
            $table->foreignId('proveedor_id')->constrained('proveedores')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('numero_documento')->nullable()->index();
            $table->date('fecha_documento')->nullable();
            $table->date('fecha_ingreso');
            $table->string('moneda', 5)->default('GTQ');
            $table->decimal('tipo_cambio', 10,4)->nullable();
            $table->decimal('subtotal', 15,2)->default(0);
            $table->decimal('impuesto', 15,2)->default(0);
            $table->decimal('total', 15,2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrada_compras');
    }
};
