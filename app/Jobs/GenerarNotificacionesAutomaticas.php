<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class GenerarNotificacionesAutomaticas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('🔄 Iniciando generación automática de notificaciones');
            
            // Ejecutar el comando de generación de notificaciones reales
            // (que ya incluye la limpieza de notificaciones resueltas)
            Artisan::call('notificaciones:generar-reales');
            
            Log::info('✅ Generación automática de notificaciones completada');
            
        } catch (\Exception $e) {
            Log::error('❌ Error en generación automática de notificaciones: ' . $e->getMessage());
        }
    }
}
