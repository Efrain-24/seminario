<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckDashboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar que el dashboard tenga acceso a todas las tablas necesarias';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verificando tablas necesarias para el dashboard...');
        
        $tables = [
            'lotes', 
            'alertas', 
            'notificaciones', 
            'mortalidades', 
            'seguimientos', 
            'unidad_produccions',
            'users'
        ];
        
        $missingTables = [];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $this->info("✓ Tabla '$table' existe");
            } else {
                $this->error("✗ Tabla '$table' NO existe");
                $missingTables[] = $table;
            }
        }
        
        if (empty($missingTables)) {
            $this->info("\n🎉 Todas las tablas necesarias existen!");
            
            // Verificar algunos datos de prueba
            try {
                $lotes = DB::table('lotes')->count();
                $this->info("📊 Lotes en BD: $lotes");
                
                $alertas = DB::table('alertas')->count();
                $this->info("⚠️  Alertas en BD: $alertas");
                
                $notificaciones = DB::table('notificaciones')->count();
                $this->info("🔔 Notificaciones en BD: $notificaciones");
                
                $mortalidades = DB::table('mortalidades')->count();
                $this->info("💀 Mortalidades en BD: $mortalidades");
                
            } catch (\Exception $e) {
                $this->error("Error al consultar datos: " . $e->getMessage());
            }
        } else {
            $this->error("\n❌ Faltan las siguientes tablas: " . implode(', ', $missingTables));
            return 1;
        }
        
        return 0;
    }
}
