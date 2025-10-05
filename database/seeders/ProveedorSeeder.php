<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Proveedor;
use Illuminate\Support\Facades\DB;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void

    {
        $proveedores = [
            [
                'nombre' => 'Concentrados Nutricionales S.A.',
                'nit' => '12345678901',
                'tipo' => 'empresa',
                'categoria' => 'alimentos',
                'estado' => 'activo',
                'telefono_principal' => '+502 2234-5678',
                'telefono_secundario' => '+502 5555-1234',
                'email' => 'ventas@concentrados.com.gt',
                'sitio_web' => 'https://www.concentrados.com.gt',
                'direccion' => '15 Avenida 8-45, Zona 11',
                'departamento' => 'Guatemala',
                'municipio' => 'Guatemala',
                'zona' => '11',
                'codigo_postal' => '01011',
                'limite_credito' => 150000.00,
                'dias_credito' => 30,
                'forma_pago_preferida' => 'credito',
                'moneda_preferida' => 'GTQ',
                'calificacion' => 4.5,
                'total_evaluaciones' => 15,
                'tiempo_entrega_promedio' => 3.2,
                'porcentaje_cumplimiento' => 94.5,
                'saldo_actual' => 25000.00,
                'total_compras_mes' => 45000.00,
                'total_compras_historico' => 450000.00,
                'fecha_ultima_compra' => now()->subDays(7),
                'fecha_ultimo_pago' => now()->subDays(15),
                'contacto_comercial_nombre' => 'Maria Rodriguez',
                'contacto_comercial_telefono' => '+502 5555-9876',
                'contacto_comercial_email' => 'maria.rodriguez@concentrados.com.gt',
                'contacto_comercial_cargo' => 'Gerente de Ventas',
                'especialidades' => 'Concentrados premium para peces, pellets flotantes y hundibles, suplementos nutricionales especializados para diferentes etapas de crecimiento.',
                'condiciones_especiales' => 'Descuento del 3% por pagos anticipados. Entrega gratuita en pedidos mayores a Q25,000.',
                'requiere_orden_compra' => true,
                'acepta_devoluciones' => true,
                'certificaciones' => ['ISO 9001', 'HACCP', 'GMP'],
                'notas' => 'Proveedor confiable con más de 15 años en el mercado. Excelente calidad de productos.',
            ],
            [
                'nombre' => 'Químicos y Suministros Acuícolas',
                'nit' => '98765432101',
                'tipo' => 'empresa',
                'categoria' => 'insumos',
                'estado' => 'activo',
                'telefono_principal' => '+502 2456-7890',
                'email' => 'info@quimicos-acuicolas.com',
                'direccion' => '8 Calle 12-30, Zona 9',
                'departamento' => 'Guatemala',
                'municipio' => 'Guatemala',
                'zona' => '9',
                'limite_credito' => 75000.00,
                'dias_credito' => 15,
                'forma_pago_preferida' => 'transferencia',
                'moneda_preferida' => 'GTQ',
                'calificacion' => 4.2,
                'total_evaluaciones' => 8,
                'tiempo_entrega_promedio' => 2.5,
                'porcentaje_cumplimiento' => 88.0,
                'saldo_actual' => 12500.00,
                'total_compras_mes' => 22000.00,
                'total_compras_historico' => 180000.00,
                'fecha_ultima_compra' => now()->subDays(3),
                'contacto_comercial_nombre' => 'Carlos Mendoza',
                'contacto_comercial_telefono' => '+502 5678-9012',
                'contacto_comercial_email' => 'carlos.mendoza@quimicos-acuicolas.com',
                'contacto_comercial_cargo' => 'Representante de Ventas',
                'especialidades' => 'Productos químicos para tratamiento de agua, desinfectantes, probióticos, aditivos nutricionales.',
                'condiciones_especiales' => 'Envío express disponible con costo adicional.',
                'requiere_orden_compra' => false,
                'acepta_devoluciones' => true,
                'certificaciones' => ['ISO 14001'],
            ],
            [
                'nombre' => 'Equipos Industriales del Pacífico',
                'nit' => '55443322101',
                'tipo' => 'empresa',
                'categoria' => 'equipos',
                'estado' => 'activo',
                'telefono_principal' => '+502 2789-0123',
                'telefono_secundario' => '+502 4456-7890',
                'email' => 'ventas@eip.com.gt',
                'sitio_web' => 'https://www.eip.com.gt',
                'direccion' => 'Km 25 Carretera a El Salvador',
                'departamento' => 'Guatemala',
                'municipio' => 'Villa Nueva',
                'limite_credito' => 200000.00,
                'dias_credito' => 45,
                'forma_pago_preferida' => 'cheque',
                'moneda_preferida' => 'GTQ',
                'calificacion' => 4.8,
                'total_evaluaciones' => 12,
                'tiempo_entrega_promedio' => 7.5,
                'porcentaje_cumplimiento' => 96.8,
                'saldo_actual' => 0.00,
                'total_compras_mes' => 65000.00,
                'total_compras_historico' => 850000.00,
                'fecha_ultima_compra' => now()->subDays(20),
                'fecha_ultimo_pago' => now()->subDays(10),
                'contacto_comercial_nombre' => 'Roberto Silva',
                'contacto_comercial_telefono' => '+502 3456-7890',
                'contacto_comercial_email' => 'roberto.silva@eip.com.gt',
                'contacto_comercial_cargo' => 'Gerente Comercial',
                'especialidades' => 'Bombas de agua, sistemas de aireación, filtros, equipos de medición de calidad de agua, generadores eléctricos.',
                'condiciones_especiales' => 'Garantía extendida de 2 años. Servicio técnico especializado incluido.',
                'requiere_orden_compra' => true,
                'acepta_devoluciones' => false,
                'certificaciones' => ['ISO 9001', 'CE', 'UL'],
                'notas' => 'Excelente servicio post-venta. Equipos de alta calidad y durabilidad.',
            ],
        ];

        foreach ($proveedores as $proveedorData) {
            // Solo crear si no existe un proveedor con el mismo nombre
            $existeProveedor = Proveedor::where('nombre', $proveedorData['nombre'])->first();
            
            if (!$existeProveedor) {
                Proveedor::create($proveedorData);
                echo "✓ Proveedor '{$proveedorData['nombre']}' creado exitosamente.\n";
            } else {
                echo "- Proveedor '{$proveedorData['nombre']}' ya existe, no se duplicó.\n";
            }
        }
    }
}
