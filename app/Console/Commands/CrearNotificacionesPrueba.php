<?php

namespace App\Console\Commands;

use App\Models\Notificacion;
use Illuminate\Console\Command;

class CrearNotificacionesPrueba extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificaciones:demo {--limpiar : Limpiar notificaciones existentes primero}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear notificaciones de prueba para demonstrar el sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('limpiar')) {
            $this->info('Limpiando notificaciones existentes...');
            Notificacion::truncate();
        }

        $this->info('Creando notificaciones de prueba...');

        // Notificación de éxito
        Notificacion::create([
            'tipo' => 'success',
            'titulo' => '¡Sistema de Notificaciones Activado!',
            'mensaje' => 'El sistema de notificaciones se ha configurado correctamente y está funcionando.',
            'datos' => ['demo' => true],
            'icono' => 'check-circle',
            'url' => null,
        ]);

        // Alerta de inventario
        Notificacion::create([
            'tipo' => 'warning',
            'titulo' => 'Stock Bajo en Inventario',
            'mensaje' => 'El alimento balanceado tiene stock bajo (15 kg disponible, mínimo 50 kg)',
            'datos' => [
                'item_nombre' => 'Alimento Balanceado Premium',
                'stock_actual' => 15,
                'stock_minimo' => 50,
                'tipo_alerta' => 'stock_bajo'
            ],
            'icono' => 'package-x',
            'url' => route('produccion.inventario.alertas.index'),
        ]);

        // Anomalía de producción
        Notificacion::create([
            'tipo' => 'error',
            'titulo' => 'Anomalía de Producción Detectada',
            'mensaje' => 'El lote P-001 presenta bajo rendimiento (alta). Déficit: 32.5%',
            'datos' => [
                'codigo_lote' => 'P-001',
                'deficit_pct' => 32.5,
                'severidad' => 'alta'
            ],
            'icono' => 'alert-triangle',
            'url' => route('produccion.alertas.index'),
        ]);

        // Producto por vencer
        Notificacion::create([
            'tipo' => 'warning',
            'titulo' => 'Producto por Vencer',
            'mensaje' => 'El lote L-2024-08 de vitaminas vence en 3 días',
            'datos' => [
                'item_nombre' => 'Vitaminas para Peces',
                'lote' => 'L-2024-08',
                'dias_restantes' => 3,
                'tipo_alerta' => 'por_vencer'
            ],
            'icono' => 'calendar-clock',
            'url' => route('produccion.inventario.alertas.index'),
        ]);

        // Información general
        Notificacion::create([
            'tipo' => 'info',
            'titulo' => 'Mantenimiento Programado',
            'mensaje' => 'Se ha programado mantenimiento para las unidades de producción este fin de semana.',
            'datos' => [
                'fecha_programada' => '2025-08-31',
                'tipo' => 'mantenimiento_preventivo'
            ],
            'icono' => 'wrench',
            'url' => null,
            'fecha_vencimiento' => now()->addDays(2)
        ]);

        $this->info('✅ Se crearon 5 notificaciones de prueba');
        $this->info('👀 Revisa el icono de campana en el navbar para verlas');
        
        return Command::SUCCESS;
    }
}
