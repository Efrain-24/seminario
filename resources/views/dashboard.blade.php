<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }} - Análisis de Crecimiento
            </h2>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <a href="{{ route('aplicaciones') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Ver Aplicaciones
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Resumen General -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Resumen General</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        @php
                            // Obtener datos de lotes activos
                            $lotesActivos = \App\Models\Lote::where('estado', 'activo')->count();
                            $totalPeces = \App\Models\Lote::where('estado', 'activo')->sum('cantidad_inicial');
                            $promedioSemanas = \App\Models\Lote::where('estado', 'activo')
                                ->whereNotNull('fecha_inicio')
                                ->get()
                                ->avg(function($lote) {
                                    return $lote->fecha_inicio->diffInWeeks(now());
                                }) ?? 0;
                            $alimentacionesHoy = \App\Models\Alimentacion::whereDate('fecha_alimentacion', today())->count();
                        @endphp
                        
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Lotes Activos</p>
                                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $lotesActivos }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Peces</p>
                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($totalPeces) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Promedio Semanas</p>
                                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($promedioSemanas, 1) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Alimentaciones Hoy</p>
                                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $alimentacionesHoy }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Predicción de Crecimiento por Lote -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Predicción de Crecimiento por Lote</h3>
                    
                    @php
                        $lotesConPrediccion = \App\Models\Lote::where('estado', 'activo')
                            ->whereNotNull('fecha_inicio')
                            ->with(['unidadProduccion', 'seguimientos'])
                            ->get()
                            ->map(function($lote) {
                                $semanasTranscurridas = $lote->fecha_inicio->diffInWeeks(now());
                                $seguimientos = $lote->seguimientos()->orderBy('fecha_seguimiento', 'desc')->take(3)->get();
                                
                                // Cálculos basados en datos reales de trucha
                                $pesoEsperadoSemana = 0;
                                $tendencia = 'estable';
                                $prediccionProximaSemana = 0;
                                
                                if ($lote->especie == 'trucha') {
                                    // Curva típica de crecimiento para trucha arcoíris
                                    $pesoEsperadoSemana = min(250, 2 + ($semanasTranscurridas * 4.5) + (($semanasTranscurridas ** 1.2) * 0.8));
                                    $prediccionProximaSemana = min(260, 2 + (($semanasTranscurridas + 1) * 4.5) + ((($semanasTranscurridas + 1) ** 1.2) * 0.8));
                                } else {
                                    // Curva genérica para otras especies
                                    $pesoEsperadoSemana = min(200, 1.5 + ($semanasTranscurridas * 3.8) + (($semanasTranscurridas ** 1.15) * 0.7));
                                    $prediccionProximaSemana = min(210, 1.5 + (($semanasTranscurridas + 1) * 3.8) + ((($semanasTranscurridas + 1) ** 1.15) * 0.7));
                                }
                                
                                // Determinar tendencia basada en seguimientos recientes
                                if ($seguimientos->count() >= 2) {
                                    $ultimoPeso = $seguimientos->first()->peso_promedio ?? $pesoEsperadoSemana;
                                    $penultimoPeso = $seguimientos->skip(1)->first()->peso_promedio ?? $pesoEsperadoSemana * 0.9;
                                    
                                    if ($ultimoPeso > $penultimoPeso * 1.05) {
                                        $tendencia = 'creciente';
                                    } elseif ($ultimoPeso < $penultimoPeso * 0.95) {
                                        $tendencia = 'decreciente';
                                    }
                                    
                                    // Ajustar predicción basada en tendencia real
                                    $factorTendencia = ($ultimoPeso / $pesoEsperadoSemana);
                                    $prediccionProximaSemana *= $factorTendencia;
                                }
                                
                                return [
                                    'lote' => $lote,
                                    'semanas_transcurridas' => $semanasTranscurridas,
                                    'peso_esperado_actual' => round($pesoEsperadoSemana, 1),
                                    'prediccion_proxima_semana' => round($prediccionProximaSemana, 1),
                                    'tendencia' => $tendencia,
                                    'seguimientos_recientes' => $seguimientos,
                                    'supervivencia_estimada' => max(75, 95 - ($semanasTranscurridas * 0.8)),
                                    'biomasa_estimada' => round(($lote->cantidad_inicial * ($pesoEsperadoSemana / 1000) * (max(75, 95 - ($semanasTranscurridas * 0.8)) / 100)), 2)
                                ];
                            });
                    @endphp
                    
                    @if($lotesConPrediccion->count() > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($lotesConPrediccion as $datos)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $datos['lote']->codigo_lote }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $datos['lote']->unidadProduccion->nombre }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-500">
                                                {{ ucfirst($datos['lote']->especie) }} • Semana {{ $datos['semanas_transcurridas'] }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            @if($datos['tendencia'] == 'creciente')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                    </svg>
                                                    Creciente
                                                </span>
                                            @elseif($datos['tendencia'] == 'decreciente')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                                    </svg>
                                                    Decreciente
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                    </svg>
                                                    Estable
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Peso Esperado Actual</p>
                                            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                                {{ $datos['peso_esperado_actual'] }}g
                                            </p>
                                        </div>
                                        <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg">
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Predicción Próx. Sem.</p>
                                            <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                                {{ $datos['prediccion_proxima_semana'] }}g
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Supervivencia Est.</p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ number_format($datos['supervivencia_estimada'], 1) }}%
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Biomasa Est.</p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $datos['biomasa_estimada'] }} kg
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Indicador visual de progreso -->
                                    <div class="mb-3">
                                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
                                            <span>Desarrollo</span>
                                            <span>{{ min(100, round(($datos['semanas_transcurridas'] / 24) * 100)) }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full" 
                                                 style="width: {{ min(100, round(($datos['semanas_transcurridas'] / 24) * 100)) }}%"></div>
                                        </div>
                                    </div>
                                    
                                    @if($datos['seguimientos_recientes']->count() > 0)
                                        <div class="text-xs text-gray-500 dark:text-gray-500">
                                            <p>Último seguimiento: {{ $datos['seguimientos_recientes']->first()->fecha_seguimiento->format('d/m/Y') }}</p>
                                            <p>Peso real: {{ $datos['seguimientos_recientes']->first()->peso_promedio ?? 'N/A' }}g</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Sin lotes activos</h3>
                            <p class="text-gray-600 dark:text-gray-400">No hay lotes activos para mostrar predicciones de crecimiento.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recomendaciones -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recomendaciones</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                                        Monitoreo Regular
                                    </p>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-400">
                                        Realiza seguimientos semanales para ajustar las predicciones
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-blue-800 dark:text-blue-300">
                                        Alimentación Balanceada
                                    </p>
                                    <p class="text-sm text-blue-700 dark:text-blue-400">
                                        Ajusta las raciones según el peso y edad de los peces
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
