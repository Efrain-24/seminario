<?php

namespace App\Http\Controllers;

use App\Services\ConsistenciaEstimacionService;
use App\Services\FiltrosTrazabilidadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PanelIndicadoresController extends Controller
{
    protected $consistenciaService;
    protected $filtrosService;
    
    public function __construct(
        ConsistenciaEstimacionService $consistenciaService,
        FiltrosTrazabilidadService $filtrosService
    ) {
        $this->consistenciaService = $consistenciaService;
        $this->filtrosService = $filtrosService;
    }

    /**
     * RF39: Panel de indicadores con confirmación al ocultar módulos
     */
    public function index(Request $request)
    {
        // Obtener filtros del request
        $filtros = $request->only([
            'especie', 'estado', 'fecha_inicio_desde', 'fecha_inicio_hasta',
            'cantidad_inicial_min', 'cantidad_inicial_max', 'biomasa_min', 'biomasa_max'
        ]);
        
        // Obtener datos de consistencia
        $consistencia = $this->consistenciaService->verificarConsistencia($filtros);
        
        // Obtener datos de trazabilidad con filtros avanzados
        $trazabilidad = $this->filtrosService->aplicarFiltrosAvanzados($filtros);
        
        // Preparar datos para el panel
        $panelData = $this->prepararDatosPanel($consistencia, $trazabilidad);
        
        return view('panel.indicadores.index', compact('panelData', 'consistencia', 'trazabilidad'));
    }

    /**
     * Mostrar panel consolidado con todos los indicadores
     */
    public function consolidado(Request $request)
    {
        $filtros = $request->only([
            'especie', 'estado', 'fecha_inicio_desde', 'fecha_inicio_hasta',
            'mostrar_costos', 'mostrar_ventas', 'mostrar_consistencia', 'mostrar_trazabilidad'
        ]);
        
        $modulos = [
            'costos' => $filtros['mostrar_costos'] ?? true,
            'ventas' => $filtros['mostrar_ventas'] ?? true,
            'consistencia' => $filtros['mostrar_consistencia'] ?? true,
            'trazabilidad' => $filtros['mostrar_trazabilidad'] ?? true
        ];
        
        $datos = [];
        
        // Cargar datos según módulos activos
        if ($modulos['costos']) {
            $costoService = app(\App\Services\CostoProduccionService::class);
            $lotes = \App\Models\Lote::all();
            $datos['costos'] = $costoService->generarReporteCostos($lotes);
        }
        
        if ($modulos['ventas']) {
            $ventasService = app(\App\Services\VentasResultadosService::class);
            $datos['ventas'] = $ventasService->obtenerResultadosVentas($filtros);
        }
        
        if ($modulos['consistencia']) {
            $datos['consistencia'] = $this->consistenciaService->verificarConsistencia($filtros);
        }
        
        if ($modulos['trazabilidad']) {
            $datos['trazabilidad'] = $this->filtrosService->aplicarFiltrosAvanzados($filtros);
        }
        
        return view('panel.indicadores.consolidado', compact('datos', 'modulos', 'filtros'));
    }

    /**
     * RF39: Confirmar ocultación de módulo con advertencia
     */
    public function confirmarOcultarModulo(Request $request)
    {
        $request->validate([
            'modulo' => 'required|in:costos,ventas,consistencia,trazabilidad',
            'accion' => 'required|in:ocultar,mostrar'
        ]);
        
        $modulo = $request->modulo;
        $accion = $request->accion;
        
        // Obtener información del módulo
        $infoModulo = $this->obtenerInfoModulo($modulo);
        
        if ($accion === 'ocultar') {
            // Generar advertencias específicas
            $advertencias = $this->generarAdvertenciasOcultacion($modulo);
            
            return response()->json([
                'success' => true,
                'requiere_confirmacion' => true,
                'modulo' => $infoModulo,
                'advertencias' => $advertencias,
                'mensaje_confirmacion' => $this->generarMensajeConfirmacion($modulo)
            ]);
        } else {
            // Mostrar módulo - sin confirmación necesaria
            return response()->json([
                'success' => true,
                'requiere_confirmacion' => false,
                'mensaje' => "Módulo {$infoModulo['nombre']} activado correctamente"
            ]);
        }
    }

    /**
     * Ejecutar ocultación del módulo tras confirmación
     */
    public function ejecutarOcultarModulo(Request $request)
    {
        $request->validate([
            'modulo' => 'required|in:costos,ventas,consistencia,trazabilidad',
            'confirmado' => 'required|boolean'
        ]);
        
        if (!$request->confirmado) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Acción cancelada por el usuario'
            ]);
        }
        
        $modulo = $request->modulo;
        
        // Guardar preferencia de usuario (en session o BD)
        session()->put("panel.modulo_{$modulo}_oculto", true);
        
        // Log de la acción
        Log::info("Usuario ocultó módulo del panel", [
            'modulo' => $modulo,
            'usuario' => Auth::user()->name ?? 'Sistema',
            'fecha' => now()
        ]);
        
        $infoModulo = $this->obtenerInfoModulo($modulo);
        
        return response()->json([
            'success' => true,
            'mensaje' => "Módulo {$infoModulo['nombre']} ocultado correctamente",
            'redirect' => route('panel.indicadores.consolidado', ['mostrar_' . $modulo => false])
        ]);
    }

    /**
     * Restaurar módulo oculto
     */
    public function restaurarModulo(Request $request)
    {
        $request->validate([
            'modulo' => 'required|in:costos,ventas,consistencia,trazabilidad'
        ]);
        
        $modulo = $request->modulo;
        
        // Eliminar preferencia de ocultación
        session()->forget("panel.modulo_{$modulo}_oculto");
        
        $infoModulo = $this->obtenerInfoModulo($modulo);
        
        return response()->json([
            'success' => true,
            'mensaje' => "Módulo {$infoModulo['nombre']} restaurado correctamente"
        ]);
    }

    /**
     * Control de acceso - verificar si el usuario puede ver/ocultar módulos
     */
    public function verificarAcceso(Request $request)
    {
        $modulo = $request->get('modulo');
        
        // Verificar permisos del usuario (personalizable según roles)
        $permisos = [
            'costos' => true,
            'ventas' => true,
            'consistencia' => true,
            'trazabilidad' => true
        ];
        
        return response()->json([
            'success' => true,
            'permisos' => $permisos,
            'puede_modificar' => true
        ]);
    }

    /**
     * Preparar datos para el panel principal
     */
    private function prepararDatosPanel(array $consistencia, array $trazabilidad): array
    {
        return [
            'resumen_general' => [
                'total_lotes' => count($consistencia['lotes']),
                'lotes_consistentes' => $consistencia['resumen_consistencia']['lotes_consistentes'] ?? 0,
                'alertas_totales' => $consistencia['resumen_consistencia']['alertas_totales'] ?? 0,
                'nivel_confianza_promedio' => $consistencia['indicadores_globales']['confianza_promedio'] ?? 0
            ],
            'indicadores_clave' => [
                'fcr_promedio' => $consistencia['indicadores_globales']['fcr_promedio'] ?? 0,
                'mortalidad_promedio' => $consistencia['indicadores_globales']['mortalidad_promedio'] ?? 0,
                'biomasa_total' => $consistencia['indicadores_globales']['biomasa_total_kg'] ?? 0,
                'alimento_total' => $consistencia['indicadores_globales']['alimento_total_kg'] ?? 0
            ],
            'distribucion_especies' => $trazabilidad['estadisticas']['especies'] ?? [],
            'distribucion_estados' => $trazabilidad['estadisticas']['estados'] ?? []
        ];
    }

    /**
     * Obtener información detallada del módulo
     */
    private function obtenerInfoModulo(string $modulo): array
    {
        $modulos = [
            'costos' => [
                'nombre' => 'Análisis de Costos',
                'icono' => '💰',
                'descripcion' => 'Cálculo detallado del costo por libra producida',
                'funcionalidades' => [
                    'Costo de insumos y alimentación',
                    'Indicadores de rentabilidad',
                    'Análisis por lote',
                    'Exportación de reportes'
                ]
            ],
            'ventas' => [
                'nombre' => 'Ventas y Resultados',
                'icono' => '📈',
                'descripcion' => 'Análisis de ventas ejecutadas vs potenciales',
                'funcionalidades' => [
                    'Ventas realizadas',
                    'Potencial de inventario',
                    'Escenarios de precio',
                    'Márgenes de ganancia'
                ]
            ],
            'consistencia' => [
                'nombre' => 'Consistencia y Estimación',
                'icono' => '🎯',
                'descripcion' => 'Verificación de consistencia entre datos',
                'funcionalidades' => [
                    'Validación de producción',
                    'Análisis de pérdidas',
                    'Estimaciones futuras',
                    'Alertas de inconsistencia'
                ]
            ],
            'trazabilidad' => [
                'nombre' => 'Filtros y Trazabilidad',
                'icono' => '🔍',
                'descripcion' => 'Filtros avanzados y trazabilidad completa',
                'funcionalidades' => [
                    'Filtros parametrizables',
                    'Cronología de eventos',
                    'Cadena de suministro',
                    'Eventos críticos'
                ]
            ]
        ];
        
        return $modulos[$modulo] ?? [];
    }

    /**
     * Generar advertencias específicas para la ocultación
     */
    private function generarAdvertenciasOcultacion(string $modulo): array
    {
        $advertencias = [
            'costos' => [
                '⚠️ Perderá acceso al cálculo de costos por libra producida',
                '📊 No podrá ver indicadores de rentabilidad',
                '💱 Los reportes de costos no estarán disponibles',
                '🎯 Recomendación: Mantenga este módulo para control financiero'
            ],
            'ventas' => [
                '⚠️ No verá el análisis de ventas vs inventario disponible',
                '📈 Perderá escenarios de precios potenciales',
                '💰 Los márgenes de ganancia no se mostrarán',
                '🎯 Recomendación: Útil para decisiones de comercialización'
            ],
            'consistencia' => [
                '⚠️ No se validará la consistencia de datos',
                '🔍 Las inconsistencias no serán detectadas',
                '📉 Perderá alertas de problemas en producción',
                '🎯 Recomendación: Crítico para calidad de datos'
            ],
            'trazabilidad' => [
                '⚠️ Perderá capacidades de filtrado avanzado',
                '📋 La cronología de eventos no estará disponible',
                '🔗 La trazabilidad completa se ocultará',
                '🎯 Recomendación: Esencial para auditorías'
            ]
        ];
        
        return $advertencias[$modulo] ?? ['⚠️ Se ocultará este módulo del panel'];
    }

    /**
     * Generar mensaje de confirmación personalizado
     */
    private function generarMensajeConfirmacion(string $modulo): string
    {
        $infoModulo = $this->obtenerInfoModulo($modulo);
        
        return "¿Está seguro que desea ocultar el módulo '{$infoModulo['nombre']}'? " .
               "Esta acción puede afectar su capacidad de monitorear aspectos importantes de la producción. " .
               "Puede restaurarlo en cualquier momento desde las configuraciones del panel.";
    }

    /**
     * Obtener métricas rápidas para el dashboard
     */
    public function metricas(Request $request)
    {
        // Métricas básicas rápidas sin filtros complejos
        $totalLotes = \App\Models\Lote::count();
        $lotesActivos = \App\Models\Lote::where('estado', 'activo')->count();
        $biomasaTotal = \App\Models\Lote::sum('biomasa');
        
        return response()->json([
            'total_lotes' => $totalLotes,
            'lotes_activos' => $lotesActivos,
            'biomasa_total_kg' => $biomasaTotal,
            'biomasa_total_lb' => $biomasaTotal * 2.20462,
            'ultima_actualizacion' => now()->format('d/m/Y H:i:s')
        ]);
    }
}