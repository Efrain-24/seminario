<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de Acción Correctiva
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                <div class="mb-4">
                    <strong>Título:</strong> {{ $accion->titulo }}
                </div>
                <div class="mb-4">
                    <strong>Descripción:</strong> {{ $accion->descripcion }}
                </div>
                <div class="mb-4">
                    <strong>Responsable:</strong> {{ $accion->responsable ? $accion->responsable->name : '-' }}
                </div>
                <div class="mb-4">
                    <strong>Fecha Detectada:</strong> {{ $accion->fecha_detectada }}
                </div>
                <div class="mb-4">
                    <strong>Fecha Límite:</strong> {{ $accion->fecha_limite }}
                </div>
                <div class="mb-4">
                    <strong>Estado:</strong> {{ $accion->estado }}
                </div>
                <div class="mb-4">
                    <strong>Observaciones:</strong> {{ $accion->observaciones }}
                </div>
                <a href="{{ route('acciones_correctivas.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Volver</a>
            </div>
        </div>
    </div>
</x-app-layout>
