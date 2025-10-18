# Descuento y Devolución de Peces en Inventario

## Descripción del Cambio

Se implementó la funcionalidad de:
1. **Descuento automático** del artículo "pez" en inventario cuando se **crea** un lote
2. **Devolución automática** del artículo "pez" al inventario cuando se **elimina** un lote

## Archivos Modificados

### `app/Http/Controllers/LoteController.php`

Se agregaron los siguientes cambios:

#### Imports Agregados:
- `App\Models\InventarioItem`
- `App\Models\InventarioMovimiento`
- `App\Models\InventarioExistencia`
- `Illuminate\Support\Facades\Log`
- `Illuminate\Support\Facades\Auth`

#### Método Modificado: `store()`

Se agregó un bloque `try-catch` después de crear el lote que llama al método privado `descontarPezDelInventario($lote)`:

```php
// Descontar el artículo "pez" del inventario
try {
    $this->descontarPezDelInventario($lote);
} catch (\Exception $e) {
    // Logging del error pero no impedir la creación del lote
    Log::warning("Error al descontar peces del inventario para el lote {$lote->codigo_lote}: " . $e->getMessage());
}
```

**Nota:** Si hay un error al descontar, el lote se crea igualmente (no fallará la operación), pero se registra un warning en los logs.

#### Nuevo Método Privado: `descontarPezDelInventario(Lote $lote)`

Este método:

1. **Busca el artículo "pez"** en la tabla `inventario_items`:
   - Busca por nombre (case-insensitive) o por SKU
   - Si no existe, solo registra un warning y retorna

2. **Crea un registro de movimiento** en `inventario_movimientos`:
   - Tipo: `salida`
   - Referencia: Vinculado al lote creado
   - Descripción: Referencia al código del lote
   - Usuario: El usuario autenticado

3. **Descontar el stock** de las existencias:
   - Itera sobre todas las bodegas con stock del artículo "pez"
   - Descuenta la cantidad según la cantidad inicial del lote
   - Si hay múltiples bodegas, descuenta proporcionalmente

4. **Registra la operación** en los logs (`Log::info()`)

#### Nuevo Método Privado: `devolverPezAlInventario(Lote $lote)`

Este método se ejecuta cuando se **elimina/desactiva** un lote y:

1. **Busca el artículo "pez"** en la tabla `inventario_items` (igual que al descontar)

2. **Verifica movimiento de descarga previo**:
   - Busca si existe un movimiento tipo `salida` para este lote
   - Si no existe, no hay nada que devolver (registra info y retorna)

3. **Crea un movimiento de devolución**:
   - Tipo: `entrada`
   - Descripción: "Devolución de peces por eliminación del lote..."
   - Referencia: Vinculado al mismo lote

4. **Suma el stock de peces**:
   - Si no hay existencias previas, crea una nueva con el stock devuelto
   - Si existen, suma a la primera existencia encontrada

5. **Registra la operación** en los logs (`Log::info()`)

## Flujo de Operación

### Al Crear un Lote (Descuento):
```
Usuario crea lote (1000 peces)
    ↓
✅ Lote guardado en BD
    ↓
🔍 Buscar artículo "pez"
    ↓
📝 Crear movimiento SALIDA (1000 unidades)
    ↓
📦 Descontar del inventario
    ↓
✅ Lote listo para usar
```

### Al Eliminar/Desactivar un Lote (Devolución):
```
Usuario desactiva lote (1000 peces)
    ↓
🔍 Verificar descarga previa
    ↓
📝 Crear movimiento ENTRADA (1000 unidades)
    ↓
📦 Sumar al inventario
    ↓
✅ Lote inactivo + Peces devueltos
```

## Requisitos Previos

- Debe existir un artículo en la tabla `inventario_items` con:
  - `nombre` = "pez" (o similar, búsqueda case-insensitive)
  - O `sku` = "pez"

- Debe haber existencias registradas en `inventario_existencias` para el artículo "pez"

## Manejo de Errores

- Si no existe el artículo "pez": Se registra un warning, pero el lote se crea/elimina normalmente
- Si hay error descargando stock: Se lanza un warning, pero el lote se crea normalmente
- Si hay error devolviendo peces: Se lanza un warning, pero el lote se desactiva normalmente
- La creación/eliminación del lote **nunca fallará** por problemas con el inventario

## Registros de Log

Buscar en `storage/logs/laravel.log`:

**Al Crear Lote:**
```
[2025-10-18] Se descargaron 1000 peces del inventario para el lote TIL-2025-001
```

**Al Eliminar Lote:**
```
[2025-10-18] Se devolvieron 1000 peces al inventario para el lote TIL-2025-001
```

**Errores:**
```
[2025-10-18] No se encontró el artículo 'pez' en el inventario para descontar en el lote TIL-2025-001
[2025-10-18] Error al descontar peces del inventario para el lote TIL-2025-001: [mensaje de error]
```

## Ejemplo de Uso

Al crear un lote con los siguientes datos:
- Código: `TIL-2025-001`
- Especie: `Tilapia Nilótica`
- Cantidad inicial: `1000` peces
- Fecha inicio: `2025-10-18`

Automáticamente:
1. Se crea el lote con estado `activo`
2. Se busca el artículo "pez" en inventario
3. Se descargan 1000 unidades de "pez" del inventario
4. Se registra un movimiento de salida vinculado al lote

**Si luego se desactiva el lote:**
1. Se verifica que haya movimiento de descarga previo
2. Se crean 1000 unidades de "pez" de vuelta al inventario
3. Se registra un movimiento de entrada vinculado al lote
4. El lote pasa a estado `inactivo`

## Futuras Mejoras (Opcionales)

- Permitir especificar qué artículo descontar (no solo "pez")
- Permitir seleccionar la bodega específica de descuento
- Validar stock mínimo antes de descontar
- Implementar transacciones para garantizar consistencia
- Agregar historial de movimientos por lote
