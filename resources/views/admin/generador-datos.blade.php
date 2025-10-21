@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-database mr-3 text-blue-600"></i>
                Generador de Datos de Prueba
            </h1>
            <div class="text-sm text-gray-500">
                Sistema para poblar la base de datos con información realista
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Generador de Cosechas -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-fish mr-2 text-green-600"></i>
                    Generar Cosechas de Prueba
                </h2>
                
                <form action="{{ route('admin.generador.cosechas') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="lote_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Seleccionar Lote
                        </label>
                        <select name="lote_id" id="lote_id" required 
                                class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Selecciona un lote --</option>
                            @foreach($lotes as $lote)
                                <option value="{{ $lote->id }}">
                                    {{ $lote->codigo_lote }} - {{ $lote->especie ?? 'Sin especie' }}
                                    ({{ $lote->cantidad_inicial }} peces)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="num_cosechas" class="block text-sm font-medium text-gray-700 mb-2">
                                Número de Cosechas
                            </label>
                            <input type="number" name="num_cosechas" id="num_cosechas" 
                                   value="5" min="1" max="20" required
                                   class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="periodo_dias" class="block text-sm font-medium text-gray-700 mb-2">
                                Período (días atrás)
                            </label>
                            <input type="number" name="periodo_dias" id="periodo_dias" 
                                   value="90" min="30" max="365" required
                                   class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 transition duration-200 font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Generar Cosechas
                    </button>
                </form>
            </div>

            <!-- Generador de Ventas -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-shopping-cart mr-2 text-purple-600"></i>
                    Generar Ventas de Prueba
                </h2>
                
                <form action="{{ route('admin.generador.ventas') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="lote_ventas_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Seleccionar Lote
                        </label>
                        <select name="lote_id" id="lote_ventas_id" required 
                                class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Selecciona un lote --</option>
                            @foreach($lotes as $lote)
                                <option value="{{ $lote->id }}">
                                    {{ $lote->codigo_lote }} - {{ $lote->especie ?? 'Sin especie' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="num_ventas" class="block text-sm font-medium text-gray-700 mb-2">
                                Número de Ventas
                            </label>
                            <input type="number" name="num_ventas" id="num_ventas" 
                                   value="3" min="1" max="10" required
                                   class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="periodo_ventas_dias" class="block text-sm font-medium text-gray-700 mb-2">
                                Período (días atrás)
                            </label>
                            <input type="number" name="periodo_dias" id="periodo_ventas_dias" 
                                   value="60" min="15" max="180" required
                                   class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-purple-600 text-white py-3 px-4 rounded-md hover:bg-purple-700 transition duration-200 font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Generar Ventas
                    </button>
                </form>
            </div>
        </div>

        <!-- Acciones de Limpieza -->
        <div class="mt-8 bg-red-50 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-red-800 mb-4">
                <i class="fas fa-trash-alt mr-2"></i>
                Limpiar Datos de Prueba
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <form action="{{ route('admin.generador.limpiar-cosechas') }}" method="POST" 
                      onsubmit="return confirm('¿Estás seguro de eliminar todas las cosechas de prueba?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full bg-red-600 text-white py-3 px-4 rounded-md hover:bg-red-700 transition duration-200 font-medium">
                        <i class="fas fa-broom mr-2"></i>
                        Limpiar Cosechas
                    </button>
                </form>

                <form action="{{ route('admin.generador.limpiar-ventas') }}" method="POST" 
                      onsubmit="return confirm('¿Estás seguro de eliminar todas las ventas de prueba?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full bg-red-600 text-white py-3 px-4 rounded-md hover:bg-red-700 transition duration-200 font-medium">
                        <i class="fas fa-broom mr-2"></i>
                        Limpiar Ventas
                    </button>
                </form>
            </div>
            
            <div class="mt-4 text-sm text-red-600">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                <strong>Precaución:</strong> Esta acción eliminará permanentemente todos los datos de prueba generados.
            </div>
        </div>

        <!-- Estadísticas Actuales -->
        <div class="mt-8 bg-blue-50 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-blue-800 mb-4">
                <i class="fas fa-chart-bar mr-2"></i>
                Estadísticas de Datos de Prueba
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['cosechas'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Cosechas de Prueba</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['ventas'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Ventas de Prueba</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['lotes_con_datos'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Lotes con Datos</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ number_format($stats['total_kg'] ?? 0, 1) }} kg</div>
                    <div class="text-sm text-gray-600">Total Cosechado</div>
                </div>
            </div>
        </div>

        <!-- Instrucciones -->
        <div class="mt-8 bg-yellow-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-yellow-800 mb-3">
                <i class="fas fa-info-circle mr-2"></i>
                Instrucciones de Uso
            </h3>
            <ul class="list-disc list-inside text-yellow-700 space-y-2">
                <li>Selecciona un lote activo para generar datos de prueba realistas</li>
                <li>Los datos generados respetan las características de cada especie</li>
                <li>Las cosechas se crean con fechas distribuidas en el período especificado</li>
                <li>Las ventas utilizan clientes aleatorios del sistema</li>
                <li>Los precios se calculan con variaciones realistas del mercado</li>
                <li>Usa las opciones de limpieza para eliminar datos de prueba</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar automáticamente las estadísticas cada 30 segundos
    setInterval(function() {
        window.location.reload();
    }, 30000);
});
</script>
@endsection