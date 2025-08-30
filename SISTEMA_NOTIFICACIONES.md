# Sistema de Notificaciones - Beyond Learning 🔔

## ¿Qué hemos implementado?

Hemos creado un **sistema completo de notificaciones** similar al de los teléfonos móviles, que se muestra en el navbar de la aplicación.

---

## Características Principales ✨

### 🎯 **Icono de Campana Inteligente**
- **Ubicación**: Navbar superior, junto al menú de usuario
- **Contador dinámico**: Muestra el número de notificaciones no leídas
- **Actualización automática**: Se actualiza cada 30 segundos

### 📱 **Panel Desplegable**
- **Tamaño**: 420px de ancho (más grande que antes)
- **Posición**: Desplegado hacia la izquierda para mejor visibilidad
- **Texto**: Letras más pequeñas y compactas
- **Scroll**: Soporte para múltiples notificaciones

### 🎨 **Tipos de Notificaciones**
- **🔴 Error**: Anomalías de producción críticas
- **🟡 Warning**: Alertas de inventario y productos por vencer  
- **🟢 Success**: Confirmaciones y acciones exitosas
- **🔵 Info**: Información general del sistema

---

## Funcionalidades del Panel 🛠️

### ✅ **Gestión de Notificaciones**
- **Marcar como leída**: Click en el ✓ azul
- **Ver detalles**: Click en el 🔗 verde (si hay enlace)
- **Eliminar**: Click en la ✕ roja
- **Marcar todas**: Botón "Marcar todas" en el header

### 🔄 **Interacciones**
- **Click en notificación**: Va directamente al enlace relacionado
- **Actualización manual**: Botón "Actualizar" en el footer
- **Cierre automático**: Click fuera del panel para cerrar

---

## Notificaciones Automáticas 🤖

### 📦 **Inventario**
- **Stock bajo**: Cuando un ítem está por debajo del stock mínimo
- **Productos vencidos**: Lotes que ya vencieron
- **Por vencer**: Productos que vencen pronto

### 🐟 **Producción**  
- **Anomalías detectadas**: Bajo rendimiento en lotes
- **Deficiencias alimentarias**: Problemas en FCR
- **Alertas de biomasa**: Problemas en peso estimado

---

## Comandos Disponibles 🚀

```bash
# Crear notificaciones de prueba
php artisan notificaciones:demo

# Limpiar y crear nuevas
php artisan notificaciones:demo --limpiar

# Ver rutas de la API
php artisan route:list | findstr notificaciones
```

---

## API Endpoints 🔌

- **GET** `/notificaciones` - Obtener notificaciones no leídas
- **GET** `/notificaciones/count` - Contar notificaciones
- **PATCH** `/notificaciones/{id}/marcar-leida` - Marcar una como leída  
- **POST** `/notificaciones/marcar-todas-leidas` - Marcar todas
- **DELETE** `/notificaciones/{id}` - Eliminar notificación

---

## Configuración y Personalización ⚙️

### 📝 **Base de Datos**
- **Tabla**: `notificaciones`
- **Campos principales**: tipo, titulo, mensaje, datos, icono, url, leida
- **Expiración**: Las notificaciones pueden tener fecha de vencimiento

### 🎨 **Estilos**
- **Responsive**: Se adapta a pantallas móviles
- **Dark mode**: Soporte completo para modo oscuro
- **Iconos**: Usa Lucide Icons para consistencia visual

### 🔧 **Extensibilidad**
```php
// Crear notificación personalizada
Notificacion::create([
    'tipo' => 'warning',
    'titulo' => 'Mi Alerta',
    'mensaje' => 'Descripción de la alerta',
    'datos' => ['custom_data' => 'value'],
    'icono' => 'alert-circle',
    'url' => route('mi.ruta'),
]);
```

---

## Estado Actual ✅

- ✅ **Sistema completo funcionando**
- ✅ **Panel responsive y atractivo**  
- ✅ **Notificaciones automáticas configuradas**
- ✅ **API REST funcional**
- ✅ **5 notificaciones de prueba creadas**
- ✅ **Integración con alertas existentes**

---

## Próximos Pasos 🚧

1. **Notificaciones push** (opcional)
2. **Sonidos de notificación** (opcional)  
3. **Filtros por tipo** (opcional)
4. **Historial completo** (opcional)

---

**🎉 ¡El sistema está listo para usar!** 

Revisa el icono de campana en el navbar para ver las notificaciones de prueba que se crearon.
