<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $tipoAlimento->nombre_completo }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('tipos-alimento.edit', $tipoAlimento) }}" 
                   class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
                <a href="{{ route('tipos-alimento.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Información Principal -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <!-- Información Básica -->
                        <div class="lg:col-span-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Información Básica</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre</label>
                                    <p class="text-lg text-gray-900 dark:text-gray-100">{{ $tipoAlimento->nombre }}</p>
                                </div>
                                
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Marca</label>
                                    <p class="text-lg text-gray-900 dark:text-gray-100">{{ $tipoAlimento->marca ?? 'Sin marca' }}</p>
                                </div>
                                
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Categoría</label>
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                        @if($tipoAlimento->categoria === 'concentrado') bg-blue-100 text-blue-800
                                        @elseif($tipoAlimento->categoria === 'pellet') bg-green-100 text-green-800
                                        @elseif($tipoAlimento->categoria === 'hojuela') bg-yellow-100 text-yellow-800
                                        @elseif($tipoAlimento->categoria === 'artesanal') bg-purple-100 text-purple-800
                                        @elseif($tipoAlimento->categoria === 'vivo') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ App\Models\TipoAlimento::getCategorias()[$tipoAlimento->categoria] ?? $tipoAlimento->categoria }}
                                    </span>
                                </div>
                                
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</label>
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                        {{ $tipoAlimento->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $tipoAlimento->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Estadísticas de Uso -->
                        <div class="lg:col-span-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Estadísticas de Uso</h3>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                        {{ $estadisticas['total_usos'] }}
                                    </div>
                                    <div class="text-sm text-blue-700 dark:text-blue-300">
                                        Total de usos
                                    </div>
                                </div>
                                
                                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        {{ number_format($estadisticas['cantidad_total_usada'], 2) }} kg
                                    </div>
                                    <div class="text-sm text-green-700 dark:text-green-300">
                                        Cantidad total usada
                                    </div>
                                </div>
                                
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                        ${{ number_format($estadisticas['costo_total_usado'], 2) }}
                                    </div>
                                    <div class="text-sm text-yellow-700 dark:text-yellow-300">
                                        Costo total
                                    </div>
                                </div>
                                
                                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                        {{ $estadisticas['ultimo_uso'] ? \Carbon\Carbon::parse($estadisticas['ultimo_uso'])->format('d/m/Y') : 'N/A' }}
                                    </div>
                                    <div class="text-sm text-purple-700 dark:text-purple-300">
                                        Último uso
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Composición Nutricional -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Composición Nutricional</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $tipoAlimento->proteina ? $tipoAlimento->proteina . '%' : 'N/A' }}
                            </div>
                            <div class="text-sm text-blue-700 dark:text-blue-300">Proteína</div>
                        </div>
                        
                        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ $tipoAlimento->grasa ? $tipoAlimento->grasa . '%' : 'N/A' }}
                            </div>
                            <div class="text-sm text-green-700 dark:text-green-300">Grasa</div>
                        </div>
                        
                        <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                {{ $tipoAlimento->fibra ? $tipoAlimento->fibra . '%' : 'N/A' }}
                            </div>
                            <div class="text-sm text-yellow-700 dark:text-yellow-300">Fibra</div>
                        </div>
                        
                        <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                {{ $tipoAlimento->humedad ? $tipoAlimento->humedad . '%' : 'N/A' }}
                            </div>
                            <div class="text-sm text-purple-700 dark:text-purple-300">Humedad</div>
                        </div>
                        
                        <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                                {{ $tipoAlimento->ceniza ? $tipoAlimento->ceniza . '%' : 'N/A' }}
                            </div>
                            <div class="text-sm text-red-700 dark:text-red-300">Ceniza</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Presentación y Costos -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Presentación y Costos</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Presentación</label>
                            <p class="text-lg text-gray-900 dark:text-gray-100">
                                {{ $tipoAlimento->presentacion ? App\Models\TipoAlimento::getPresentaciones()[$tipoAlimento->presentacion] : 'No especificada' }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Peso por Presentación</label>
                            <p class="text-lg text-gray-900 dark:text-gray-100">
                                {{ $tipoAlimento->peso_presentacion ? $tipoAlimento->peso_presentacion . ' kg' : 'No especificado' }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Costo por Kg</label>
                            <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                {{ $tipoAlimento->costo_por_kg ? '$' . number_format($tipoAlimento->costo_por_kg, 2) : 'No definido' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Descripción -->
            @if($tipoAlimento->descripcion)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Descripción</h3>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $tipoAlimento->descripcion }}</p>
                    </div>
                </div>
            @endif
            
            <!-- Historial de Uso Reciente -->
            @if($tipoAlimento->alimentaciones->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Historial de Uso Reciente</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Fecha
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Lote
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Unidad
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Cantidad
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Costo
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($tipoAlimento->alimentaciones->take(10) as $alimentacion)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ \Carbon\Carbon::parse($alimentacion->fecha_alimentacion)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $alimentacion->lote->codigo ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ $alimentacion->lote->unidadProduccion->codigo ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $alimentacion->cantidad_kg }} kg
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                ${{ number_format($alimentacion->costo_total, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($tipoAlimento->alimentaciones->count() > 10)
                            <div class="mt-4 text-center">
                                <a href="{{ route('alimentacion.index') }}?tipo_alimento_id={{ $tipoAlimento->id }}" 
                                   class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    Ver todos los registros ({{ $tipoAlimento->alimentaciones->count() }})
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
