#!/bin/bash

# Script de deployment para Hostinger
echo "=== DEPLOYMENT SCRIPT PARA HOSTINGER ==="
echo "Autor: Sistema de Piscicultura"
echo "Fecha: $(date)"
echo

echo "1. Limpiando cachés de Laravel..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "2. Instalando dependencias de producción..."
composer install --optimize-autoloader --no-dev

echo "3. Compilando assets..."
npm run build

echo "4. Configurando permisos..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

echo "5. Ejecutando migraciones..."
php artisan migrate --force

echo "6. Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== DEPLOYMENT COMPLETADO ==="
echo "No olvides:"
echo "- Configurar el .env con los datos de Hostinger"
echo "- Importar la base de datos"
echo "- Configurar el document root hacia /public"
echo