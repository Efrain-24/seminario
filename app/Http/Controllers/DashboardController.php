<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\UnidadProduccion;
use App\Models\Notificacion;
use App\Models\Alerta;
use App\Models\Mortalidad;
use App\Models\Enfermedad;
use App\Models\Seguimiento;
use App\Models\InventarioExistencia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $estadisticasGenerales = $this->obtenerEstadisticasGenerales();
        
        // Estadísticas de producción
        $estadisticasProduccion = $this->obtenerEstadisticasProduccion();
        
        // Alertas y notificaciones
        $alertasNotificaciones = $this->obtenerAlertasNotificaciones();
        
        // Datos para gráficos
        $datosGraficos = $this->obtenerDatosGraficos();
        
        // Actividad reciente
        $actividadReciente = $this->obtenerActividadReciente();
        
        return view('dashboard', compact(
            'estadisticasGenerales',
            'estadisticasProduccion',
            'alertasNotificaciones',
            'datosGraficos',
            'actividadReciente'
        ));
    }

    private function obtenerEstadisticasGenerales()
    {
        $lotesActivos = Lote::where('estado', 'activo')->count();
        $unidadesActivas = UnidadProduccion::where('estado', 'activo')->count();
        $notificacionesNoLeidas = Notificacion::noLeidas()
            ->paraUsuario(Auth::id())
            ->vigentes()
            ->count();
        $alertasActivas = Alerta::whereNull('fecha_resolucion')->count();
        
        // Biomasa total
        $biomasaTotal = Lote::where('estado', 'activo')
            ->get()
            ->sum(function($lote) {
                return $lote->biomasa;
            });
        
        // Porcentaje de ocupación promedio
        $ocupacionPromedio = UnidadProduccion::where('estado', 'activo')
            ->get()
            ->avg(function($unidad) {
                return $unidad->porcentaje_ocupacion;
            });

        return [
            'lotes_activos' => $lotesActivos,
            'unidades_activas' => $unidadesActivas,
            'notificaciones_no_leidas' => $notificacionesNoLeidas,
            'alertas_activas' => $alertasActivas,
            'biomasa_total' => round($biomasaTotal, 2),
            'ocupacion_promedio' => round($ocupacionPromedio ?? 0, 1)
        ];
    }

    private function obtenerEstadisticasProduccion()
    {
        // Mortalidad del mes actual
        $mortalidadMes = Mortalidad::whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->sum('cantidad');
        
        // Peso promedio actual
        $pesoPromedio = DB::table('seguimientos')
            ->join('lotes', 'seguimientos.lote_id', '=', 'lotes.id')
            ->where('lotes.estado', 'activo')
            ->whereNotNull('seguimientos.peso_promedio')
            ->avg('seguimientos.peso_promedio');
        
        // Crecimiento promedio (últimos 30 días)
        $crecimientoPromedio = $this->calcularCrecimientoPromedio();
        
        // Especies en producción
        $especiesProduccion = Lote::where('estado', 'activo')
            ->select('especie', DB::raw('count(*) as cantidad'))
            ->groupBy('especie')
            ->get();
        
        // Eficiencia alimentaria promedio
        $eficienciaAlimentaria = $this->calcularEficienciaAlimentaria();

        return [
            'mortalidad_mes' => $mortalidadMes,
            'peso_promedio' => round($pesoPromedio ?? 0, 2),
            'crecimiento_promedio' => round($crecimientoPromedio, 2),
            'especies_produccion' => $especiesProduccion,
            'eficiencia_alimentaria' => round($eficienciaAlimentaria, 2)
        ];
    }

    private function obtenerAlertasNotificaciones()
    {
        $alertasCriticas = Alerta::where(function($query) {
                $query->where('tipo_alerta', 'enfermedad')
                      ->where('nivel_riesgo', 'alto')
                      ->orWhere(function($q) {
                          $q->where('tipo_alerta', 'bajo peso')
                            ->where('porcentaje_desviacion', '<=', -25);
                      });
            })
            ->whereNull('fecha_resolucion')
            ->with('lote')
            ->latest()
            ->take(5)
            ->get();

        $notificacionesRecientes = Notificacion::paraUsuario(Auth::id())
            ->vigentes()
            ->latest()
            ->take(5)
            ->get();

        return [
            'alertas_criticas' => $alertasCriticas,
            'notificaciones_recientes' => $notificacionesRecientes
        ];
    }

    private function obtenerDatosGraficos()
    {
        // Datos para gráfico de mortalidad por mes (últimos 6 meses)
        $mortalidadPorMes = [];
        for ($i = 5; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $cantidad = Mortalidad::whereMonth('fecha', $fecha->month)
                ->whereYear('fecha', $fecha->year)
                ->sum('cantidad');
            
            $mortalidadPorMes[] = [
                'mes' => $fecha->format('M Y'),
                'cantidad' => $cantidad
            ];
        }

        // Datos para gráfico de biomasa por especie
        $biomasaPorEspecie = Lote::where('estado', 'activo')
            ->select('especie')
            ->get()
            ->groupBy('especie')
            ->map(function($lotes, $especie) {
                $biomasa = $lotes->sum(function($lote) {
                    return $lote->biomasa;
                });
                return [
                    'especie' => $especie,
                    'biomasa' => round($biomasa, 2)
                ];
            })
            ->values();

        // Datos para gráfico de ocupación de unidades
        $ocupacionUnidades = UnidadProduccion::where('estado', 'activo')
            ->select('tipo')
            ->get()
            ->groupBy('tipo')
            ->map(function($unidades, $tipo) {
                $ocupacionPromedio = $unidades->avg(function($unidad) {
                    return $unidad->porcentaje_ocupacion;
                });
                return [
                    'tipo' => ucfirst(str_replace('_', ' ', $tipo)),
                    'ocupacion' => round($ocupacionPromedio ?? 0, 1)
                ];
            })
            ->values();

        // Datos para gráfico de crecimiento (últimas 4 semanas)
        $crecimientoPorSemana = $this->obtenerCrecimientoPorSemana();

        return [
            'mortalidad_por_mes' => $mortalidadPorMes,
            'biomasa_por_especie' => $biomasaPorEspecie,
            'ocupacion_unidades' => $ocupacionUnidades,
            'crecimiento_por_semana' => $crecimientoPorSemana
        ];
    }

    private function obtenerActividadReciente()
    {
        $actividades = collect();

        // Últimos seguimientos
        $seguimientos = Seguimiento::with(['lote', 'usuario'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($seguimiento) {
                return [
                    'tipo' => 'seguimiento',
                    'descripcion' => "Seguimiento registrado para lote {$seguimiento->lote->codigo_lote}",
                    'fecha' => $seguimiento->fecha_seguimiento,
                    'usuario' => $seguimiento->usuario->name ?? 'Sistema',
                    'icono' => 'clipboard-list',
                    'color' => 'blue'
                ];
            });

        // Últimas mortalidades
        $mortalidades = Mortalidad::with(['lote', 'user'])
            ->latest()
            ->take(3)
            ->get()
            ->map(function($mortalidad) {
                return [
                    'tipo' => 'mortalidad',
                    'descripcion' => "Mortalidad registrada en lote {$mortalidad->lote->codigo_lote}: {$mortalidad->cantidad} individuos",
                    'fecha' => $mortalidad->fecha,
                    'usuario' => $mortalidad->user->name ?? 'Sistema',
                    'icono' => 'alert-triangle',
                    'color' => 'red'
                ];
            });

        // Últimas alertas
        $alertas = Alerta::with(['lote'])
            ->latest()
            ->take(3)
            ->get()
            ->map(function($alerta) {
                return [
                    'tipo' => 'alerta',
                    'descripcion' => "Alerta de {$alerta->tipo_alerta} en lote {$alerta->lote->codigo_lote}",
                    'fecha' => $alerta->created_at,
                    'usuario' => 'Sistema',
                    'icono' => 'bell',
                    'color' => 'yellow'
                ];
            });

        return $actividades
            ->merge($seguimientos)
            ->merge($mortalidades)
            ->merge($alertas)
            ->sortByDesc('fecha')
            ->take(10)
            ->values();
    }

    private function calcularCrecimientoPromedio()
    {
        $seguimientos = DB::table('seguimientos')
            ->join('lotes', 'seguimientos.lote_id', '=', 'lotes.id')
            ->where('lotes.estado', 'activo')
            ->where('seguimientos.fecha_seguimiento', '>=', Carbon::now()->subDays(30))
            ->whereNotNull('seguimientos.peso_promedio')
            ->select('seguimientos.lote_id', 'seguimientos.peso_promedio', 'seguimientos.fecha_seguimiento')
            ->orderBy('seguimientos.fecha_seguimiento')
            ->get()
            ->groupBy('lote_id');

        $crecimientos = [];
        
        foreach ($seguimientos as $loteId => $registros) {
            if ($registros->count() >= 2) {
                $primero = $registros->first();
                $ultimo = $registros->last();
                
                $diasTranscurridos = Carbon::parse($ultimo->fecha_seguimiento)
                    ->diffInDays(Carbon::parse($primero->fecha_seguimiento));
                
                if ($diasTranscurridos > 0) {
                    $crecimiento = ($ultimo->peso_promedio - $primero->peso_promedio) / $diasTranscurridos;
                    $crecimientos[] = $crecimiento;
                }
            }
        }

        return collect($crecimientos)->avg() ?? 0;
    }

    private function calcularEficienciaAlimentaria()
    {
        // Simplificado: retorna un valor estimado
        // En implementación real se calcularía basado en consumo de alimento vs ganancia de peso
        return 1.8; // Factor de conversión alimenticia típico
    }

    private function obtenerCrecimientoPorSemana()
    {
        $datos = [];
        
        for ($i = 3; $i >= 0; $i--) {
            $fechaInicio = Carbon::now()->subWeeks($i + 1);
            $fechaFin = Carbon::now()->subWeeks($i);
            
            $pesoPromedio = DB::table('seguimientos')
                ->join('lotes', 'seguimientos.lote_id', '=', 'lotes.id')
                ->where('lotes.estado', 'activo')
                ->whereBetween('seguimientos.fecha_seguimiento', [$fechaInicio, $fechaFin])
                ->avg('seguimientos.peso_promedio');
            
            $datos[] = [
                'semana' => "Sem " . (4 - $i),
                'peso_promedio' => round($pesoPromedio ?? 0, 2)
            ];
        }
        
        return $datos;
    }
}