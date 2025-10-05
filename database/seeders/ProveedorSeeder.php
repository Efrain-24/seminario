<?php<?php<?php<?php



namespace Database\Seeders;



use Illuminate\Database\Console\Seeds\WithoutModelEvents;namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Proveedor;

use Carbon\Carbon;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;namespace Database\Seeders;namespace Database\Seeders;

class ProveedorSeeder extends Seeder

{use Illuminate\Database\Seeder;

    /**

     * Run the database seeds.use App\Models\Proveedor;

     */

    public function run(): voiduse Carbon\Carbon;

    {

        $proveedores = [use Illuminate\Database\Seeder;use Illuminate\Database\Seeder;

            [

                'nombre' => 'Concentrados Nutricionales S.A.',class ProveedorSeeder extends Seeder

                'nit' => '12345678901',

                'tipo' => 'empresa',{use App\Models\Proveedor;use App\Models\Proveedor;

                'categoria' => 'alimentos',

                'estado' => 'activo',    /**

                'telefono_principal' => '+502 2234-5678',

                'telefono_secundario' => '+502 5555-1234',     * Run the database seeds.use Illuminate\Support\Facades\DB;use Illuminate\Support\Facades\DB;

                'email' => 'ventas@concentrados.com.gt',

                'sitio_web' => 'https://www.concentrados.com.gt',     */

                'direccion' => '15 Avenida 8-45, Zona 11',

                'departamento' => 'Guatemala',    public function run(): void

                'municipio' => 'Guatemala',

                'zona' => '11',    {

                'codigo_postal' => '01011',

                'limite_credito' => 150000.00,        $proveedores = [class ProveedorSeeder extends Seederclass ProveedorSeeder extends Seeder

                'dias_credito' => 30,

                'forma_pago_preferida' => 'credito',            [

                'moneda_preferida' => 'GTQ',

                'calificacion' => 4.5,                'nombre' => 'Concentrados Nutricionales S.A.',{{

                'total_evaluaciones' => 15,

                'tiempo_entrega_promedio' => 3.2,                'nit' => '12345678901',

                'porcentaje_cumplimiento' => 94.5,

                'saldo_actual' => 25000.00,                'tipo' => 'empresa',    /**    /**

                'total_compras_mes' => 45000.00,

                'total_compras_historico' => 450000.00,                'categoria' => 'alimentos',

                'fecha_ultima_compra' => Carbon::now()->subDays(7),

                'fecha_ultimo_pago' => Carbon::now()->subDays(15),                'estado' => 'activo',     * Run the database seeder.     * Run the database seeder.

                'contacto_comercial_nombre' => 'Maria Rodriguez',

                'contacto_comercial_telefono' => '+502 5555-9876',                'telefono_principal' => '+502 2234-5678',

                'contacto_comercial_email' => 'maria.rodriguez@concentrados.com.gt',

                'contacto_comercial_cargo' => 'Gerente de Ventas',                'telefono_secundario' => '+502 5555-1234',     */     */

                'especialidades' => 'Concentrados premium para peces, pellets flotantes y hundibles',

                'condiciones_especiales' => 'Descuento 3% por pagos anticipados',                'email' => 'ventas@concentrados.com.gt',

                'requiere_orden_compra' => true,

                'acepta_devoluciones' => true,                'sitio_web' => 'https://www.concentrados.com.gt',    public function run(): void    public function run(): void

                'certificaciones' => json_encode(['ISO 9001', 'HACCP', 'GMP']),

                'notas' => 'Proveedor confiable con más de 15 años en el mercado',                'direccion' => '15 Avenida 8-45, Zona 11',

            ],

            [                'departamento' => 'Guatemala',    {    {

                'nombre' => 'Químicos y Suministros Acuícolas',

                'nit' => '98765432101',                'municipio' => 'Guatemala',

                'tipo' => 'empresa',

                'categoria' => 'insumos',                'zona' => '11',        // Limpiar tabla antes de agregar datos        // Limpiar tabla antes de agregar datos

                'estado' => 'activo',

                'telefono_principal' => '+502 2456-7890',                'codigo_postal' => '01011',

                'email' => 'info@quimicos-acuicolas.com',

                'direccion' => '8 Calle 12-30, Zona 9',                'limite_credito' => 150000.00,        DB::table('proveedores')->delete();        DB::table('proveedores')->delete();

                'departamento' => 'Guatemala',

                'municipio' => 'Guatemala',                'dias_credito' => 30,

                'zona' => '9',

                'limite_credito' => 75000.00,                'forma_pago_preferida' => 'credito',

                'dias_credito' => 15,

                'forma_pago_preferida' => 'transferencia',                'moneda_preferida' => 'GTQ',

                'moneda_preferida' => 'GTQ',

                'calificacion' => 4.2,                'calificacion' => 4.5,        $proveedores = [        $proveedores = [

                'total_evaluaciones' => 8,

                'tiempo_entrega_promedio' => 2.5,                'total_evaluaciones' => 15,

                'porcentaje_cumplimiento' => 88.0,

                'saldo_actual' => 12500.00,                'tiempo_entrega_promedio' => 3.2,            [            [

                'total_compras_mes' => 22000.00,

                'total_compras_historico' => 180000.00,                'porcentaje_cumplimiento' => 94.5,

                'fecha_ultima_compra' => Carbon::now()->subDays(3),

                'contacto_comercial_nombre' => 'Carlos Mendoza',                'saldo_actual' => 25000.00,                'nombre' => 'Concentrados Nutricionales S.A.',                'nombre' => 'Concentrados Nutricionales S.A.',

                'contacto_comercial_telefono' => '+502 5678-9012',

                'contacto_comercial_email' => 'carlos.mendoza@quimicos-acuicolas.com',                'total_compras_mes' => 45000.00,

                'contacto_comercial_cargo' => 'Representante de Ventas',

                'especialidades' => 'Productos químicos para tratamiento de agua',                'total_compras_historico' => 450000.00,                'nit' => '12345678901',                'nit' => '12345678901',

                'condiciones_especiales' => 'Envío express disponible',

                'requiere_orden_compra' => false,                'fecha_ultima_compra' => Carbon::now()->subDays(7),

                'acepta_devoluciones' => true,

                'certificaciones' => json_encode(['ISO 14001']),                'fecha_ultimo_pago' => Carbon::now()->subDays(15),                'tipo' => 'empresa',                'tipo' => 'empresa',

            ],

            [                'contacto_comercial_nombre' => 'Maria Rodriguez',

                'nombre' => 'Equipos Industriales del Pacífico',

                'nit' => '55443322101',                'contacto_comercial_telefono' => '+502 5555-9876',                'categoria' => 'alimentos',                'categoria' => 'alimentos',

                'tipo' => 'empresa',

                'categoria' => 'equipos',                'contacto_comercial_email' => 'maria.rodriguez@concentrados.com.gt',

                'estado' => 'activo',

                'telefono_principal' => '+502 2789-0123',                'contacto_comercial_cargo' => 'Gerente de Ventas',                'estado' => 'activo',                'estado' => 'activo',

                'telefono_secundario' => '+502 4456-7890',

                'email' => 'ventas@eip.com.gt',                'especialidades' => 'Concentrados premium para peces, pellets flotantes y hundibles, suplementos nutricionales especializados.',

                'sitio_web' => 'https://www.eip.com.gt',

                'direccion' => 'Km 25 Carretera a El Salvador',                'condiciones_especiales' => 'Descuento del 3% por pagos anticipados. Entrega gratuita en pedidos mayores a Q25,000.',                'telefono_principal' => '+502 2234-5678',                'telefono_principal' => '+502 2234-5678',

                'departamento' => 'Guatemala',

                'municipio' => 'Villa Nueva',                'requiere_orden_compra' => true,

                'limite_credito' => 200000.00,

                'dias_credito' => 45,                'acepta_devoluciones' => true,                'telefono_secundario' => '+502 5555-1234',                'telefono_secundario' => '+502 5555-1234',

                'forma_pago_preferida' => 'cheque',

                'moneda_preferida' => 'GTQ',                'certificaciones' => json_encode(['ISO 9001', 'HACCP', 'GMP']),

                'calificacion' => 4.8,

                'total_evaluaciones' => 12,                'notas' => 'Proveedor confiable con más de 15 años en el mercado. Excelente calidad de productos.',                'email' => 'ventas@concentrados.com.gt',                'email' => 'ventas@concentrados.com.gt',

                'tiempo_entrega_promedio' => 7.5,

                'porcentaje_cumplimiento' => 96.8,            ],

                'saldo_actual' => 0.00,

                'total_compras_mes' => 65000.00,            [                'sitio_web' => 'https://www.concentrados.com.gt',                'sitio_web' => 'https://www.concentrados.com.gt',

                'total_compras_historico' => 850000.00,

                'fecha_ultima_compra' => Carbon::now()->subDays(20),                'nombre' => 'Químicos y Suministros Acuícolas',

                'fecha_ultimo_pago' => Carbon::now()->subDays(10),

                'contacto_comercial_nombre' => 'Roberto Silva',                'nit' => '98765432101',                'direccion' => '15 Avenida 8-45, Zona 11',                'direccion' => '15 Avenida 8-45, Zona 11',

                'contacto_comercial_telefono' => '+502 3456-7890',

                'contacto_comercial_email' => 'roberto.silva@eip.com.gt',                'tipo' => 'empresa',

                'contacto_comercial_cargo' => 'Gerente Comercial',

                'especialidades' => 'Bombas de agua, sistemas de aireación, filtros',                'categoria' => 'insumos',                'departamento' => 'Guatemala',                'departamento' => 'Guatemala',

                'condiciones_especiales' => 'Garantía extendida de 2 años',

                'requiere_orden_compra' => true,                'estado' => 'activo',

                'acepta_devoluciones' => false,

                'certificaciones' => json_encode(['ISO 9001', 'CE', 'UL']),                'telefono_principal' => '+502 2456-7890',                'municipio' => 'Guatemala',                'municipio' => 'Guatemala',

                'notas' => 'Excelente servicio post-venta',

            ],                'email' => 'info@quimicos-acuicolas.com',

        ];

                'direccion' => '8 Calle 12-30, Zona 9',                'zona' => '11',                'zona' => '11',

        foreach ($proveedores as $proveedor) {

            Proveedor::create($proveedor);                'departamento' => 'Guatemala',

        }

                'municipio' => 'Guatemala',                'codigo_postal' => '01011',                'codigo_postal' => '01011',

        $this->command->info('Se crearon ' . count($proveedores) . ' proveedores de prueba exitosamente.');

    }                'zona' => '9',

}
                'limite_credito' => 75000.00,                'limite_credito' => 150000.00,                'limite_credito' => 150000.00,

                'dias_credito' => 15,

                'forma_pago_preferida' => 'transferencia',                'dias_credito' => 30,                'dias_credito' => 30,

                'moneda_preferida' => 'GTQ',

                'calificacion' => 4.2,                'forma_pago_preferida' => 'credito',                'forma_pago_preferida' => 'credito',

                'total_evaluaciones' => 8,

                'tiempo_entrega_promedio' => 2.5,                'moneda_preferida' => 'GTQ',                'moneda_preferida' => 'GTQ',

                'porcentaje_cumplimiento' => 88.0,

                'saldo_actual' => 12500.00,                'calificacion' => 4.5,                'calificacion' => 4.5,

                'total_compras_mes' => 22000.00,

                'total_compras_historico' => 180000.00,                'total_evaluaciones' => 15,                'total_evaluaciones' => 15,

                'fecha_ultima_compra' => Carbon::now()->subDays(3),

                'contacto_comercial_nombre' => 'Carlos Mendoza',                'tiempo_entrega_promedio' => 3.2,                'tiempo_entrega_promedio' => 3.2,

                'contacto_comercial_telefono' => '+502 5678-9012',

                'contacto_comercial_email' => 'carlos.mendoza@quimicos-acuicolas.com',                'porcentaje_cumplimiento' => 94.5,                'porcentaje_cumplimiento' => 94.5,

                'contacto_comercial_cargo' => 'Representante de Ventas',

                'especialidades' => 'Productos químicos para tratamiento de agua, desinfectantes, probióticos, aditivos nutricionales.',                'saldo_actual' => 25000.00,                'saldo_actual' => 25000.00,

                'condiciones_especiales' => 'Envío express disponible con costo adicional.',

                'requiere_orden_compra' => false,                'total_compras_mes' => 45000.00,                'total_compras_mes' => 45000.00,

                'acepta_devoluciones' => true,

                'certificaciones' => json_encode(['ISO 14001']),                'total_compras_historico' => 450000.00,                'total_compras_historico' => 450000.00,

            ],

            [                'fecha_ultima_compra' => now()->subDays(7),                'fecha_ultima_compra' => now()->subDays(7),

                'nombre' => 'Equipos Industriales del Pacífico',

                'nit' => '55443322101',                'fecha_ultimo_pago' => now()->subDays(15),                'fecha_ultimo_pago' => now()->subDays(15),

                'tipo' => 'empresa',

                'categoria' => 'equipos',                'contacto_comercial_nombre' => 'Maria Rodriguez',                'contacto_comercial_nombre' => 'Maria Rodriguez',

                'estado' => 'activo',

                'telefono_principal' => '+502 2789-0123',                'contacto_comercial_telefono' => '+502 5555-9876',                'contacto_comercial_telefono' => '+502 5555-9876',

                'telefono_secundario' => '+502 4456-7890',

                'email' => 'ventas@eip.com.gt',                'contacto_comercial_email' => 'maria.rodriguez@concentrados.com.gt',                'contacto_comercial_email' => 'maria.rodriguez@concentrados.com.gt',

                'sitio_web' => 'https://www.eip.com.gt',

                'direccion' => 'Km 25 Carretera a El Salvador',                'contacto_comercial_cargo' => 'Gerente de Ventas',                'contacto_comercial_cargo' => 'Gerente de Ventas',

                'departamento' => 'Guatemala',

                'municipio' => 'Villa Nueva',                'especialidades' => 'Concentrados premium para peces, pellets flotantes y hundibles, suplementos nutricionales especializados para diferentes etapas de crecimiento.',                'especialidades' => 'Concentrados premium para peces, pellets flotantes y hundibles, suplementos nutricionales especializados para diferentes etapas de crecimiento.',

                'limite_credito' => 200000.00,

                'dias_credito' => 45,                'condiciones_especiales' => 'Descuento del 3% por pagos anticipados. Entrega gratuita en pedidos mayores a Q25,000.',                'condiciones_especiales' => 'Descuento del 3% por pagos anticipados. Entrega gratuita en pedidos mayores a Q25,000.',

                'forma_pago_preferida' => 'cheque',

                'moneda_preferida' => 'GTQ',                'requiere_orden_compra' => true,                'requiere_orden_compra' => true,

                'calificacion' => 4.8,

                'total_evaluaciones' => 12,                'acepta_devoluciones' => true,                'acepta_devoluciones' => true,

                'tiempo_entrega_promedio' => 7.5,

                'porcentaje_cumplimiento' => 96.8,                'certificaciones' => ['ISO 9001', 'HACCP', 'GMP'],                'certificaciones' => ['ISO 9001', 'HACCP', 'GMP'],

                'saldo_actual' => 0.00,

                'total_compras_mes' => 65000.00,                'notas' => 'Proveedor confiable con más de 15 años en el mercado. Excelente calidad de productos.',                'notas' => 'Proveedor confiable con más de 15 años en el mercado. Excelente calidad de productos.',

                'total_compras_historico' => 850000.00,

                'fecha_ultima_compra' => Carbon::now()->subDays(20),            ],            ],

                'fecha_ultimo_pago' => Carbon::now()->subDays(10),

                'contacto_comercial_nombre' => 'Roberto Silva',            [            [

                'contacto_comercial_telefono' => '+502 3456-7890',

                'contacto_comercial_email' => 'roberto.silva@eip.com.gt',                'nombre' => 'Químicos y Suministros Acuícolas',                'nombre' => 'Químicos y Suministros Acuícolas',

                'contacto_comercial_cargo' => 'Gerente Comercial',

                'especialidades' => 'Bombas de agua, sistemas de aireación, filtros, equipos de medición de calidad de agua.',                'nit' => '98765432101',                'nit' => '98765432101',

                'condiciones_especiales' => 'Garantía extendida de 2 años. Servicio técnico especializado incluido.',

                'requiere_orden_compra' => true,                'tipo' => 'empresa',                'tipo' => 'empresa',

                'acepta_devoluciones' => false,

                'certificaciones' => json_encode(['ISO 9001', 'CE', 'UL']),                'categoria' => 'insumos',                'categoria' => 'insumos',

                'notas' => 'Excelente servicio post-venta. Equipos de alta calidad y durabilidad.',

            ],                'estado' => 'activo',                'estado' => 'activo',

        ];

                'telefono_principal' => '+502 2456-7890',                'telefono_principal' => '+502 2456-7890',

        foreach ($proveedores as $proveedor) {

            Proveedor::create($proveedor);                'email' => 'info@quimicos-acuicolas.com',                'email' => 'info@quimicos-acuicolas.com',

        }

                'direccion' => '8 Calle 12-30, Zona 9',                'direccion' => '8 Calle 12-30, Zona 9',

        $this->command->info('Se crearon ' . count($proveedores) . ' proveedores de prueba exitosamente.');

    }                'departamento' => 'Guatemala',                'departamento' => 'Guatemala',

}
                'municipio' => 'Guatemala',                'municipio' => 'Guatemala',

                'zona' => '9',                'zona' => '9',

                'limite_credito' => 75000.00,                'limite_credito' => 75000.00,

                'dias_credito' => 15,                'dias_credito' => 15,

                'forma_pago_preferida' => 'transferencia',                'forma_pago_preferida' => 'transferencia',

                'moneda_preferida' => 'GTQ',                'moneda_preferida' => 'GTQ',

                'calificacion' => 4.2,                'calificacion' => 4.2,

                'total_evaluaciones' => 8,                'total_evaluaciones' => 8,

                'tiempo_entrega_promedio' => 2.5,                'tiempo_entrega_promedio' => 2.5,

                'porcentaje_cumplimiento' => 88.0,                'porcentaje_cumplimiento' => 88.0,

                'saldo_actual' => 12500.00,                'saldo_actual' => 12500.00,

                'total_compras_mes' => 22000.00,                'total_compras_mes' => 22000.00,

                'total_compras_historico' => 180000.00,                'total_compras_historico' => 180000.00,

                'fecha_ultima_compra' => now()->subDays(3),                'fecha_ultima_compra' => now()->subDays(3),

                'contacto_comercial_nombre' => 'Carlos Mendoza',                'contacto_comercial_nombre' => 'Carlos Mendoza',

                'contacto_comercial_telefono' => '+502 5678-9012',                'contacto_comercial_telefono' => '+502 5678-9012',

                'contacto_comercial_email' => 'carlos.mendoza@quimicos-acuicolas.com',                'contacto_comercial_email' => 'carlos.mendoza@quimicos-acuicolas.com',

                'contacto_comercial_cargo' => 'Representante de Ventas',                'contacto_comercial_cargo' => 'Representante de Ventas',

                'especialidades' => 'Productos químicos para tratamiento de agua, desinfectantes, probióticos, aditivos nutricionales.',                'especialidades' => 'Productos químicos para tratamiento de agua, desinfectantes, probióticos, aditivos nutricionales.',

                'condiciones_especiales' => 'Envío express disponible con costo adicional.',                'condiciones_especiales' => 'Envío express disponible con costo adicional.',

                'requiere_orden_compra' => false,                'requiere_orden_compra' => false,

                'acepta_devoluciones' => true,                'acepta_devoluciones' => true,

                'certificaciones' => ['ISO 14001'],                'certificaciones' => ['ISO 14001'],

            ],            ],

            [            [

                'nombre' => 'Equipos Industriales del Pacífico',                'nombre' => 'Equipos Industriales del Pacífico',

                'nit' => '55443322101',                'nit' => '55443322101',

                'tipo' => 'empresa',                'tipo' => 'empresa',

                'categoria' => 'equipos',                'categoria' => 'equipos',

                'estado' => 'activo',                'estado' => 'activo',

                'telefono_principal' => '+502 2789-0123',                'telefono_principal' => '+502 2789-0123',

                'telefono_secundario' => '+502 4456-7890',                'telefono_secundario' => '+502 4456-7890',

                'email' => 'ventas@eip.com.gt',                'email' => 'ventas@eip.com.gt',

                'sitio_web' => 'https://www.eip.com.gt',                'sitio_web' => 'https://www.eip.com.gt',

                'direccion' => 'Km 25 Carretera a El Salvador',                'direccion' => 'Km 25 Carretera a El Salvador',

                'departamento' => 'Guatemala',                'departamento' => 'Guatemala',

                'municipio' => 'Villa Nueva',                'municipio' => 'Villa Nueva',

                'limite_credito' => 200000.00,                'limite_credito' => 200000.00,

                'dias_credito' => 45,                'dias_credito' => 45,

                'forma_pago_preferida' => 'cheque',                'forma_pago_preferida' => 'cheque',

                'moneda_preferida' => 'GTQ',                'moneda_preferida' => 'GTQ',

                'calificacion' => 4.8,                'calificacion' => 4.8,

                'total_evaluaciones' => 12,                'total_evaluaciones' => 12,

                'tiempo_entrega_promedio' => 7.5,                'tiempo_entrega_promedio' => 7.5,

                'porcentaje_cumplimiento' => 96.8,                'porcentaje_cumplimiento' => 96.8,

                'saldo_actual' => 0.00,                'saldo_actual' => 0.00,

                'total_compras_mes' => 65000.00,                'total_compras_mes' => 65000.00,

                'total_compras_historico' => 850000.00,                'total_compras_historico' => 850000.00,

                'fecha_ultima_compra' => now()->subDays(20),                'fecha_ultima_compra' => now()->subDays(20),

                'fecha_ultimo_pago' => now()->subDays(10),                'fecha_ultimo_pago' => now()->subDays(10),

                'contacto_comercial_nombre' => 'Roberto Silva',                'contacto_comercial_nombre' => 'Roberto Silva',

                'contacto_comercial_telefono' => '+502 3456-7890',                'contacto_comercial_telefono' => '+502 3456-7890',

                'contacto_comercial_email' => 'roberto.silva@eip.com.gt',                'contacto_comercial_email' => 'roberto.silva@eip.com.gt',

                'contacto_comercial_cargo' => 'Gerente Comercial',                'contacto_comercial_cargo' => 'Gerente Comercial',

                'especialidades' => 'Bombas de agua, sistemas de aireación, filtros, equipos de medición de calidad de agua, generadores eléctricos.',                'especialidades' => 'Bombas de agua, sistemas de aireación, filtros, equipos de medición de calidad de agua, generadores eléctricos.',

                'condiciones_especiales' => 'Garantía extendida de 2 años. Servicio técnico especializado incluido.',                'condiciones_especiales' => 'Garantía extendida de 2 años. Servicio técnico especializado incluido.',

                'requiere_orden_compra' => true,                'requiere_orden_compra' => true,

                'acepta_devoluciones' => false,                'acepta_devoluciones' => false,

                'certificaciones' => ['ISO 9001', 'CE', 'UL'],                'certificaciones' => ['ISO 9001', 'CE', 'UL'],

                'notas' => 'Excelente servicio post-venta. Equipos de alta calidad y durabilidad.',                'notas' => 'Excelente servicio post-venta. Equipos de alta calidad y durabilidad.',

            ],            ],

        ];        ];



        foreach ($proveedores as $proveedor) {        foreach ($proveedores as $proveedor) {

            Proveedor::create($proveedor);            Proveedor::create($proveedor);

        }        }

    }    }

}}Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
    }
}
