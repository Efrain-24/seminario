<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class LimpiarNotificacionesResueltasJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('🧹 Iniciando limpieza automática de notificaciones resueltas');
            
            // Ejecutar el comando de limpieza
            Artisan::call('notificaciones:limpiar-resueltas');
            
            Log::info('✅ Limpieza automática de notificaciones completada');
            
        } catch (\Exception $e) {
            Log::error('❌ Error en limpieza automática de notificaciones: ' . $e->getMessage());
        }
    }
}
