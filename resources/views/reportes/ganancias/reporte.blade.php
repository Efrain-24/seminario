@extends('layouts.app')

@section('title', 'Reporte de Ganancias - ' . $lote->codigo)

@section('content')

<!-- 🔔 Notificaciones -->
<x-notification type="success" :message="session('success')" />
<x-notification type="error" :message="session('error')" />
<x-notification type="warning" :message="session('warning')" />

<div class="container mx-auto py-8">
    <!-- 🔍 Selector de lote -->
    <form method="GET" action="{{ route('reportes.ganancias') }}" class="mb-6 bg-gray-50 p-4 rounded-lg flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanque/Lote</label>
            <select name="lote_id" class="form-control">
                <option value="">Seleccionar lote</option>
                @foreach($lotes as $l)
                    <option value="{{ $l->id }}" {{ $lote->id == $l->id ? 'selected' : '' }}>
                        {{ $l->codigo }} - {{ $l->unidadProduccion->nombre ?? 'N/A' }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Ver Reporte</button>
    </form>

    <!-- 🧾 Encabezado del reporte -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    📈 Reporte de Ganancias
                </h1>
                <h2 class="text-xl text-gray-600">Lote: {{ $lote->codigo }}</h2>
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

    <!-- 💹 Resumen financiero -->
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

    <!-- 💰 Desglose de costos -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Desglose de Costos</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <table class="w-full">
                    <tbody class="divide-y divide-gray-200">
                        <tr><td class="py-3 text-gray-700">Compra Lote</td><td class="py-3 text-right">Q{{ number_format($desglose['precio_compra_lote'], 2) }}</td></tr>
                        <tr><td class="py-3 text-gray-700">Alimentación</td><td class="py-3 text-right">Q{{ number_format($desglose['total_alimentacion'], 2) }}</td></tr>
                        <tr><td class="py-3 text-gray-700">Mantenimientos</td><td class="py-3 text-right">Q{{ number_format($desglose['total_mantenimientos'], 2) }}</td></tr>
                        <tr><td class="py-3 text-gray-700">Limpiezas</td><td class="py-3 text-right">Q{{ number_format($desglose['total_limpiezas'], 2) }}</td></tr>
                        <tr class="border-t-2 border-gray-300 font-bold">
                            <td class="py-3 text-gray-800">Total Costos</td>
                            <td class="py-3 text-right text-red-600">Q{{ number_format($desglose['total_costos'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <canvas id="costosChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- 🐟 Detalle de Alimentación -->
    @if($alimentacionDetalle->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Detalle de Alimentación</h3>
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Producto</th>
                        <th class="px-4 py-2 text-right">Cantidad</th>
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

    <!-- 🔧 Mantenimientos -->
    @if($mantenimientoDetalle->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Mantenimientos</h3>
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2">Fecha</th>
                        <th class="px-4 py-2">Tipo</th>
                        <th class="px-4 py-2">Descripción</th>
                        <th class="px-4 py-2 text-right">Costo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($mantenimientoDetalle as $m)
                    <tr>
                        <td class="px-4 py-2">{{ $m['fecha'] }}</td>
                        <td class="px-4 py-2">{{ $m['tipo'] }}</td>
                        <td class="px-4 py-2">{{ $m['descripcion'] }}</td>
                        <td class="px-4 py-2 text-right">Q{{ number_format($m['costo'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- 🧽 Limpiezas -->
    @if($limpiezaDetalle->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Limpiezas</h3>
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2">Fecha</th>
                        <th class="px-4 py-2">Tipo</th>
                        <th class="px-4 py-2">Productos</th>
                        <th class="px-4 py-2 text-right">Costo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($limpiezaDetalle as $l)
                    <tr>
                        <td class="px-4 py-2">{{ $l['fecha'] }}</td>
                        <td class="px-4 py-2">{{ $l['tipo'] }}</td>
                        <td class="px-4 py-2">{{ $l['productos'] }}</td>
                        <td class="px-4 py-2 text-right">Q{{ number_format($l['costo'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- 💰 Ventas -->
    @if($ventasDetalle->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Ventas</h3>
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2">Fecha</th>
                        <th class="px-4 py-2">Código</th>
                        <th class="px-4 py-2">Cliente</th>
                        <th class="px-4 py-2 text-right">Peso (kg)</th>
                        <th class="px-4 py-2 text-right">Precio/kg</th>
                        <th class="px-4 py-2 text-right">Total</th>
                        <th class="px-4 py-2 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($ventasDetalle as $v)
                    <tr>
                        <td class="px-4 py-2">{{ $v['fecha'] ? \Carbon\Carbon::parse($v['fecha'])->format('d/m/Y') : 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $v['codigo'] ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $v['cliente'] ?? 'N/A' }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($v['peso_kg'], 2) }}</td>
                        <td class="px-4 py-2 text-right">Q{{ number_format($v['precio_kg'], 2) }}</td>
                        <td class="px-4 py-2 text-right font-semibold">Q{{ number_format($v['total'], 2) }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                {{ $v['estado'] == 'completada' ? 'bg-green-100 text-green-800' : 
                                   ($v['estado'] == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($v['estado'] ?? 'N/A') }}
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
        <p class="text-sm text-yellow-700">
            ⚠️ <strong>Sin ventas registradas:</strong> No se encontraron ventas para este lote. Registre ventas para ver un reporte completo.
        </p>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('costosChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Compra', 'Alimentación', 'Mantenimientos', 'Limpiezas'],
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
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endsection
