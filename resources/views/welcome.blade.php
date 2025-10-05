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
                                href="{{ route('aplicaciones') }}"
                                class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200"
                            >
                                Ir a Aplicaciones
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
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-12">
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

                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl border border-gray-200 dark:border-gray-700">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Módulos del Sistema</h3>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-4">
                                <div class="bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Gestión de Producción</h4>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm">Control de lotes, unidades de producción y mantenimiento de instalaciones.</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="bg-green-100 dark:bg-green-900 rounded-lg p-3">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Sistema de Inventario</h4>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm">Control de alimentos, insumos, bodegas y movimientos de inventario.</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Sistema de Alimentación</h4>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm">Programación y seguimiento de alimentación de los cultivos.</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="bg-purple-100 dark:bg-purple-900 rounded-lg p-3">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Monitoreo y Control</h4>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm">Seguimiento de mortalidad, cosechas parciales y alertas del sistema.</p>
                                </div>
                            </div>
                        </div>
                        
                        @guest
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-center text-gray-600 dark:text-gray-300 mb-4">¿Listo para comenzar?</p>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <a href="{{ route('login') }}" 
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-3 px-4 rounded-lg font-medium transition-colors duration-200">
                                    Iniciar Sesión
                                </a>
                                @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="flex-1 border border-blue-600 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900 dark:text-blue-400 text-center py-3 px-4 rounded-lg font-medium transition-colors duration-200">
                                    Registrarse
                                </a>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 text-center">
                            <p class="text-gray-600 dark:text-gray-300 mb-4">¡Bienvenido de nuevo, {{ Auth::user() ? Auth::user()->name : 'Usuario' }}!</p>
                            <a href="{{ route('aplicaciones') }}" 
                               class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-medium transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                                Acceder a las Aplicaciones
                            </a>
                        </div>
                        @endguest
                    </div>
                </div>

                <!-- Características del sistema -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 text-center">
                        <div class="bg-blue-100 dark:bg-blue-900 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Gestión Eficiente</h3>
                        <p class="text-gray-600 dark:text-gray-300">Automatiza procesos y optimiza la productividad de tus cultivos acuícolas.</p>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 text-center">
                        <div class="bg-green-100 dark:bg-green-900 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Análisis Detallado</h3>
                        <p class="text-gray-600 dark:text-gray-300">Obtén informes y métricas precisas para tomar decisiones informadas.</p>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 text-center">
                        <div class="bg-purple-100 dark:bg-purple-900 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Seguro y Confiable</h3>
                        <p class="text-gray-600 dark:text-gray-300">Protección de datos y acceso controlado con roles y permisos personalizados.</p>
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
