@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-100 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header del Panel -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                        📊 Panel de Indicadores - HU-007/HU-008
                    </h1>
                    <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                        Mostrar la Ganancia (realizada) y si el lote está en proceso, la Venta potencial (inventario disponible por precio estimado)
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('costos.produccion.index') }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        💰 Ver Costos
                    </a>
                    <a href="{{ route('ventas.resultados.index') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        📈 Ver Ventas
                    </a>
                </div>
            </div>
        </div>

        <!-- Filtros Globales -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" action="{{ route('panel.indicadores.consolidado') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Especie</label>
                    <input type="text" name="especie" value="{{ request('especie') }}" 
                           placeholder="Ej: Tilapia" 
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado</label>
                    <select name="estado" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="cosechado" {{ request('estado') == 'cosechado' ? 'selected' : '' }}>Cosechado</option>
                        <option value="vendido" {{ request('estado') == 'vendido' ? 'selected' : '' }}>Vendido</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha Desde</label>
                    <input type="date" name="fecha_inicio_desde" value="{{ request('fecha_inicio_desde') }}" 
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha Hasta</label>
                    <input type="date" name="fecha_inicio_hasta" value="{{ request('fecha_inicio_hasta') }}" 
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                        🔍 Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>

        <!-- Control de Módulos Visibles -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                🎛️ Control de Módulos del Panel
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $modulosDisponibles = [
                        'costos' => ['nombre' => 'Análisis de Costos', 'icono' => '💰', 'activo' => $modulos['costos'] ?? true],
                        'ventas' => ['nombre' => 'Ventas y Resultados', 'icono' => '📈', 'activo' => $modulos['ventas'] ?? true],
                        'consistencia' => ['nombre' => 'Consistencia', 'icono' => '🎯', 'activo' => $modulos['consistencia'] ?? true],
                        'trazabilidad' => ['nombre' => 'Trazabilidad', 'icono' => '🔍', 'activo' => $modulos['trazabilidad'] ?? true]
                    ];
                @endphp
                
                @foreach($modulosDisponibles as $codigo => $modulo)
                <div class="flex items-center justify-between p-3 border rounded-lg {{ $modulo['activo'] ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }}">
                    <div class="flex items-center">
                        <span class="text-2xl mr-2">{{ $modulo['icono'] }}</span>
                        <span class="font-medium text-gray-900">{{ $modulo['nombre'] }}</span>
                    </div>
                    <button onclick="toggleModulo('{{ $codigo }}', {{ $modulo['activo'] ? 'true' : 'false' }})"
                            class="px-3 py-1 text-sm rounded {{ $modulo['activo'] ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                        {{ $modulo['activo'] ? 'Ocultar' : 'Mostrar' }}
                    </button>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Contenido de Módulos -->
        <div class="space-y-8">
            
            <!-- Módulo de Costos -->
            @if($modulos['costos'] ?? true)
            <div id="modulo-costos" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-green-50 dark:bg-green-900/20 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <span class="text-2xl mr-3">💰</span>
                            RF22: Análisis de Costos de Producción
                        </h3>
                        <a href="{{ route('costos.produccion.index') }}" 
                           class="text-green-600 hover:text-green-800 font-medium">
                            Ver Detalle Completo →
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if(isset($datos['costos']))
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-green-100 dark:bg-green-900/30 p-4 rounded-lg">
                                <div class="text-sm text-green-600 dark:text-green-400">Costo Total Operación</div>
                                <div class="text-2xl font-bold text-green-800 dark:text-green-200">
                                    Q{{ number_format($datos['costos']['resumen']['costo_total_operacion'] ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="bg-blue-100 dark:bg-blue-900/30 p-4 rounded-lg">
                                <div class="text-sm text-blue-600 dark:text-blue-400">Producción Total</div>
                                <div class="text-2xl font-bold text-blue-800 dark:text-blue-200">
                                    {{ number_format($datos['costos']['resumen']['produccion_total_libras'] ?? 0, 1) }} lb
                                </div>
                            </div>
                            <div class="bg-purple-100 dark:bg-purple-900/30 p-4 rounded-lg">
                                <div class="text-sm text-purple-600 dark:text-purple-400">Costo Promedio/Libra</div>
                                <div class="text-2xl font-bold text-purple-800 dark:text-purple-200">
                                    Q{{ number_format($datos['costos']['resumen']['costo_promedio_por_libra'] ?? 0, 2) }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Resumen por Lote (Primeros 5) -->
                        @if(isset($datos['costos']['lotes']) && count($datos['costos']['lotes']) > 0)
                        <div class="mt-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Resumen por Lote (Primeros 5)</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Lote</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Costo Total</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Producción</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Costo/Libra</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ganancia</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach(array_slice($datos['costos']['lotes'], 0, 5) as $lote)
                                        <tr>
                                            <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $lote['codigo_lote'] }}<br>
                                                <span class="text-xs text-gray-500">{{ $lote['especie'] }}</span>
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                                                Q{{ number_format($lote['costos']['total'], 2) }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                                                {{ number_format($lote['produccion']['total_libras'], 1) }} lb
                                            </td>
                                            <td class="px-4 py-2 text-sm font-bold text-blue-600">
                                                Q{{ number_format($lote['indicadores']['costo_por_libra'], 2) }}
                                            </td>
                                            <td class="px-4 py-2 text-sm font-bold {{ $lote['indicadores']['ganancia_realizada'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                Q{{ number_format($lote['indicadores']['ganancia_realizada'], 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">
                            Módulo de costos no cargado. <a href="{{ route('costos.produccion.index') }}" class="text-blue-600 hover:text-blue-800">Ver análisis completo</a>
                        </p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Módulo de Ventas -->
            @if($modulos['ventas'] ?? true)
            <div id="modulo-ventas" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-blue-50 dark:bg-blue-900/20 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <span class="text-2xl mr-3">📈</span>
                            RF36: Ventas Ejecutadas vs Potenciales
                        </h3>
                        <a href="{{ route('ventas.resultados.index') }}" 
                           class="text-blue-600 hover:text-blue-800 font-medium">
                            Ver Análisis Completo →
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if(isset($datos['ventas']))
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="bg-green-100 dark:bg-green-900/30 p-4 rounded-lg">
                                <div class="text-sm text-green-600 dark:text-green-400">Ventas Ejecutadas</div>
                                <div class="text-2xl font-bold text-green-800 dark:text-green-200">
                                    Q{{ number_format($datos['ventas']['resumen_global']['ventas_ejecutadas_total'] ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="bg-blue-100 dark:bg-blue-900/30 p-4 rounded-lg">
                                <div class="text-sm text-blue-600 dark:text-blue-400">Ventas Potenciales</div>
                                <div class="text-2xl font-bold text-blue-800 dark:text-blue-200">
                                    Q{{ number_format($datos['ventas']['resumen_global']['ventas_potenciales_total'] ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="bg-yellow-100 dark:bg-yellow-900/30 p-4 rounded-lg">
                                <div class="text-sm text-yellow-600 dark:text-yellow-400">Oportunidad</div>
                                <div class="text-2xl font-bold text-yellow-800 dark:text-yellow-200">
                                    Q{{ number_format($datos['ventas']['resumen_global']['oportunidad_total'] ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="bg-purple-100 dark:bg-purple-900/30 p-4 rounded-lg">
                                <div class="text-sm text-purple-600 dark:text-purple-400">Eficiencia</div>
                                <div class="text-2xl font-bold text-purple-800 dark:text-purple-200">
                                    {{ number_format($datos['ventas']['resumen_global']['eficiencia_ventas'] ?? 0, 1) }}%
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">
                            Módulo de ventas no cargado. <a href="{{ route('ventas.resultados.index') }}" class="text-blue-600 hover:text-blue-800">Ver análisis completo</a>
                        </p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Módulo de Consistencia -->
            @if($modulos['consistencia'] ?? true)
            <div id="modulo-consistencia" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-yellow-50 dark:bg-yellow-900/20 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                        <span class="text-2xl mr-3">🎯</span>
                        RF37: Consistencia y Estimación
                    </h3>
                </div>
                <div class="p-6">
                    @if(isset($datos['consistencia']))
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-green-100 dark:bg-green-900/30 p-4 rounded-lg">
                                <div class="text-sm text-green-600 dark:text-green-400">Lotes Consistentes</div>
                                <div class="text-2xl font-bold text-green-800 dark:text-green-200">
                                    {{ $datos['consistencia']['resumen_consistencia']['lotes_consistentes'] ?? 0 }} / {{ $datos['consistencia']['resumen_consistencia']['total_lotes'] ?? 0 }}
                                </div>
                                <div class="text-xs text-green-600 dark:text-green-400">
                                    {{ number_format($datos['consistencia']['resumen_consistencia']['porcentaje_consistencia'] ?? 0, 1) }}%
                                </div>
                            </div>
                            <div class="bg-red-100 dark:bg-red-900/30 p-4 rounded-lg">
                                <div class="text-sm text-red-600 dark:text-red-400">Alertas Totales</div>
                                <div class="text-2xl font-bold text-red-800 dark:text-red-200">
                                    {{ $datos['consistencia']['resumen_consistencia']['alertas_totales'] ?? 0 }}
                                </div>
                            </div>
                            <div class="bg-blue-100 dark:bg-blue-900/30 p-4 rounded-lg">
                                <div class="text-sm text-blue-600 dark:text-blue-400">FCR Promedio</div>
                                <div class="text-2xl font-bold text-blue-800 dark:text-blue-200">
                                    {{ number_format($datos['consistencia']['indicadores_globales']['fcr_promedio'] ?? 0, 2) }}
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">
                            Módulo de consistencia no cargado.
                        </p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Módulo de Trazabilidad -->
            @if($modulos['trazabilidad'] ?? true)
            <div id="modulo-trazabilidad" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-purple-50 dark:bg-purple-900/20 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                        <span class="text-2xl mr-3">🔍</span>
                        RF38: Filtros y Trazabilidad Avanzada
                    </h3>
                </div>
                <div class="p-6">
                    @if(isset($datos['trazabilidad']))
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="bg-purple-100 dark:bg-purple-900/30 p-4 rounded-lg">
                                <div class="text-sm text-purple-600 dark:text-purple-400">Lotes Analizados</div>
                                <div class="text-2xl font-bold text-purple-800 dark:text-purple-200">
                                    {{ $datos['trazabilidad']['estadisticas']['total_lotes'] ?? 0 }}
                                </div>
                            </div>
                            <div class="bg-indigo-100 dark:bg-indigo-900/30 p-4 rounded-lg">
                                <div class="text-sm text-indigo-600 dark:text-indigo-400">Especies Diferentes</div>
                                <div class="text-2xl font-bold text-indigo-800 dark:text-indigo-200">
                                    {{ count($datos['trazabilidad']['estadisticas']['especies'] ?? []) }}
                                </div>
                            </div>
                            <div class="bg-teal-100 dark:bg-teal-900/30 p-4 rounded-lg">
                                <div class="text-sm text-teal-600 dark:text-teal-400">Biomasa Total</div>
                                <div class="text-2xl font-bold text-teal-800 dark:text-teal-200">
                                    {{ number_format($datos['trazabilidad']['estadisticas']['biomasa_total'] ?? 0, 1) }} kg
                                </div>
                            </div>
                            <div class="bg-orange-100 dark:bg-orange-900/30 p-4 rounded-lg">
                                <div class="text-sm text-orange-600 dark:text-orange-400">Días Promedio</div>
                                <div class="text-2xl font-bold text-orange-800 dark:text-orange-200">
                                    {{ number_format($datos['trazabilidad']['estadisticas']['promedio_dias_produccion'] ?? 0, 0) }}
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">
                            Módulo de trazabilidad no cargado.
                        </p>
                    @endif
                </div>
            </div>
            @endif

        </div>

        <!-- Información adicional -->
        <div class="mt-8 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-6">
            <h4 class="text-lg font-medium text-indigo-900 dark:text-indigo-100 mb-3">
                📋 Información sobre el Panel de Indicadores (Sprint 11)
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-indigo-800 dark:text-indigo-200">
                <div>
                    <strong>Funcionalidades implementadas:</strong>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>RF22: Cálculo detallado del costo por libra producida</li>
                        <li>RF36: Obtener ventas ejecutadas y ventas potenciales</li>
                        <li>RF37: Consistencia y estimación de datos</li>
                        <li>RF38: Filtros avanzados y trazabilidad</li>
                    </ul>
                </div>
                <div>
                    <strong>Controles del panel:</strong>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>RF39: Confirmación al ocultar módulos</li>
                        <li>Filtros parametrizables por fechas y especies</li>
                        <li>Visualización consolidada de todos los indicadores</li>
                        <li>Alertas y recomendaciones automáticas</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Ocultar Módulos -->
<div id="modalConfirmacion" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center mb-4">
            <span class="text-3xl mr-3">⚠️</span>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Confirmar Ocultación de Módulo
            </h3>
        </div>
        
        <div id="contenidoModal" class="mb-6">
            <!-- Contenido dinámico del modal -->
        </div>
        
        <div class="flex justify-end space-x-3">
            <button onclick="cerrarModal()" 
                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                Cancelar
            </button>
            <button onclick="confirmarOcultacion()" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Sí, Ocultar Módulo
            </button>
        </div>
    </div>
</div>

<script>
let moduloActual = '';
let accionActual = '';

async function toggleModulo(modulo, activo) {
    moduloActual = modulo;
    accionActual = activo ? 'ocultar' : 'mostrar';
    
    if (accionActual === 'mostrar') {
        // Mostrar sin confirmación
        await ejecutarAccionModulo(modulo, 'mostrar', true);
        return;
    }
    
    // Solicitar confirmación para ocultar
    try {
        const response = await fetch('{{ route("panel.indicadores.modulo.confirmar_ocultar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                modulo: modulo,
                accion: accionActual
            })
        });
        
        const data = await response.json();
        
        if (data.success && data.requiere_confirmacion) {
            mostrarModalConfirmacion(data);
        } else {
            await ejecutarAccionModulo(modulo, accionActual, true);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    }
}

function mostrarModalConfirmacion(data) {
    const modal = document.getElementById('modalConfirmacion');
    const contenido = document.getElementById('contenidoModal');
    
    let advertenciasHtml = '';
    if (data.advertencias && data.advertencias.length > 0) {
        advertenciasHtml = `
            <div class="mb-4">
                <h4 class="font-medium text-red-800 dark:text-red-200 mb-2">Advertencias:</h4>
                <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                    ${data.advertencias.map(adv => `<li>${adv}</li>`).join('')}
                </ul>
            </div>
        `;
    }
    
    contenido.innerHTML = `
        <div class="mb-4">
            <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                ${data.modulo.icono} ${data.modulo.nombre}
            </h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">${data.modulo.descripcion}</p>
        </div>
        
        ${advertenciasHtml}
        
        <p class="text-sm text-gray-700 dark:text-gray-300">
            ${data.mensaje_confirmacion}
        </p>
    `;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function cerrarModal() {
    const modal = document.getElementById('modalConfirmacion');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

async function confirmarOcultacion() {
    await ejecutarAccionModulo(moduloActual, accionActual, true);
    cerrarModal();
}

async function ejecutarAccionModulo(modulo, accion, confirmado) {
    try {
        const endpoint = accion === 'ocultar' 
            ? '{{ route("panel.indicadores.modulo.ejecutar_ocultar") }}'
            : '{{ route("panel.indicadores.modulo.restaurar") }}';
            
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                modulo: modulo,
                confirmado: confirmado
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                location.reload();
            }
        } else {
            alert(data.mensaje || 'Error al procesar la solicitud');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    }
}

// Actualizar métricas cada 5 minutos
setInterval(async function() {
    try {
        const response = await fetch('{{ route("panel.indicadores.metricas") }}');
        const data = await response.json();
        
        // Actualizar algún indicador en tiempo real si es necesario
        console.log('Métricas actualizadas:', data);
    } catch (error) {
        console.error('Error actualizando métricas:', error);
    }
}, 300000); // 5 minutos
</script>
@endsection