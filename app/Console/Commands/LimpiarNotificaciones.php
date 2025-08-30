<?php

namespace App\Console\Commands;

use App\Models\Notificacion;
use Illuminate\Console\Command;

class LimpiarNotificaciones extends Command
{
    protected $signature = 'notificaciones:limpiar';
    protected $description = 'Limpiar todas las notificaciones de la base de datos';

    public function handle()
    {
        $total = Notificacion::count();
        
        if ($total === 0) {
            $this->info("✨ No hay notificaciones para limpiar");
            return Command::SUCCESS;
        }
        
        $this->info("🗑️ Eliminando {$total} notificaciones...");
        
        Notificacion::truncate();
        
        $this->info("✅ Todas las notificaciones han sido eliminadas");
        
        return Command::SUCCESS;
    }
}
