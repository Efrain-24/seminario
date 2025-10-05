import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Inicializar Lucide Icons cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Usar lucide del CDN
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

// También inicializar después de cambios dinámicos (útil para Alpine.js)
document.addEventListener('alpine:initialized', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
