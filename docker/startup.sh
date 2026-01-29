#!/bin/sh

# 1. Instalar dependencias si no existen (Primera vez)
if [ ! -d "/var/www/vendor" ]; then
    echo "Vendor directory not found. Installing dependencies..."
    composer install --no-interaction --optimize-autoloader --no-dev
fi

# 2. Crear archivo de base de datos si no existe
if [ ! -f /var/www/database/database.sqlite ]; then
    echo "Creating database.sqlite..."
    touch /var/www/database/database.sqlite
fi

# 2. Arreglar permisos (Crítico para SQLite y Laravel Logs)
echo "Fixing permissions..."
chmod -R 777 /var/www/storage
chmod -R 777 /var/www/bootstrap/cache
chmod -R 777 /var/www/database

# 3. Ejecutar migraciones automáticamente
echo "Running migrations..."
php artisan migrate --force --seed

# 4. Iniciar Supervisor
echo "Starting Supervisor..."
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf