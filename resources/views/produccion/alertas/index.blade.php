<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
            Alertas por anomalías
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 space-y-6">

        {{-- Filtros/Parámetros --}}
        <form method="GET"
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Lote</label>
                <select name="lote_id"
                    class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                    <option value="">Todos</option>
                    @foreach ($lotes as $l)
                        <option value="{{ $l->id }}" @selected($loteId == $l->id)>{{ $l->codigo_lote }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Tipo de alerta</label>
                <select name="tipo_alerta"
                    class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                    <option value="">Todas</option>
                    <option value="densidad" @selected($tipoAlerta == 'densidad')>Densidad crítica</option>
                    <option value="mortalidad" @selected($tipoAlerta == 'mortalidad')>Mortalidad elevada</option>
                    <option value="enfermedad" @selected($tipoAlerta == 'enfermedad')>Enfermedades registradas</option>
                    <option value="bajo_peso" @selected($tipoAlerta == 'bajo_peso')>Bajo peso</option>
                </select>
            </div>
            <div class="sm:col-span-2 md:col-span-3 lg:col-span-6 flex justify-end">
                <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Aplicar</button>
            </div>
        </form>

        {{-- Tabla de alertas --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Lote</th>
                        <th class="px-4 py-2 text-left">Tipo de alerta</th>
                        <th class="px-4 py-2 text-left">Periodo</th>
                        <th class="px-4 py-2 text-right">Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alertas as $a)
                        @if(($a->tipo_alerta === 'mortalidad' && $a->porcentaje_mortalidad >= 50) || 
                            ($a->tipo_alerta === 'densidad' && $a->porcentaje_capacidad >= 50))
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-4 py-2">{{ $a->codigo_lote }}</td>
                                <td class="px-4 py-2">{{ $a->tipo_alerta === 'mortalidad' ? 'Mortalidad elevada' : 'Densidad crítica' }}</td>
                                <td class="px-4 py-2 text-gray-700 dark:text-gray-300">
                                    <span class="inline-flex items-center">
                                        <i data-lucide="calendar" class="w-4 h-4 mr-1"></i>
                                        {{ $a->periodo ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    @if($a->tipo_alerta === 'mortalidad')
                                        @php
                                            $porcentaje = $a->porcentaje_mortalidad;
                                            $colorClase = match(true) {
                                                $porcentaje >= 76 => 'text-red-600 bg-red-100 dark:bg-red-900/20',
                                                $porcentaje >= 51 => 'text-amber-600 bg-amber-100 dark:bg-amber-900/20',
                                                default => 'text-orange-600 bg-orange-100 dark:bg-orange-900/20'
                                            };
                                            $nivelAlerta = match(true) {
                                                $porcentaje >= 76 => '¡Alerta Crítica!',
                                                $porcentaje >= 51 => '¡Alerta Alta!',
                                                default => '¡Precaución!'
                                            };
                                        @endphp
                                        <span class="font-semibold {{ $colorClase }} px-2 py-1 rounded">{{ $nivelAlerta }} Mortalidad Elevada</span><br>
                                        Cantidad de peces muertos: {{ $a->cantidad_inicial - $a->cantidad_actual }}<br>
                                        Porcentaje de mortalidad: <span class="font-bold {{ $colorClase }} px-2 py-1 rounded">{{ number_format($a->porcentaje_mortalidad, 2) }}%</span>
                                    @elseif($a->tipo_alerta === 'enfermedad')
                                        @php
                                            $porcentaje = $a->porcentaje_afectados;
                                            $colorClase = match(true) {
                                                $a->nivel_riesgo === 'alto' => 'text-red-600 bg-red-100 dark:bg-red-900/20',
                                                $a->nivel_riesgo === 'medio' => 'text-amber-600 bg-amber-100 dark:bg-amber-900/20',
                                                default => 'text-orange-600 bg-orange-100 dark:bg-orange-900/20'
                                            };
                                            $nivelAlerta = match($a->nivel_riesgo) {
                                                'alto' => '¡Alerta Sanitaria Crítica!',
                                                'medio' => '¡Alerta Sanitaria!',
                                                default => '¡Precaución Sanitaria!'
                                            };
                                        @endphp
                                        <span class="font-semibold {{ $colorClase }} px-2 py-1 rounded">{{ $nivelAlerta }}</span><br>
                                        Enfermedad: <span class="font-medium">{{ $a->nombre_enfermedad }}</span><br>
                                        Peces afectados: {{ $a->cantidad_afectados }}<br>
                                        Porcentaje del lote: <span class="font-bold {{ $colorClase }} px-2 py-1 rounded">{{ number_format($a->porcentaje_afectados, 2) }}%</span><br>
                                        Estado: <span class="font-medium">{{ $a->estado_tratamiento }}</span>
                                    @elseif($a->tipo_alerta === 'bajo_peso')
                                        @php
                                            $porcentaje = abs($a->porcentaje_desviacion);
                                            $colorClase = match(true) {
                                                $porcentaje >= 25 => 'text-red-600 bg-red-100 dark:bg-red-900/20',
                                                $porcentaje >= 20 => 'text-amber-600 bg-amber-100 dark:bg-amber-900/20',
                                                default => 'text-orange-600 bg-orange-100 dark:bg-orange-900/20'
                                            };
                                            $nivelAlerta = match(true) {
                                                $porcentaje >= 25 => '¡Alerta Crítica de Peso!',
                                                $porcentaje >= 20 => '¡Alerta de Peso!',
                                                default => '¡Peso Bajo!'
                                            };
                                        @endphp
                                        <span class="font-semibold {{ $colorClase }} px-2 py-1 rounded">{{ $nivelAlerta }}</span><br>
                                        Peso actual: <span class="font-medium">{{ number_format($a->peso_actual, 2) }} g</span><br>
                                        Peso esperado: <span class="font-medium">{{ number_format($a->peso_esperado, 2) }} g</span><br>
                                        Desviación: <span class="font-bold {{ $colorClase }} px-2 py-1 rounded">{{ number_format($a->porcentaje_desviacion, 2) }}%</span><br>
                                        FCR: {{ number_format($a->factor_conversion_alimento, 2) }}<br>
                                        Días consecutivos: {{ $a->dias_desviacion }} días<br>
                                        @if($a->observaciones_alimentacion)
                                            <span class="text-sm italic">{{ $a->observaciones_alimentacion }}</span>
                                        @endif
                                    @else
                                        @php
                                            $porcentaje = $a->porcentaje_capacidad;
                                            $colorClase = match(true) {
                                                $porcentaje >= 120 => 'text-red-600 bg-red-100 dark:bg-red-900/20',
                                                $porcentaje >= 110 => 'text-amber-600 bg-amber-100 dark:bg-amber-900/20',
                                                default => 'text-orange-600 bg-orange-100 dark:bg-orange-900/20'
                                            };
                                            $nivelAlerta = match(true) {
                                                $porcentaje >= 120 => '¡Sobrepoblación Crítica!',
                                                $porcentaje >= 110 => '¡Densidad Alta!',
                                                default => '¡Densidad Elevada!'
                                            };
                                        @endphp
                                        <span class="font-semibold {{ $colorClase }} px-2 py-1 rounded">{{ $nivelAlerta }}</span><br>
                                        Densidad actual: {{ number_format($a->densidad_actual, 2) }} peces/m³<br>
                                        Capacidad máxima: {{ number_format($a->capacidad_maxima, 2) }} peces/m³<br>
                                        Porcentaje de capacidad: <span class="font-bold {{ $colorClase }} px-2 py-1 rounded">{{ number_format($a->porcentaje_capacidad, 2) }}%</span>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                No se detectaron alertas para los parámetros actuales.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="text-xs text-gray-500 dark:text-gray-400">
            Ajusta los filtros para visualizar alertas específicas.
        </p>
    </div>
</x-app-layout>
