<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\GenerarNotificacionesAutomaticas;

class GenerarNotificacionesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificaciones:automaticas {--force : Forzar ejecución}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar notificaciones automáticas del sistema (stock bajo, vencimientos, limpiezas pendientes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Iniciando generación de notificaciones automáticas...');
        
        try {
            // Ejecutar el job de notificaciones
            GenerarNotificacionesAutomaticas::dispatch();
            
            $this->info('✅ Notificaciones automáticas generadas exitosamente');
            $this->newLine();
            $this->line('📋 Verificaciones realizadas:');
            $this->line('   📦 Stock bajo en inventario');
            $this->line('   ⏰ Elementos próximos a vencer');
            $this->line('   🧹 Limpiezas pendientes');
            $this->line('   🗑️ Limpieza de notificaciones antiguas');
            
        } catch (\Exception $e) {
            $this->error('❌ Error al generar notificaciones: ' . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}
