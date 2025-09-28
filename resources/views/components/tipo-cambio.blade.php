@php
    use App\Models\TipoCambio;
    $tipoCambio = TipoCambio::ultimoDisponible();
    $diferenciaDias = $tipoCambio ? now()->diffInDays($tipoCambio->fecha) : 0;
@endphp

<div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-4 border border-green-200 dark:border-green-700">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">Tipo de Cambio USD</h4>
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    @if($tipoCambio)
                        {{ $tipoCambio->fecha->format('d/m/Y') }}
                        @if($diferenciaDias > 0)
                            <span class="text-amber-600 dark:text-amber-400">({{ $diferenciaDias }} días)</span>
                        @endif
                    @else
                        Sin datos
                    @endif
                </p>
            </div>
        </div>
        
        <div class="text-right">
            @if($tipoCambio)
                <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ $tipoCambio->valor_formateado }}
                </div>
                <div class="text-xs text-gray-600 dark:text-gray-400">
                    por US$1.00
                </div>
            @else
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    No disponible
                </div>
            @endif
        </div>
    </div>
    
    <!-- Botón para actualizar -->
    <div class="mt-3 pt-3 border-t border-green-200 dark:border-green-700">
        <button onclick="actualizarTipoCambio()" 
                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md transition-colors duration-200">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Actualizar
        </button>
        
        @if($diferenciaDias > 1)
            <span class="ml-2 text-xs text-amber-600 dark:text-amber-400">
                ⚠️ Tipo de cambio desactualizado
            </span>
        @endif
    </div>
</div>

<script>
function actualizarTipoCambio() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    
    // Mostrar loading
    btn.innerHTML = '<svg class="w-3 h-3 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Actualizando...';
    btn.disabled = true;
    
    // Hacer petición AJAX para actualizar
    fetch('/api/tipo-cambio/test-actualizar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje personalizado según el estado
            if (data.status === 'warning') {
                alert('⚠️ ' + data.message + '\n\n' + (data.data?.detalle || ''));
            } else {
                alert('✅ ' + data.message);
            }
            // Recargar la página para mostrar el nuevo valor
            window.location.reload();
        } else {
            alert('❌ Error al actualizar: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el tipo de cambio');
    })
    .finally(() => {
        // Restaurar botón
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}
</script>