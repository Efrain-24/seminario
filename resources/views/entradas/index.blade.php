@extends('layouts.app')
@section('title','Entradas de Inventario')
@section('content')

<!-- Notificaciones -->
<x-notification type="success" :message="session('success')" />
<x-notification type="error" :message="session('error')" />
<x-notification type="warning" :message="session('warning')" />

<div class="container mx-auto max-w-6xl">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Entradas</h1>
        <a href="{{ route('entradas.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Nueva Entrada</a>
    </div>
    <table class="w-full text-sm border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">ID</th>
                <th class="p-2 border">Proveedor</th>
                <th class="p-2 border">Número Doc</th>
                <th class="p-2 border">Fecha Ingreso</th>
                <th class="p-2 border">Total</th>
                <th class="p-2 border"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($entradas as $e)
                <tr class="hover:bg-gray-50">
                    <td class="p-2 border text-center">{{ $e->id }}</td>
                    <td class="p-2 border">{{ $e->proveedor->nombre ?? '-' }}</td>
                    <td class="p-2 border">{{ $e->numero_documento ?? 'Manual' }}</td>
                    <td class="p-2 border">{{ $e->fecha_ingreso?->format('d/m/Y') }}</td>
                    <td class="p-2 border text-right">Q{{ number_format($e->total,2) }}</td>
                    <td class="p-2 border text-center"><a href="{{ route('entradas.show',$e) }}" class="text-blue-600 underline">Ver</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-4 text-center text-gray-500">Sin registros</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">{{ $entradas->links() }}</div>
</div>
@endsection
