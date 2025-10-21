<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\CosechaParcial;
use App\Models\UnidadProduccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GeneradorDatosController extends Controller
{
    /**
     * Mostrar formulario para generar datos de prueba
     */
    public function index()
    {
        $lotes = Lote::with('unidadProduccion')->get();
        
        // Obtener estadísticas
        $stats = [
            'cosechas' => CosechaParcial::where('observaciones', 'like', '%[DATOS_PRUEBA]%')->count(),
            'ventas' => 0, // TODO: agregar conteo de ventas de prueba
            'lotes_con_datos' => Lote::whereHas('cosechasParciales', function($q) {
                $q->where('observaciones', 'like', '%[DATOS_PRUEBA]%');
            })->count(),
            'total_kg' => CosechaParcial::where('observaciones', 'like', '%[DATOS_PRUEBA]%')->sum('peso_cosechado_kg') ?? 0
        ];
        
        return view('admin.generador-datos', compact('lotes', 'stats'));
    }

    /**
     * Generar cosechas y ventas aleatorias
     */
    public function generarCosechas(Request $request)
    {
        $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'num_cosechas' => 'required|integer|min:1|max:20',
            'periodo_dias' => 'required|integer|min:30|max:365'
        ]);

        try {
            DB::beginTransaction();

            $lote = Lote::findOrFail($request->lote_id);
            $numCosechas = $request->num_cosechas;
            $periodoDias = $request->periodo_dias;

            $cosechasGeneradas = [];
            $ventasGeneradas = 0;

            for ($i = 0; $i < $numCosechas; $i++) {
                $cosecha = $this->generarCosechaAleatoria($lote, true, $periodoDias);
                $cosechasGeneradas[] = $cosecha;
                
                if ($cosecha->destino === 'venta') {
                    $ventasGeneradas++;
                }
            }

            DB::commit();

            return back()->with('success', 
                "✅ Generadas {$numCosechas} cosechas para el lote {$lote->codigo_lote}. " .
                "🏪 {$ventasGeneradas} fueron configuradas como ventas con precios aleatorios."
            );

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al generar datos: ' . $e->getMessage());
        }
    }

    /**
     * Generar una cosecha aleatoria
     */
    private function generarCosechaAleatoria(Lote $lote, bool $incluirVentas = true, int $periodoDias = 30): CosechaParcial
    {
        // Rangos aleatorios según la especie
        $rangos = $this->obtenerRangosPorEspecie($lote->especie);
        
        // Cantidad cosechada aleatoria
        $cantidadCosechada = rand($rangos['cantidad_min'], $rangos['cantidad_max']);
        
        // Peso promedio por pez según especie
        $pesoPromedioPorPez = $this->obtenerPesoPromedioPorEspecie($lote->especie);
        $pesoTotalKg = ($cantidadCosechada * $pesoPromedioPorPez) + (rand(-20, 30) / 100); // ±30% variación
        $pesoTotalKg = max(0.1, round($pesoTotalKg, 2)); // Mínimo 0.1kg
        
        // Fecha aleatoria en el período especificado
        $fechaCosecha = Carbon::now()->subDays(rand(1, $periodoDias));
        
        // Determinar destino (70% ventas, 30% consumo/otros)
        $esVenta = $incluirVentas && (rand(1, 100) <= 70);
        $destino = $esVenta ? 'venta' : $this->obtenerDestinoAleatorio();
        
        $cosecha = new CosechaParcial([
            'lote_id' => $lote->id,
            'fecha' => $fechaCosecha,
            'cantidad_cosechada' => $cantidadCosechada,
            'peso_cosechado_kg' => $pesoTotalKg,
            'destino' => $destino,
            'responsable' => $this->obtenerResponsableAleatorio(),
            'observaciones' => $this->generarObservacionAleatoria($lote->especie, $cantidadCosechada),
            'user_id' => Auth::check() ? Auth::user()->id : 1
        ]);

        // Si es venta, agregar datos de venta
        if ($esVenta) {
            $precioKg = $this->obtenerPrecioAleatorio($lote->especie);
            $totalVenta = round($pesoTotalKg * $precioKg, 2);
            
            $cosecha->fill([
                'codigo_venta' => 'V-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'cliente' => $this->obtenerClienteAleatorio(),
                'telefono_cliente' => $this->generarTelefonoAleatorio(),
                'fecha_venta' => $fechaCosecha,
                'precio_kg' => $precioKg,
                'total_venta' => $totalVenta,
                'metodo_pago' => $this->obtenerMetodoPagoAleatorio(),
                'estado_venta' => 'completada'
            ]);
        }

        $cosecha->save();
        return $cosecha;
    }

    /**
     * Obtener rangos por especie
     */
    private function obtenerRangosPorEspecie(string $especie): array
    {
        $especies = [
            'Tilapia Nilótica' => ['cantidad_min' => 50, 'cantidad_max' => 300, 'peso_promedio' => 0.8],
            'Trucha Arcoíris' => ['cantidad_min' => 30, 'cantidad_max' => 200, 'peso_promedio' => 1.2],
            'Carpa' => ['cantidad_min' => 40, 'cantidad_max' => 250, 'peso_promedio' => 1.5],
            'Bagre' => ['cantidad_min' => 25, 'cantidad_max' => 150, 'peso_promedio' => 2.0]
        ];

        // Buscar por coincidencia parcial
        foreach ($especies as $key => $valores) {
            if (stripos($especie, explode(' ', $key)[0]) !== false) {
                return $valores;
            }
        }

        // Default para especies no reconocidas
        return ['cantidad_min' => 30, 'cantidad_max' => 200, 'peso_promedio' => 1.0];
    }

    /**
     * Obtener peso promedio por especie
     */
    private function obtenerPesoPromedioPorEspecie(string $especie): float
    {
        $rangos = $this->obtenerRangosPorEspecie($especie);
        return $rangos['peso_promedio'];
    }

    /**
     * Obtener precio aleatorio por especie
     */
    private function obtenerPrecioAleatorio(string $especie): float
    {
        $precios = [
            'Tilapia Nilótica' => ['min' => 12.0, 'max' => 18.0],
            'Trucha Arcoíris' => ['min' => 25.0, 'max' => 35.0],
            'Carpa' => ['min' => 10.0, 'max' => 16.0],
            'Bagre' => ['min' => 20.0, 'max' => 30.0]
        ];

        foreach ($precios as $key => $rango) {
            if (stripos($especie, explode(' ', $key)[0]) !== false) {
                return round(rand($rango['min'] * 100, $rango['max'] * 100) / 100, 2);
            }
        }

        // Default
        return round(rand(1200, 2000) / 100, 2);
    }

    /**
     * Obtener destino aleatorio (no venta)
     */
    private function obtenerDestinoAleatorio(): string
    {
        // Solo usar valores permitidos en el ENUM de la BD: ['venta', 'consumo', 'muestra', 'otro']
        $destinos = ['consumo', 'muestra', 'otro'];
        return $destinos[array_rand($destinos)];
    }

    /**
     * Obtener responsable aleatorio
     */
    private function obtenerResponsableAleatorio(): string
    {
        $responsables = [
            'Juan Pérez', 'María González', 'Carlos López', 'Ana Martínez',
            'Roberto Silva', 'Carmen Rodríguez', 'Luis Morales', 'Sofia Herrera'
        ];
        return $responsables[array_rand($responsables)];
    }

    /**
     * Obtener cliente aleatorio
     */
    private function obtenerClienteAleatorio(): string
    {
        $clientes = [
            'Restaurante El Buen Sabor', 'Pescadería Central', 'Hotel Plaza',
            'Supermercado La Económica', 'Restaurante Mariscos del Mar',
            'Distribuidora Alimentaria', 'Restaurant Los Compadres',
            'Mercado Municipal', 'Cliente Particular'
        ];
        return $clientes[array_rand($clientes)];
    }

    /**
     * Generar teléfono aleatorio
     */
    private function generarTelefonoAleatorio(): string
    {
        return sprintf('%d-%d', rand(3000, 5999), rand(1000, 9999));
    }

    /**
     * Obtener método de pago aleatorio
     */
    private function obtenerMetodoPagoAleatorio(): string
    {
        $metodos = ['efectivo', 'transferencia', 'cheque', 'tarjeta'];
        return $metodos[array_rand($metodos)];
    }

    /**
     * Generar observación aleatoria
     */
    private function generarObservacionAleatoria(string $especie, int $cantidad): string
    {
        $observaciones = [
            "Cosecha exitosa de {$cantidad} ejemplares de {$especie}. Excelente calidad y tamaño comercial.",
            "Peces en óptimas condiciones. Cosecha matutina con buena manipulación.",
            "Ejemplares de {$especie} con peso promedio superior al esperado.",
            "Cosecha selectiva de los mejores ejemplares. Calidad premium.",
            "Proceso de cosecha sin inconvenientes. Peces activos y saludables."
        ];
        return $observaciones[array_rand($observaciones)];
    }

    /**
     * Limpiar datos de prueba
     */
    public function limpiarDatos(Request $request)
    {
        $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'confirmar' => 'required|accepted'
        ]);

        try {
            $lote = Lote::findOrFail($request->lote_id);
            $eliminadas = CosechaParcial::where('lote_id', $lote->id)->delete();

            return back()->with('success', 
                "🗑️ Eliminadas {$eliminadas} cosechas del lote {$lote->codigo_lote}"
            );

        } catch (\Exception $e) {
            return back()->with('error', 'Error al limpiar datos: ' . $e->getMessage());
        }
    }

    /**
     * Generar ventas de prueba
     */
    public function generarVentas(Request $request)
    {
        $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'num_ventas' => 'required|integer|min:1|max:10',
            'periodo_dias' => 'required|integer|min:15|max:180'
        ]);

        try {
            DB::beginTransaction();

            $lote = Lote::findOrFail($request->lote_id);
            $numVentas = $request->num_ventas;
            $periodoDias = $request->periodo_dias;

            $ventasGeneradas = 0;

            for ($i = 0; $i < $numVentas; $i++) {
                // Generar cosecha con destino venta
                $cosecha = $this->generarCosechaAleatoria($lote, true, $periodoDias);
                
                if ($cosecha->destino === 'venta') {
                    $ventasGeneradas++;
                }
            }

            DB::commit();

            return back()->with('success', 
                "🏪 Generadas {$ventasGeneradas} ventas para el lote {$lote->codigo_lote}"
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al generar ventas: ' . $e->getMessage());
        }
    }

    /**
     * Limpiar cosechas de prueba
     */
    public function limpiarCosechas(Request $request)
    {
        try {
            $eliminadas = CosechaParcial::where('observaciones', 'like', '%[DATOS_PRUEBA]%')->delete();

            return back()->with('success', 
                "🗑️ Eliminadas {$eliminadas} cosechas de prueba"
            );

        } catch (\Exception $e) {
            return back()->with('error', 'Error al limpiar cosechas: ' . $e->getMessage());
        }
    }

    /**
     * Limpiar ventas de prueba
     */
    public function limpiarVentas(Request $request)
    {
        try {
            // Eliminar cosechas que fueron generadas como ventas
            $eliminadas = CosechaParcial::where('observaciones', 'like', '%[DATOS_PRUEBA]%')
                                     ->where('destino', 'venta')
                                     ->delete();

            return back()->with('success', 
                "🗑️ Eliminadas {$eliminadas} ventas de prueba"
            );

        } catch (\Exception $e) {
            return back()->with('error', 'Error al limpiar ventas: ' . $e->getMessage());
        }
    }
}