@extends('layouts.app')

@section('title', 'Reporte de Ganancias - ' . $lote->codigo)

@section('content')
@section('content')

<!-- Notificaciones -->
<x-notification type="success" :message="session('success')" />
<x-notification type="error" :message="session('error')" />
<x-notification type="warning" :message="session('warning')" />

<div class="container mx-auto py-8">
    <!-- Encabezado del reporte -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    📈 Reporte de Ganancias
                </h1>
                <h2 class="text-xl text-gray-600">Lote: {{ $lote->codigo_lote ?? $lote->codigo }}</h2>
                <p class="text-gray-500">Tanque: {{ $lote->unidadProduccion->nombre ?? 'N/A' }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
                <a href="{{ route('reportes.ganancias') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                    ← Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Resumen financiero -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800">Total Ingresos</h3>
            <p class="text-2xl font-bold text-blue-600">Q{{ number_format($desglose['total_ventas'], 2) }}</p>
        </div>
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-red-800">Total Costos</h3>
            <p class="text-2xl font-bold text-red-600">Q{{ number_format($desglose['total_costos'], 2) }}</p>
        </div>
        <div class="bg-{{ $desglose['ganancia_real'] >= 0 ? 'green' : 'red' }}-50 border-l-4 border-{{ $desglose['ganancia_real'] >= 0 ? 'green' : 'red' }}-400 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-{{ $desglose['ganancia_real'] >= 0 ? 'green' : 'red' }}-800">
                {{ $desglose['ganancia_real'] >= 0 ? 'Ganancia' : 'Pérdida' }}
            </h3>
            <p class="text-2xl font-bold text-{{ $desglose['ganancia_real'] >= 0 ? 'green' : 'red' }}-600">
                Q{{ number_format($desglose['ganancia_real'], 2) }}
            </p>
        </div>
        <div class="bg-purple-50 border-l-4 border-purple-400 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-purple-800">Margen</h3>
            <p class="text-2xl font-bold text-purple-600">{{ number_format($desglose['margen_ganancia'], 1) }}%</p>
        </div>
    </div>

    <!-- Desglose de costos -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">💰 Desglose de Costos</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <table class="w-full">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="py-3 text-gray-700">Precio compra lote (alevines)</td>
                            <td class="py-3 text-right font-medium">Q{{ number_format($desglose['precio_compra_lote'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-gray-700">Alimentación</td>
                            <td class="py-3 text-right font-medium">Q{{ number_format($desglose['total_alimentacion'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-gray-700">Mantenimientos</td>
                            <td class="py-3 text-right font-medium">Q{{ number_format($desglose['total_mantenimientos'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-gray-700">Limpiezas</td>
                            <td class="py-3 text-right font-medium">Q{{ number_format($desglose['total_limpiezas'], 2) }}</td>
                        </tr>
                        <tr class="border-t-2 border-gray-300">
                            <td class="py-3 font-bold text-gray-800">Total Costos</td>
                            <td class="py-3 text-right font-bold text-red-600">Q{{ number_format($desglose['total_costos'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <canvas id="costosChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Detalle de alimentación -->
    @if($alimentacionDetalle->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">🐟 Detalle de Alimentación</h3>
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Producto</th>
                        <th class="px-4 py-2 text-right">Cantidad (lb)</th>
                        <th class="px-4 py-2 text-right">Costo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($alimentacionDetalle as $alimentacion)
                    <tr>
                        <td class="px-4 py-2">{{ $alimentacion['fecha'] }}</td>
                        <td class="px-4 py-2">{{ $alimentacion['producto'] }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($alimentacion['cantidad'], 2) }}</td>
                        <td class="px-4 py-2 text-right">Q{{ number_format($alimentacion['costo'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Detalle de mantenimientos -->
    @if($mantenimientoDetalle->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">🔧 Detalle de Mantenimientos</h3>
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Tipo</th>
                        <th class="px-4 py-2 text-left">Descripción</th>
                        <th class="px-4 py-2 text-right">Costo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($mantenimientoDetalle as $mantenimiento)
                    <tr>
                        <td class="px-4 py-2">{{ $mantenimiento['fecha'] }}</td>
                        <td class="px-4 py-2">{{ $mantenimiento['tipo'] }}</td>
                        <td class="px-4 py-2">{{ $mantenimiento['descripcion'] }}</td>
                        <td class="px-4 py-2 text-right">Q{{ number_format($mantenimiento['costo'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Detalle de limpiezas -->
    @if($limpiezaDetalle->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">🧽 Detalle de Limpiezas</h3>
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Tipo</th>
                        <th class="px-4 py-2 text-left">Productos</th>
                        <th class="px-4 py-2 text-right">Costo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($limpiezaDetalle as $limpieza)
                    <tr>
                        <td class="px-4 py-2">{{ $limpieza['fecha'] }}</td>
                        <td class="px-4 py-2">{{ $limpieza['tipo'] }}</td>
                        <td class="px-4 py-2">{{ $limpieza['productos'] }}</td>
                        <td class="px-4 py-2 text-right">Q{{ number_format($limpieza['costo'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Detalle de ventas -->
    @if($ventasDetalle->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">💰 Detalle de Ventas</h3>
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Código</th>
                        <th class="px-4 py-2 text-left">Cliente</th>
                        <th class="px-4 py-2 text-right">Peso (kg)</th>
                        <th class="px-4 py-2 text-right">Precio/kg</th>
                        <th class="px-4 py-2 text-right">Total</th>
                        <th class="px-4 py-2 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($ventasDetalle as $venta)
                    <tr>
                        <td class="px-4 py-2">{{ $venta['fecha'] ? \Carbon\Carbon::parse($venta['fecha'])->format('d/m/Y') : 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $venta['codigo'] ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $venta['cliente'] ?? 'N/A' }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($venta['peso_kg'], 2) }}</td>
                        <td class="px-4 py-2 text-right">Q{{ number_format($venta['precio_kg'], 2) }}</td>
                        <td class="px-4 py-2 text-right font-semibold">Q{{ number_format($venta['total'], 2) }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                {{ $venta['estado'] == 'completada' ? 'bg-green-100 text-green-800' : 
                                   ($venta['estado'] == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($venta['estado'] ?? 'N/A') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>Sin ventas registradas:</strong> No se encontraron ventas para este lote. Para generar un reporte completo, registre las ventas del lote.
                </p>
            </div>
        </div>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de distribución de costos
const ctx = document.getElementById('costosChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Compra Lote', 'Alimentación', 'Mantenimientos', 'Limpiezas'],
        datasets: [{
            data: [
                {{ $desglose['precio_compra_lote'] }},
                {{ $desglose['total_alimentacion'] }},
                {{ $desglose['total_mantenimientos'] }},
                {{ $desglose['total_limpiezas'] }}
            ],
            backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection