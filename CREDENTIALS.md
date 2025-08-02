# 🔐 Credenciales de Acceso - Sistema Piscícola

## ✅ Error 419 Solucionado

El error 419 (Page Expired) ha sido corregido mediante:
- Configuración de `SESSION_DRIVER=file` en lugar de database
- Actualización de `APP_URL=http://localhost:8001`
- Limpieza de cachés de configuración

## 👥 Usuarios Disponibles

Después de ejecutar `php artisan db:seed`, puedes usar estas credenciales:

| Usuario | Email | Contraseña | Rol | Permisos |
|---------|-------|------------|-----|----------|
| **Test User** | test@example.com | password | admin | Acceso completo |
| **Administrador** | admin@piscicultura.com | admin123 | admin | Acceso completo |
| **Manager** | manager@piscicultura.com | manager123 | manager | Gestión y reportes |
| **Empleado** | empleado@piscicultura.com | empleado123 | empleado | Operaciones básicas |

## 🚀 Instrucciones de Uso

1. **Iniciar servidor:**
   ```bash
   php artisan serve --port=8001
   ```

2. **Acceder al sistema:**
   - URL: http://localhost:8001/login
   - Usar cualquiera de las credenciales de arriba

3. **Ejecutar seeders (si es necesario):**
   ```bash
   php artisan db:seed
   ```

4. **Verificar usuarios:**
   ```bash
   php artisan users:check
   ```

## ⚙️ Configuración Requerida

Asegúrate de que tu archivo `.env` tenga:
```env
SESSION_DRIVER=file
APP_URL=http://localhost:8001
```

## 🔧 Comandos Útiles

```bash
# Limpiar cachés
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Verificar migraciones
php artisan migrate:status

# Ejecutar migraciones
php artisan migrate
```

---
✅ **Sistema funcionando correctamente - Error 419 resuelto**
