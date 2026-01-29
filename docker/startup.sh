#!/bin/sh

# 1. Instalar dependencias si no existen (Primera vez)
if [ ! -d "/var/www/vendor" ]; then
    echo "Vendor directory not found. Installing dependencies..."
    composer install --no-interaction --optimize-autoloader --no-dev
fi

# 2. Configurar entorno (.env)
if [ ! -f /var/www/.env ]; then
    echo "Creating .env file..."
    cp /var/www/.env.example /var/www/.env
fi

# Generar claves si no están configuradas
if ! grep -q "^APP_KEY=base64:" /var/www/.env; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

if ! grep -q "^JWT_SECRET=.\{10,\}" /var/www/.env; then
    echo "Generating JWT_SECRET..."
    php artisan jwt:secret --force
fi

# 3. Crear archivo de base de datos si no existe
if [ ! -f /var/www/database/database.sqlite ]; then
    echo "Creating database.sqlite..."
    touch /var/www/database/database.sqlite
fi

# 4. Arreglar permisos (Crítico para SQLite y Laravel Logs)
echo "Fixing permissions..."
chmod -R 777 /var/www/storage
chmod -R 777 /var/www/bootstrap/cache
chmod -R 777 /var/www/database

# 5. Ejecutar migraciones automáticamente
echo "Running migrations..."
php artisan migrate --force --seed

# 6. Iniciar Supervisor
echo "Starting Supervisor..."
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf