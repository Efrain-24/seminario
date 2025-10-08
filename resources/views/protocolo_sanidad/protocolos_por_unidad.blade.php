@extends('layouts.app')
@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-4">Protocolos asignados a la unidad: {{ $unidad->codigo }}</h2>
    @if($protocolos->count() === 0)
        <div class="bg-white p-6 rounded shadow text-gray-500">No hay protocolos asignados a esta unidad.</div>
    @else
        @foreach($protocolos as $protocolo)
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-2">
                    <span class="font-semibold text-indigo-700">{{ $protocolo->nombre }}</span>
                    <span class="text-xs px-2 py-1 rounded bg-gray-100">v{{ $protocolo->version }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $protocolo->estado === 'vigente' ? 'bg-green-100 text-green-800' : ($protocolo->estado === 'obsoleta' ? 'bg-gray-200 text-gray-800' : 'bg-blue-100 text-blue-800') }}">{{ ucfirst($protocolo->estado) }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-sm text-gray-600">Responsable: {{ $protocolo->responsable }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-sm text-gray-600">Fecha de implementación: {{ $protocolo->fecha_implementacion }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-sm text-gray-600">Actividades:</span>
                    <ul class="list-disc ml-6">
                        @foreach($protocolo->actividades_normalizadas as $act)
                            <li>{{ $act }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="mt-4">
                    <h4 class="font-semibold mb-2">Registros de limpieza ejecutados con este protocolo:</h4>
                    @if($limpiezasPorProtocolo[$protocolo->id]->count() === 0)
                        <div class="text-gray-500">No hay registros históricos para este protocolo en la unidad.</div>
                    @else
                        <table class="min-w-full bg-white border rounded">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left">Fecha</th>
                                    <th class="px-4 py-2 text-left">Responsable</th>
                                    <th class="px-4 py-2 text-left">Estado</th>
                                    <th class="px-4 py-2 text-left">Actividades ejecutadas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($limpiezasPorProtocolo[$protocolo->id] as $limpieza)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">{{ $limpieza->fecha }}</td>
                                        <td class="px-4 py-2">{{ $limpieza->responsable }}</td>
                                        <td class="px-4 py-2">{{ ucfirst($limpieza->estado) }}</td>
                                        <td class="px-4 py-2">
                                            <ul class="list-decimal ml-4">
                                                @foreach($limpieza->actividades_normalizadas as $act)
                                                    <li>{{ is_array($act) ? ($act['descripcion'] ?? $act) : $act }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
    <a href="{{ route('protocolo-sanidad.index') }}" class="mt-6 inline-block px-4 py-2 bg-gray-600 text-white rounded">Volver a protocolos</a>
</div>
@endsection
