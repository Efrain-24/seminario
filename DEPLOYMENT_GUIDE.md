# 🚀 GUÍA DE DEPLOYMENT A HOSTINGER

## Archivos importantes para subir:
- seminario_backup.sql (Base de datos)
- .env.production (Configuración de producción)
- Todo el código del proyecto

## Pasos para deployment:

### 1. En Hostinger - Crear Base de Datos
1. Ve a hPanel > Bases de Datos MySQL
2. Crea una nueva base de datos: `u123456789_seminario`
3. Crea un usuario: `u123456789_user`
4. Asigna todos los permisos al usuario
5. Anota: nombre_bd, usuario, contraseña

### 2. Configurar .env en Hostinger
1. Sube el proyecto a public_html
2. Renombra `.env.production` a `.env`
3. Edita `.env` con los datos reales:
   - DB_DATABASE=tu_nombre_bd_real
   - DB_USERNAME=tu_usuario_real  
   - DB_PASSWORD=tu_password_real
   - APP_URL=https://tu-dominio.com
   - APP_KEY=genera_nueva_key

### 3. Configurar Document Root
1. En hPanel > Avanzado > Subdominios
2. O en Configuración del dominio
3. Cambiar document root de `/public_html` a `/public_html/public`

### 4. Importar Base de Datos
1. Ve a hPanel > phpMyAdmin
2. Selecciona tu base de datos
3. Importa el archivo `seminario_backup.sql`

### 5. Generar APP_KEY
```bash
php artisan key:generate
```

### 6. Ejecutar migraciones (si es necesario)
```bash
php artisan migrate --force
```

### 7. Configurar permisos
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 8. Optimizar para producción
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Estructura de archivos en Hostinger:
```
public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          ← Este debe ser el document root
│   ├── index.php
│   ├── build/
│   └── images/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
└── seminario_backup.sql
```

## Verificaciones finales:
- [ ] Base de datos importada correctamente
- [ ] .env configurado con datos reales
- [ ] Document root apuntando a /public
- [ ] Permisos de storage configurados
- [ ] APP_KEY generada
- [ ] Cachés optimizados

## Solución de problemas comunes:

### Error 500:
1. Verificar permisos de storage
2. Verificar configuración de .env
3. Revisar logs en storage/logs/

### Base de datos no conecta:
1. Verificar credenciales en .env
2. Verificar que el usuario tenga permisos
3. Verificar host (localhost o IP específica)

### Assets no cargan:
1. Verificar que npm run build se ejecutó
2. Verificar ruta en .env (APP_URL)
3. Verificar document root

### Migraciones fallan:
1. Verificar permisos de usuario en BD
2. Ejecutar migraciones una por una
3. Revisar logs de Laravel