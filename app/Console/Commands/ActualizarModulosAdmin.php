<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserModule;

class ActualizarModulosAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modulos:actualizar-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los módulos de todos los usuarios admin para incluir todos los módulos desarrollados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Actualizando módulos para usuarios admin...');

        // Módulos completos del sistema (sin dashboard, ya que es página separada)
        $modulosCompletos = [
            'unidades',
            'produccion',
            'inventarios',
            'usuarios_roles',
            'acciones_correctivas',
            'protocolos_limpieza',
            'ventas',
            'compras_proveedores',
            'reportes',
        ];

        // Obtener todos los usuarios admin
        $usuariosAdmin = User::where('role', 'admin')->get();

        foreach ($usuariosAdmin as $admin) {
            $this->info("📝 Actualizando módulos para: {$admin->name} ({$admin->email})");
            
            // Eliminar módulos actuales
            $admin->modules()->delete();
            
            // Agregar todos los módulos
            foreach ($modulosCompletos as $modulo) {
                $admin->modules()->create([
                    'module' => $modulo
                ]);
            }
        }

        $this->info("✅ Se actualizaron {$usuariosAdmin->count()} usuarios admin");
        $this->info("📋 Módulos asignados: " . implode(', ', $modulosCompletos));
        
        return 0;
    }
}
