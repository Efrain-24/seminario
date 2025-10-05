<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProtocoloSanidad;

class ProtocoloSanidadSeeder extends Seeder
{
    public function run(): void
    {
        ProtocoloSanidad::create([
            'nombre' => 'Protocolo General de Limpieza',
            'descripcion' => 'Limpieza diaria de todas las áreas de producción con desinfectante aprobado.',
            'fecha_implementacion' => '2025-01-01',
            'responsable' => 'Jefe de Sanidad',
            'actividades' => [
                'Limpiar tanques de cultivo',
                'Desinfectar herramientas de trabajo',
                'Limpiar filtros de agua',
                'Verificar niveles de desinfectante',
                'Registrar actividades en bitácora'
            ]
        ]);

        ProtocoloSanidad::create([
            'nombre' => 'Bioseguridad para Visitantes',
            'descripcion' => 'Control de acceso y uso de indumentaria especial para visitantes.',
            'fecha_implementacion' => '2025-02-15',
            'responsable' => 'Supervisor de Bioseguridad',
            'actividades' => [
                'Verificar identificación del visitante',
                'Proporcionar equipo de protección',
                'Explicar normas de bioseguridad',
                'Acompañar durante la visita',
                'Desinfectar equipo al final de la visita'
            ]
        ]);

        ProtocoloSanidad::create([
            'nombre' => 'Desinfección de Equipos',
            'descripcion' => 'Desinfección semanal de herramientas y equipos críticos.',
            'fecha_implementacion' => '2025-03-10',
            'responsable' => 'Encargado de Equipos',
            'actividades' => [
                'Preparar solución desinfectante',
                'Desmontar equipos removibles',
                'Aplicar desinfectante en todas las superficies',
                'Tiempo de contacto mínimo 15 minutos',
                'Enjuagar con agua limpia',
                'Secar y ensamblar equipos'
            ]
        ]);
    }
}
