<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
            Lote {{ $lote->codigo_lote }} — Predicción de Producción
        </h2>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        {{-- Toolbar: selector de estanque + volver --}}
        <div class="flex flex-col md:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <label for="selector-estanque" class="text-sm text-gray-700 dark:text-gray-200">Estanque</label>
                <select id="selector-estanque"
                    class="w-56 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                               text-gray-900 dark:text-gray-100 p-2"
                    onchange="if(this.value){ window.location.href='{{ route('produccion.control.show', ['lote' => '__ID__']) }}'.replace('__ID__', this.value); }">
                    @foreach ($lotes as $l)
                        <option value="{{ $l->id }}" @selected($l->id === $lote->id)>{{ $l->codigo_lote }}</option>
                    @endforeach
                </select>
            </div>


        </div>

        {{-- Estado estimado hoy --}}
        <section
            class="bg-white/90 dark:bg-slate-800/80 backdrop-blur rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <h3 class="text-base font-semibold mb-3 text-gray-900 dark:text-gray-100">
                Estado estimado hoy ({{ $hoy }})
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-gray-800 dark:text-gray-100">
                <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Cantidad actual</div>
                    <div class="mt-1 text-2xl font-bold">{{ number_format($lote->cantidad_actual) }} <span
                            class="text-base font-medium">peces</span></div>
                </div>
                <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Peso prom. estimado
                    </div>
                    <div class="mt-1 text-2xl font-bold">{{ $peso_hoy ? number_format($peso_hoy, 2) : '—' }} <span
                            class="text-base font-medium">g</span></div>
                </div>
                <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Biomasa estimada</div>
                    <div class="mt-1 text-2xl font-bold">{{ $biomasa_hoy ? number_format($biomasa_hoy, 2) : '—' }} <span
                            class="text-base font-medium">kg</span></div>
                </div>
            </div>
        </section>

        {{-- Predicciones en dos columnas --}}
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
                class="bg-white/90 dark:bg-slate-800/80 backdrop-blur rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                <h3 class="text-base font-semibold mb-3 text-gray-900 dark:text-gray-100">Predicción hasta fecha
                    objetivo</h3>
                <form action="{{ route('produccion.control.pred.fecha', $lote) }}" method="POST"
                    class="flex flex-col sm:flex-row gap-3 items-end">
                    @csrf
                    <div class="flex-1 w-full">
                        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Fecha objetivo</label>
                        <input type="date" name="fecha_objetivo"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                                      text-gray-900 dark:text-gray-100 p-2"
                            required>
                    </div>
                    <button class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Predecir</button>
                </form>
                @if (session('prediccion_fecha'))
                    @php($p = session('prediccion_fecha'))
                    <div class="mt-4 text-gray-800 dark:text-gray-100">
                        <div><b>Días:</b> {{ $p['dias'] }}</div>
                        <div><b>Peso promedio estimado:</b> {{ number_format($p['peso_promedio_g'], 2) }} g</div>
                        <div><b>Biomasa estimada:</b> {{ number_format($p['biomasa_kg'], 2) }} kg</div>
                    </div>
                @endif
            </div>

            <div
                class="bg-white/90 dark:bg-slate-800/80 backdrop-blur rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                <h3 class="text-base font-semibold mb-3 text-gray-900 dark:text-gray-100">Predicción para peso objetivo
                </h3>
                <form action="{{ route('produccion.control.pred.peso', $lote) }}" method="POST"
                    class="flex flex-col sm:flex-row gap-3 items-end">
                    @csrf
                    <div class="flex-1 w-full">
                        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Peso objetivo (g/pez)</label>
                        <input type="number" name="peso_objetivo_g" min="1" step="0.01"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                                      text-gray-900 dark:text-gray-100 p-2"
                            required>
                    </div>
                    <button class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Calcular
                        fecha</button>
                </form>
                @if (session('prediccion_peso'))
                    @php($q = session('prediccion_peso'))
                    <div class="mt-4 text-gray-800 dark:text-gray-100">
                        <div><b>Días:</b> {{ $q['dias'] }}</div>
                        <div><b>Fecha estimada:</b> {{ optional($q['fecha'])->format('Y-m-d') }}</div>
                        <div><b>Biomasa al objetivo:</b> {{ number_format($q['biomasa_kg'], 2) }} kg</div>
                    </div>
                @endif
            </div>
        </section>
        <a href="{{ route('produccion.control.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600
                      bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
            ← Volver al listado
        </a>
    </div>
</x-app-layout>
