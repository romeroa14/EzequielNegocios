#!/bin/bash

# Crear directorios necesarios si no existen
mkdir -p storage/app/public/products
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p .laravel/cache

# Establecer permisos
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod -R 775 .laravel/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
chown -R www-data:www-data .laravel/cache

# Crear enlace simbólico
php artisan storage:link

# Limpiar y regenerar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones
php artisan migrate --force 