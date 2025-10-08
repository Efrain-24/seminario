@extends('layouts.app')

@section('content')

<!-- Notificaciones -->
<x-notification type="success" :message="session('success')" />
<x-notification type="error" :message="session('error')" />
<x-notification type="warning" :message="session('warning')" />

<div class="max-w-4xl mx-auto py-8 px-4">
    <h2 class="text-2xl font-bold mb-4">Log de Mortalidad del Lote: {{ $lote->nombre ?? $lote->codigo_lote }}</h2>
    <div class="mb-4">
        <a href="{{ route('produccion.lotes') }}" class="text-blue-600 hover:underline">&larr; Volver a lotes</a>
    </div>
    <div class="bg-white shadow rounded p-4">
        <table class="min-w-full text-sm">
            <thead>
                <tr>
                    <th class="px-2 py-1">Fecha</th>
                    <th class="px-2 py-1">Cantidad</th>
                    <th class="px-2 py-1">Causa</th>
                    <th class="px-2 py-1">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mortalidades as $m)
                <tr class="border-b">
                    <td class="px-2 py-1">{{ $m->fecha->format('Y-m-d') }}</td>
                    <td class="px-2 py-1">{{ $m->cantidad }}</td>
                    <td class="px-2 py-1">{{ $m->causa ?? '—' }}</td>
                    <td class="px-2 py-1">{{ $m->observaciones ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4">No hay registros de mortalidad para este lote.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
