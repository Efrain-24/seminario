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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            
            // Información básica
            $table->string('nombre', 150)->comment('Nombre o razón social del proveedor');
            $table->string('nit', 20)->nullable()->comment('NIT del proveedor');
            $table->string('codigo', 20)->unique()->comment('Código único del proveedor generado automáticamente');
            
            // Clasificación
            $table->enum('tipo', ['empresa', 'persona', 'cooperativa'])->comment('Tipo de proveedor');
            $table->enum('categoria', ['alimentos', 'insumos', 'equipos', 'servicios', 'medicamentos', 'mixto'])
                  ->comment('Categoría principal de productos/servicios');
            $table->enum('estado', ['activo', 'inactivo', 'suspendido'])->default('activo')->comment('Estado del proveedor');
            
            // Información de contacto
            $table->string('telefono_principal', 20)->nullable();
            $table->string('telefono_secundario', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('sitio_web', 150)->nullable();
            
            // Dirección
            $table->string('direccion', 255)->nullable();
            $table->string('departamento', 50)->nullable();
            $table->string('municipio', 50)->nullable();
            $table->string('zona', 10)->nullable();
            $table->string('codigo_postal', 10)->nullable();
            
            // Información comercial
            $table->decimal('limite_credito', 12, 2)->nullable()->comment('Límite de crédito en GTQ');
            $table->integer('dias_credito')->default(0)->comment('Días de crédito otorgados');
            $table->enum('forma_pago_preferida', ['contado', 'credito', 'transferencia', 'cheque'])->default('contado');
            $table->enum('moneda_preferida', ['GTQ', 'USD', 'EUR'])->default('GTQ');
            
            // Calificación y evaluación
            $table->decimal('calificacion', 3, 2)->nullable()->comment('Calificación del 1.00 al 5.00');
            $table->integer('total_evaluaciones')->default(0);
            $table->decimal('tiempo_entrega_promedio', 5, 2)->nullable()->comment('Días promedio de entrega');
            $table->decimal('porcentaje_cumplimiento', 5, 2)->nullable()->comment('% de cumplimiento en entregas');
            
            // Información financiera
            $table->decimal('saldo_actual', 12, 2)->default(0)->comment('Saldo actual en GTQ (+ a favor del proveedor)');
            $table->decimal('total_compras_mes', 12, 2)->default(0)->comment('Total compras del mes actual');
            $table->decimal('total_compras_historico', 12, 2)->default(0)->comment('Total histórico de compras');
            $table->date('fecha_ultima_compra')->nullable();
            $table->date('fecha_ultimo_pago')->nullable();
            
            // Contacto comercial
            $table->string('contacto_comercial_nombre', 100)->nullable()->comment('Nombre del contacto comercial');
            $table->string('contacto_comercial_telefono', 20)->nullable();
            $table->string('contacto_comercial_email', 100)->nullable();
            $table->string('contacto_comercial_cargo', 80)->nullable();
            
            // Información adicional
            $table->text('especialidades')->nullable()->comment('Especialidades o productos principales');
            $table->text('condiciones_especiales')->nullable()->comment('Condiciones comerciales especiales');
            $table->text('notas')->nullable()->comment('Notas adicionales');
            $table->boolean('requiere_orden_compra')->default(false)->comment('Si requiere orden de compra formal');
            $table->boolean('acepta_devoluciones')->default(true)->comment('Si acepta devoluciones');
            
            // Certificaciones y documentos
            $table->json('certificaciones')->nullable()->comment('Lista de certificaciones (ISO, HACCP, etc.)');
            $table->json('documentos')->nullable()->comment('Documentos adjuntos (contratos, certificados)');
            
            // Auditoría
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('registrado_por')->nullable();
            $table->unsignedBigInteger('actualizado_por')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['estado', 'categoria']);
            $table->index(['tipo', 'estado']);
            $table->index('fecha_ultima_compra');
            $table->index('calificacion');
            $table->index(['departamento', 'municipio']);
            // $table->fullText(['nombre', 'especialidades']); // No soportado en SQLite
            
            // Foreign keys
            $table->foreign('registrado_por')->references('id')->on('users')->nullOnDelete();
            $table->foreign('actualizado_por')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};