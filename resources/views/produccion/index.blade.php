<x-ap    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-12">ayout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Módulo de Producción
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Tarjeta Gestión de Lotes -->
            <div class="bg-gray-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-600 rounded-full p-2">
                        <i class="fas fa-layer-group fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Gestión de Lotes</h3>
                        <p class="text-sm text-gray-300">Talla, peso y cantidad por lote.</p>
                    </div>
                </div>
                <a href="{{ route('produccion.lotes') }}"
                    class="mt-4 inline-block text-blue-400 hover:underline">Acceder al módulo →</a>
            </div>

            <!-- Tarjeta Traslados entre Tanques -->
            <div class="bg-gray-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-600 rounded-full p-2">
                        <i class="fas fa-exchange-alt fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Traslados</h3>
                        <p class="text-sm text-gray-300">Movimientos entre tanques.</p>
                    </div>
                </div>
                <a href="{{ route('produccion.traslados') }}"
                    class="mt-4 inline-block text-blue-400 hover:underline">Acceder al módulo →</a>
            </div>

            <!-- Tarjeta Seguimiento de Lotes -->
            <div class="bg-gray-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-600 rounded-full p-2">
                        <i class="fas fa-ruler fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Seguimiento de Lotes</h3>
                        <p class="text-sm text-gray-300">Talla/peso, cálculo de biomasa.</p>
                    </div>
                </div>
                <a href="{{ route('produccion.seguimiento.lotes') }}"
                    class="mt-4 inline-block text-blue-400 hover:underline">Acceder al módulo →</a>
            </div>


        </div>
    </div>
</x-app-layout>
