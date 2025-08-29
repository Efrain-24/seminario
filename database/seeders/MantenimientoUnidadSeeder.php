<?php

namespace Database\Seeders;

use App\Models\MantenimientoUnidad;
use App\Models\UnidadProduccion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MantenimientoUnidadSeeder extends Seeder
{
    public function run()
    {
        // Verificar si ya existen datos
        $registrosExistentes = MantenimientoUnidad::count();
        if ($registrosExistentes > 0) {
            $this->command->info("✅ Ya existen {$registrosExistentes} registros de mantenimiento en la base de datos.");
            return;
        }

        $unidades = UnidadProduccion::all();
        $usuarios = User::all();

        if ($unidades->isEmpty() || $usuarios->isEmpty()) {
            $this->command->warn('No hay unidades de producción o usuarios disponibles. Saltando seeder de mantenimientos.');
            return;
        }

        $tipos = ['preventivo', 'correctivo', 'limpieza', 'reparacion', 'inspeccion', 'desinfeccion'];
        $prioridades = ['baja', 'media', 'alta', 'critica'];
        $estados = ['programado', 'en_proceso', 'completado', 'cancelado'];

        // Descripciones por tipo
        $descripciones = [
            'preventivo' => [
                'Mantenimiento preventivo mensual de sistemas de filtración',
                'Revisión y calibración de sensores de temperatura',
                'Lubricación de bombas y sistemas mecánicos',
                'Inspección de estructuras y soldaduras',
                'Verificación de sistemas de aireación'
            ],
            'correctivo' => [
                'Reparación de fuga en tubería principal',
                'Reemplazo de bomba dañada en sistema de circulación',
                'Corrección de niveles de pH fuera de rango',
                'Reparación de grietas en paredes del tanque',
                'Ajuste de sistema de control automático'
            ],
            'limpieza' => [
                'Limpieza profunda y desinfección general',
                'Eliminación de algas y sedimentos acumulados',
                'Desinfección de sistemas de alimentación',
                'Limpieza de filtros y sistemas de purificación',
                'Sanitización después de ciclo productivo'
            ],
            'reparacion' => [
                'Reparación de sistema de bombeo',
                'Arreglo de grietas en estructura',
                'Reemplazo de tuberías deterioradas',
                'Reparación de sistema de oxigenación',
                'Corrección de problemas en válvulas'
            ],
            'inspeccion' => [
                'Inspección general del estado de la unidad',
                'Evaluación de calidad del agua',
                'Revisión de sistemas de seguridad',
                'Auditoría de condiciones ambientales',
                'Verificación de protocolos de bioseguridad'
            ],
            'desinfeccion' => [
                'Desinfección completa de la unidad',
                'Eliminación de patógenos y bacterias',
                'Sanitización de equipos y herramientas',
                'Aplicación de productos desinfectantes',
                'Esterilización de sistemas de alimentación'
            ]
        ];

        // Crear mantenimientos para cada unidad
        foreach ($unidades as $unidad) {
            // 2-4 mantenimientos por unidad
            $cantidad = rand(2, 4);
            
            for ($i = 0; $i < $cantidad; $i++) {
                $tipo = $tipos[array_rand($tipos)];
                $descripcion = $descripciones[$tipo][array_rand($descripciones[$tipo])];
                
                // Fechas variadas: algunos pasados, algunos futuros
                $fechaBase = Carbon::now()->subDays(rand(-30, 60));
                
                $estado = $estados[array_rand($estados)];
                
                // Ajustar estado según fecha
                if ($fechaBase->isPast() && $estado === 'programado') {
                    $estado = ['en_proceso', 'completado'][array_rand(['en_proceso', 'completado'])];
                }
                
                $data = [
                    'unidad_produccion_id' => $unidad->id,
                    'user_id' => $usuarios->random()->id,
                    'tipo_mantenimiento' => $tipo,
                    'descripcion_trabajo' => $descripcion,
                    'fecha_mantenimiento' => $fechaBase->format('Y-m-d'),
                    'prioridad' => $prioridades[array_rand($prioridades)],
                    'estado_mantenimiento' => $estado,
                    'observaciones_antes' => rand(0, 1) ? 'Observaciones previas al mantenimiento.' : null,
                    'requiere_vaciado' => rand(0, 1),
                    'requiere_traslado_peces' => rand(0, 1),
                    'created_at' => $fechaBase->copy()->subDays(rand(1, 7)),
                    'updated_at' => ($estado !== 'programado') ? $fechaBase->copy()->addHours(rand(1, 48)) : $fechaBase->copy()->subDays(rand(1, 7))
                ];

                // Datos adicionales para mantenimientos completados
                if ($estado === 'completado') {
                    $horaInicio = rand(6, 12);
                    $horaFin = $horaInicio + rand(2, 8);
                    
                    $data['hora_inicio'] = sprintf('%02d:00:00', $horaInicio);
                    $data['hora_fin'] = sprintf('%02d:00:00', min($horaFin, 23));
                    $data['costo_mantenimiento'] = rand(50000, 500000);
                    $data['observaciones_despues'] = 'Mantenimiento completado satisfactoriamente.';
                    $data['materiales_utilizados'] = 'Materiales y herramientas utilizadas en el mantenimiento.';
                    $data['proxima_revision'] = $fechaBase->copy()->addDays(rand(30, 90))->format('Y-m-d');
                } elseif ($estado === 'en_proceso') {
                    $horaInicio = rand(6, 18);
                    $data['hora_inicio'] = sprintf('%02d:00:00', $horaInicio);
                }

                // Verificar si ya existe un mantenimiento similar
                $existente = MantenimientoUnidad::where([
                    'unidad_produccion_id' => $data['unidad_produccion_id'],
                    'fecha_mantenimiento' => $data['fecha_mantenimiento'],
                    'tipo_mantenimiento' => $data['tipo_mantenimiento']
                ])->first();

                if (!$existente) {
                    $mantenimiento = MantenimientoUnidad::create($data);
                    $this->command->info("Mantenimiento creado: {$mantenimiento->tipo_mantenimiento} - {$unidad->nombre}");
                }
            }
        }

        // Crear algunos mantenimientos urgentes/próximos
        for ($i = 0; $i < 3; $i++) {
            $unidad = $unidades->random();
            $tipo = ['correctivo', 'inspeccion'][array_rand(['correctivo', 'inspeccion'])];
            
            // Verificar si ya existe
            $fecha = Carbon::now()->addDays(rand(1, 7))->format('Y-m-d');
            $existente = MantenimientoUnidad::where([
                'unidad_produccion_id' => $unidad->id,
                'fecha_mantenimiento' => $fecha,
                'tipo_mantenimiento' => $tipo
            ])->first();

            if (!$existente) {
                MantenimientoUnidad::create([
                    'unidad_produccion_id' => $unidad->id,
                    'user_id' => $usuarios->random()->id,
                    'tipo_mantenimiento' => $tipo,
                    'descripcion_trabajo' => $descripciones[$tipo][array_rand($descripciones[$tipo])],
                    'fecha_mantenimiento' => $fecha,
                    'prioridad' => ['alta', 'critica'][array_rand(['alta', 'critica'])],
                    'estado_mantenimiento' => 'programado',
                    'observaciones_antes' => 'Mantenimiento urgente programado.',
                ]);
            }
        }

        $totalMantenimientos = MantenimientoUnidad::count();
        $this->command->info("✅ Mantenimientos procesados: {$totalMantenimientos} total en la base de datos.");
    }
}
