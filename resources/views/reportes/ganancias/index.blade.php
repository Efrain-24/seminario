@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">Reporte de Ganancias por Lote</h1>
        <div class="bg-white dark:bg-gray-800 shadow rounded p-6">
            <form method="GET" action="{{ route('reportes.ganancias') }}">
                <div class="mb-4">
                    <label for="unidad" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unidad de Producción</label>
                    <select name="unidad" id="unidad" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todas</option>
                        @foreach($unidades as $unidad)
                            <option value="{{ $unidad->id }}" {{ request('unidad') == $unidad->id ? 'selected' : '' }}>{{ $unidad->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filtrar</button>
            </form>
        </div>

        @if($resumen && $loteResumen)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 mt-8">
                <h3 class="text-lg font-bold mb-4">Resumen y Gráfica del Lote</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <p><strong>Gasto en Alimentación:</strong> Q{{ number_format($resumen['costoAlimentacion'], 2) }}</p>
                        <p><strong>Gasto en Protocolos:</strong> Q{{ number_format($resumen['costoProtocolos'], 2) }}</p>
                        <p><strong>Total de Venta:</strong> Q{{ number_format($resumen['totalVenta'], 2) }}</p>
                        <p><strong>Total de Costos:</strong> Q{{ number_format($resumen['totalCostos'], 2) }}</p>
                        <p><strong>Margen:</strong> {{ number_format($resumen['margen'], 2) }}%</p>
                        <p><strong>Estado:</strong> @if($resumen['ganancia'] >= 0)<span class="text-green-600 font-bold">Ganancia</span>@else<span class="text-red-600 font-bold">Pérdida</span>@endif</p>
                        @if(!$resumen['vendido'] && $resumen['estimado'])
                            <p><strong>Venta Potencial:</strong> Q{{ number_format($resumen['estimado'], 2) }}</p>
                        @endif
                    </div>
                    <div>
                        <canvas id="loteChart" height="120"></canvas>
                    </div>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('loteChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($grafica['labels']) !!},
                        datasets: [{
                            label: 'Monto (Q)',
                            data: {!! json_encode($grafica['data']) !!},
                            backgroundColor: [
                                '#22d3ee', '#f59e42', '#a3a3a3', '#22c55e', '#f43f5e'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            title: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            </script>
        @endif
        <div class="mt-8">
            <table class="min-w-full bg-white dark:bg-gray-800 rounded shadow">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Tanque/Lote</th>
                        <th class="px-4 py-2">Especie</th>
                        <th class="px-4 py-2">Fecha Inicio</th>
                        <th class="px-4 py-2">Costo Total</th>
                        <th class="px-4 py-2">Ventas</th>
                        <th class="px-4 py-2">Ganancia Real</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lotes as $lote)
                        <tr>
                            <td class="border px-4 py-2">{{ $lote->codigo_lote }}</td>
                            <td class="border px-4 py-2">{{ $lote->especie }}</td>
                            <td class="border px-4 py-2">{{ $lote->fecha_inicio ? $lote->fecha_inicio->format('d/m/Y') : '-' }}</td>
                            <td class="border px-4 py-2">Q{{ number_format($lote->costo_total, 2) }}</td>
                            <td class="border px-4 py-2">Q{{ number_format($lote->ventas_total, 2) }}</td>
                            <td class="border px-4 py-2 font-bold">Q{{ number_format($lote->ganancia_real, 2) }}</td>
                            <td class="border px-4 py-2">
                                <a href="{{ route('reportes.ganancias.reporte', $lote->id) }}" class="text-blue-600 hover:underline">Ver Detalle</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Reportes de Ganancias')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">
            📊 Reportes de Ganancias por Lote
        </h1>
        <p class="text-gray-600 mb-6">
            Análisis detallado de costos y ganancias por lote de producción
        </p>

        <!-- Filtros -->
        <form method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lote</label>
                    <select name="lote_id" class="form-control">
                        <option value="">Seleccionar lote</option>
                        @foreach($lotes as $lote)
                            <option value="{{ $lote->id }}" {{ request('lote_id') == $lote->id ? 'selected' : '' }}>
                                {{ $lote->codigo }} - {{ $lote->unidadProduccion->nombre ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Generar Reporte
                    </button>
                </div>
            </div>
        </form>

        <!-- Lista de lotes disponibles -->
        @if(!request('lote_id'))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($lotes as $lote)
            <div class="bg-gray-50 border rounded-lg p-4 hover:shadow-md transition-shadow">
                <h3 class="font-semibold text-lg text-gray-800">{{ $lote->codigo }}</h3>
                <p class="text-gray-600 text-sm">Tanque: {{ $lote->unidadProduccion->nombre ?? 'N/A' }}</p>
                <p class="text-gray-600 text-sm">Fecha siembra: {{ $lote->fecha_siembra ? $lote->fecha_siembra->format('d/m/Y') : 'N/A' }}</p>
                <div class="mt-3">
                    <a href="{{ route('reportes.ganancias', ['lote_id' => $lote->id]) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                        Ver Reporte
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
