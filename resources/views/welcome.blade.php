<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistema de Gestión Piscícola - Beyond Learning</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        
        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-gray-900 dark:to-gray-800 min-h-screen">
        <header class="w-full px-6 py-4 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <!-- Logo en la esquina superior izquierda -->
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-9 w-auto" />
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Sistema de Gestión Piscícola</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Beyond Learning</p>
                    </div>
                </div>
                
                <!-- Navegación -->
                @if (Route::has('login'))
                    <nav class="flex items-center gap-4">
                        @auth
                            <a
                                href="{{ url('/dashboard') }}"
                                class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200"
                            >
                                Dashboard
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex items-center px-6 py-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-200"
                            >
                                Iniciar Sesión
                            </a>

                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200"
                                >
                                    Registrarse
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </header>
        
        <!-- Contenido Principal -->
        <main class="flex-1 flex items-center justify-center px-6 py-12">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                        Gestión Integral de 
                        <span class="text-blue-600 dark:text-blue-400">Cultivos Piscícolas</span>
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                        Bienvenido a nuestra plataforma dedicada a la <strong>gestión integral de cultivos piscícolas</strong>. 
                        La piscicultura representa una alternativa sostenible para la producción de alimentos de alto valor nutricional.
                    </p>
                </div>

                <!-- Sección informativa -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">¿Qué es la Piscicultura?</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                            La piscicultura es el cultivo controlado de especies acuáticas con fines comerciales o de conservación. 
                            Esta actividad permite la producción eficiente de proteína animal de alta calidad mientras se reduce 
                            la presión sobre las poblaciones naturales de peces.
                        </p>
                        
                        <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Beneficios principales:</h4>
                        <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                            <li class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Producción de alimento rico en proteínas y ácidos grasos esenciales</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Uso eficiente de los recursos hídricos</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Menor impacto ambiental que otras formas de producción animal</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Generación de empleo en zonas rurales</span>
                            </li>
                        </ul>
                    </div>
                    
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 py-8">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <p class="text-gray-600 dark:text-gray-400">
                    © 2025 Beyond Learning - Sistema de Gestión Piscícola. Todos los derechos reservados.
                </p>
            </div>
        </footer>
    </body>
</html>
