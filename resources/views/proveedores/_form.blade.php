@php($proveedor = $proveedor ?? null)

{{-- Información Básica --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Información Básica
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nombre o Razón Social <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('nombre') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                       id="nombre" name="nombre" value="{{ old('nombre', $proveedor->nombre ?? '') }}" 
                       placeholder="Nombre completo del proveedor" required maxlength="150">
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    NIT
                </label>
                <input type="text" 
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('nit') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                       id="nit" name="nit" value="{{ old('nit', $proveedor->nit ?? '') }}" 
                       placeholder="Número de NIT" maxlength="20">
                @error('nit')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tipo de Proveedor <span class="text-red-500">*</span>
                </label>
                <select class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('tipo') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                        id="tipo" name="tipo" required>
                    <option value="">Seleccionar tipo...</option>
                    <option value="empresa" {{ old('tipo', $proveedor->tipo ?? '') == 'empresa' ? 'selected' : '' }}>
                        Empresa
                    </option>
                    <option value="persona" {{ old('tipo', $proveedor->tipo ?? '') == 'persona' ? 'selected' : '' }}>
                        Persona Natural
                    </option>
                    <option value="cooperativa" {{ old('tipo', $proveedor->tipo ?? '') == 'cooperativa' ? 'selected' : '' }}>
                        Cooperativa
                    </option>
                </select>
                @error('tipo')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Categoría Principal <span class="text-red-500">*</span>
                </label>
                <select class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('categoria') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                        id="categoria" name="categoria" required>
                    <option value="">Seleccionar categoría...</option>
                    <option value="alimentos" {{ old('categoria', $proveedor->categoria ?? '') == 'alimentos' ? 'selected' : '' }}>
                        Alimentos para Peces
                    </option>
                    <option value="insumos" {{ old('categoria', $proveedor->categoria ?? '') == 'insumos' ? 'selected' : '' }}>
                        Insumos y Suministros
                    </option>
                    <option value="equipos" {{ old('categoria', $proveedor->categoria ?? '') == 'equipos' ? 'selected' : '' }}>
                        Equipos y Maquinaria
                    </option>
                    <option value="servicios" {{ old('categoria', $proveedor->categoria ?? '') == 'servicios' ? 'selected' : '' }}>
                        Servicios
                    </option>
                    <option value="medicamentos" {{ old('categoria', $proveedor->categoria ?? '') == 'medicamentos' ? 'selected' : '' }}>
                        Medicamentos y Químicos
                    </option>
                </select>
                @error('categoria')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Estado <span class="text-red-500">*</span>
                </label>
                <select class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('estado') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                        id="estado" name="estado" required>
                    <option value="activo" {{ old('estado', $proveedor->estado ?? 'activo') == 'activo' ? 'selected' : '' }}>
                        Activo
                    </option>
                    <option value="inactivo" {{ old('estado', $proveedor->estado ?? '') == 'inactivo' ? 'selected' : '' }}>
                        Inactivo
                    </option>
                    <option value="evaluacion" {{ old('estado', $proveedor->estado ?? '') == 'evaluacion' ? 'selected' : '' }}>
                        En Evaluación
                    </option>
                </select>
                @error('estado')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

{{-- Información de Contacto --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
            </svg>
            Información de Contacto
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label for="telefono_principal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Teléfono Principal <span class="text-red-500">*</span>
                </label>
                <input type="tel" 
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('telefono_principal') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                       id="telefono_principal" name="telefono_principal" value="{{ old('telefono_principal', $proveedor->telefono_principal ?? '') }}" 
                       placeholder="Ej: 2234-5678" required maxlength="20">
                @error('telefono_principal')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="telefono_secundario" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Teléfono Secundario
                </label>
                <input type="tel" 
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('telefono_secundario') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                       id="telefono_secundario" name="telefono_secundario" value="{{ old('telefono_secundario', $proveedor->telefono_secundario ?? '') }}" 
                       placeholder="Ej: 5678-9012" maxlength="20">
                @error('telefono_secundario')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Email
                </label>
                <input type="email" 
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('email') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                       id="email" name="email" value="{{ old('email', $proveedor->email ?? '') }}" 
                       placeholder="ejemplo@correo.com" maxlength="100">
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="lg:col-span-2">
                <label for="direccion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Dirección
                </label>
                <input type="text" 
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('direccion') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                       id="direccion" name="direccion" value="{{ old('direccion', $proveedor->direccion ?? '') }}" 
                       placeholder="Dirección completa" maxlength="255">
                @error('direccion')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sitio_web" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Sitio Web
                </label>
                <input type="url" 
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('sitio_web') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                       id="sitio_web" name="sitio_web" value="{{ old('sitio_web', $proveedor->sitio_web ?? '') }}" 
                       placeholder="https://www.ejemplo.com" maxlength="255">
                @error('sitio_web')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

{{-- Información Comercial --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Información Comercial
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <label for="moneda_preferida" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Moneda Preferida
                </label>
                <select class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('moneda_preferida') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                        id="moneda_preferida" name="moneda_preferida">
                    <option value="GTQ" {{ old('moneda_preferida', $proveedor->moneda_preferida ?? 'GTQ') == 'GTQ' ? 'selected' : '' }}>
                        GTQ (Quetzales)
                    </option>
                    <option value="USD" {{ old('moneda_preferida', $proveedor->moneda_preferida ?? '') == 'USD' ? 'selected' : '' }}>
                        USD (Dólares)
                    </option>
                </select>
                @error('moneda_preferida')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="forma_pago_preferida" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Forma de Pago Preferida
                </label>
                <select class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('forma_pago_preferida') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                        id="forma_pago_preferida" name="forma_pago_preferida">
                    <option value="contado" {{ old('forma_pago_preferida', $proveedor->forma_pago_preferida ?? 'contado') == 'contado' ? 'selected' : '' }}>
                        Contado
                    </option>
                    <option value="credito" {{ old('forma_pago_preferida', $proveedor->forma_pago_preferida ?? '') == 'credito' ? 'selected' : '' }}>
                        Crédito
                    </option>
                    <option value="mixto" {{ old('forma_pago_preferida', $proveedor->forma_pago_preferida ?? '') == 'mixto' ? 'selected' : '' }}>
                        Mixto
                    </option>
                </select>
                @error('forma_pago_preferida')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="dias_credito" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Días de Crédito
                </label>
                <input type="number" 
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('dias_credito') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                       id="dias_credito" name="dias_credito" value="{{ old('dias_credito', $proveedor->dias_credito ?? 0) }}" 
                       min="0" max="365" placeholder="0">
                @error('dias_credito')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="limite_credito" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Límite de Crédito
                </label>
                <input type="number" 
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('limite_credito') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                       id="limite_credito" name="limite_credito" value="{{ old('limite_credito', $proveedor->limite_credito ?? '') }}" 
                       min="0" step="0.01" placeholder="0.00">
                @error('limite_credito')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

{{-- Información Adicional --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Información Adicional
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <label for="especialidades" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Especialidades o Productos Principales
                </label>
                <textarea class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('especialidades') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                          id="especialidades" name="especialidades" rows="4" 
                          placeholder="Describe los productos o servicios principales que ofrece este proveedor...">{{ old('especialidades', $proveedor->especialidades ?? '') }}</textarea>
                @error('especialidades')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="notas" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Notas y Observaciones
                </label>
                <textarea class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 {{ $errors->has('notas') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500' }}" 
                          id="notas" name="notas" rows="4" 
                          placeholder="Cualquier información adicional relevante sobre el proveedor...">{{ old('notas', $proveedor->notas ?? '') }}</textarea>
                @error('notas')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-wrap items-center gap-6">
                <div class="flex items-center">
                    <input type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700" 
                           id="acepta_devoluciones" name="acepta_devoluciones" value="1"
                           {{ old('acepta_devoluciones', $proveedor->acepta_devoluciones ?? true) ? 'checked' : '' }}>
                    <label for="acepta_devoluciones" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Acepta devoluciones
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700" 
                           id="es_proveedor_preferido" name="es_proveedor_preferido" value="1"
                           {{ old('es_proveedor_preferido', $proveedor->es_proveedor_preferido ?? false) ? 'checked' : '' }}>
                    <label for="es_proveedor_preferido" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Proveedor preferido
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700" 
                           id="requiere_orden_compra" name="requiere_orden_compra" value="1"
                           {{ old('requiere_orden_compra', $proveedor->requiere_orden_compra ?? false) ? 'checked' : '' }}>
                    <label for="requiere_orden_compra" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Requiere orden de compra
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>