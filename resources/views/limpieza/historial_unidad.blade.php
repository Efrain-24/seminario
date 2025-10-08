@extends('layouts.app')
@section('content')

<!-- Notificaciones -->
<x-notification type="success" :message="session('success')" />
<x-notification type="error" :message="session('error')" />
<x-notification type="warning" :message="session('warning')" />

<div class="max-w-5xl mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Historial de Limpiezas - Unidad: {{ $unidad->codigo }}</h2>
    <div class="bg-white shadow rounded-lg p-6">
        @if($limpiezas->count() > 0)
        <table class="min-w-full text-sm text-gray-800">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Fecha</th>
                    <th class="px-4 py-2 text-left">Responsable</th>
                    <th class="px-4 py-2 text-left">Protocolo</th>
                    <th class="px-4 py-2 text-left">Estado</th>
                    <th class="px-4 py-2 text-left">Progreso</th>
                    <th class="px-4 py-2 text-right"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($limpiezas as $limpieza)
                <tr class="border-t hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('limpieza.show', $limpieza) }}'">
                    <td class="px-4 py-2">{{ $limpieza->fecha }}</td>
                    <td class="px-4 py-2">{{ $limpieza->responsable }}</td>
                    <td class="px-4 py-2">{{ $limpieza->protocoloSanidad->nombre_completo ?? '' }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs rounded font-medium {{ $limpieza->estado === 'completado' ? 'bg-green-100 text-green-800' : ($limpieza->estado === 'en_progreso' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($limpieza->estado) }}
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        @php
                            $norm = $limpieza->actividades_normalizadas;
                            $totalActividades = $norm ? count($norm) : 0;
                            $actividadesCompletadas = $norm ? collect($norm)->where('completada', true)->count() : 0;
                            $porcentaje = $totalActividades > 0 ? round(($actividadesCompletadas / $totalActividades) * 100) : 0;
                        @endphp
                        <div class="flex items-center gap-2">
                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $porcentaje }}%"></div>
                            </div>
                            <span class="text-xs text-gray-600">{{ $actividadesCompletadas }}/{{ $totalActividades }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-2 text-right">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-gray-500 text-center">No hay registros de limpieza para esta unidad.</div>
        @endif
    </div>
</div>
@endsection
