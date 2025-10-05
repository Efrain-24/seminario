<?php

namespace App\Console\Commands;

use App\Models\Notificacion;
use Illuminate\Console\Command;

class RevisarNotificaciones extends Command
{
    protected $signature = 'notificaciones:revisar';
    protected $description = 'Revisar notificaciones en la base de datos';

    public function handle()
    {
        $total = Notificacion::count();
        
        $this->info("=== REVISIÓN DE NOTIFICACIONES ===");
        $this->info("Total de notificaciones: {$total}");
        
        if ($total > 0) {
            $this->info("\nLISTADO:");
            
            Notificacion::all()->each(function($notificacion) {
                $this->line("ID: {$notificacion->id} | Tipo: {$notificacion->tipo} | Título: {$notificacion->titulo}");
                $this->line("Creada: {$notificacion->created_at}");
                $this->line(str_repeat("-", 80));
            });
            
            $this->warn("\n¿Deseas eliminar todas las notificaciones? Ejecuta:");
            $this->line("php artisan notificaciones:limpiar");
        } else {
            $this->info("✅ No hay notificaciones en la base de datos");
        }
        
        return Command::SUCCESS;
    }
}
